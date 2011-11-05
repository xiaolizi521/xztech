#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      provisioning.pm
#
# Overview:
#   Perl module that contains functions that are needed during the provisioning process.  Uses
#   the Database.pm module for all database connections.  For the most part, all functions
#   should use logks for logging as these functions are for the provisioning of a server.
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::Provisioning;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw();
    %EXPORT_TAGS = ( 'all' => [ qw(
        provcheck 
        fetch_licenses
        fetch_postconf 
        get_ks_list 
        hwcheck 
        get_mac_by_ip 
        new_get_mac_by_ip 
        get_mac_from_log
        isValidOS
        isValidPanel   
        update_ks
    ) ]);
    # @EXPORT_OK = qw();
    @EXPORT_OK = ( @{ $EXPORT_TAGS{'all'} } );


}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use Net::Ping ();
use File::Type;
use MIME::Base64;

#############################
#    Serverbeach modules    #
#############################
use SB::Database;
use SB::Config;
use SB::Logger;
use SB::Common ":all";

$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

# ===============================================================================================
# Package scoped variable definitions
# ===============================================================================================


# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
#   provcheck()
#
#   parameters
#       $dbh : open handle to the database (optional)
#
#   return value
#       0   :   Provisioning off
#       1   :   Provisioning on
#
#------------------------------------------------------------------------------------

sub provcheck 
{
    my $needConnect = 0;
    my $dbh = shift();
    if (! $dbh)
    {
        $needConnect = 1;
        $dbh = ks_dbConnect();
        return undef if (!$dbh); 
    }
    my $return = $dbh->selectall_arrayref("SELECT CASE WHEN value = 'on' THEN 1 ELSE 0 END FROM misc WHERE param = 'status'")->[0]->[0];
    $dbh->disconnect() if ($needConnect == 1);
    return $return;
}



#------------------------------------------------------------------------------------
# fetch_licenses()
#
#   parameters
#       $macaddr : macaddr of server that needs the license
#
#   return value
#       success : .tgz license file
#       failure : undef
#
#------------------------------------------------------------------------------------

sub fetch_licenses
{
    # Get the mac address
    my $macaddr = shift();
    if (!$macaddr)
    {
        logks("ERR","fetch_licenses(): No mac address supplied");
        return undef;
    }

    # Get the file data from pit via license_server.php
    my $result = lwpfetch(sprintf("%s/license_server.php", $Config->{pit_baseurl}),{ macaddr => $macaddr },undef);

    # Not sure what this is here for, commenting out for now
    # print ("$result->[0], $result->[1]");
    
    if ( ($result->[0])  && (length($result->[1]) > 0) ) 
    {
        # This means we got something back
        my $tarfile = $result->[1];
        my $tgzfile;

        my $ft = File::Type->new();

        # Check to see what the filetype returned was
        my $type_from_data = $ft->checktype_contents($tarfile);

        # If the filetype is already gzip'd, we are done
        if ($type_from_data eq "application/x-gzip")  
        {
            $tgzfile = $tarfile;
        }
        # If it is just tar'd we need to gzip it
        elsif ($type_from_data eq "application/x-gtar") 
        {
            $tgzfile = Compress::Zlib::memGzip($tarfile);
        }
        # That's all of the file types that we know how to handle
        else 
        { 
            logks("ERR","$macaddr fetch_liceses(): Invalid filetype returned from license_server.php");
            return undef; 
        }

        my $encoded;
        $encoded = MIME::Base64::encode($tgzfile);
        return $encoded;
    }
    else 
    {
        # If no license was returned from license_server.php
        return undef;
    }
}


#------------------------------------------------------------------------------------
#   fetch_postconf   
#
#   parameters
#       $macaddr : mac address of the serverthat we need to postconf for
#
#   return value
#       success : hash reference containing the postconf info
#       failure : undef
#
#------------------------------------------------------------------------------------

sub fetch_postconf 
{
    my ($macaddr, $custNum, $servNum) = @_;
    my $postinfo = {};
    my @required = qw(ASPNET BPASS DNS1 DNS2 DOMAIN GATEWAY HOST IPADDR NETMASK PPASS PUSER RPASS VLAN WEBPORT customer_number server_number);

    my $return = {};

    if ($macaddr) 
    {
        $postinfo = { macaddr => $macaddr };
    }
    # Q: Why did I do this?
    # A: When linking servers, the MAC address may not be
    # linked to the customer when we request postconf info
    elsif ($custNum && $servNum) 
    {
        $postinfo = { custNum => $custNum, servNum => $servNum };
    }
    else 
    {
        logks("ERR", "fetch_postconf(): no information provided");
        return undef;
    }

    my $result = lwpfetch(sprintf("%s/provisioneer.php", $Config->{pit_baseurl}),$postinfo, undef, undef);
    if ($result->[0]) 
    {
        my @pairs = split(/(\n|\r\n)/, $result->[1]);
        foreach my $pair (@pairs) 
        {
            chomp($pair);
            $pair =~ /^(.*)=(.*)$/;
            my $param = $1;
            my $value = $2;
            next unless (($param) && ($param =~ /\w+/));
            $return->{$param} = $value;
        }
    }
    else 
    {
        logks("ERR", "$macaddr fetch_postconf(): failed to fetch postconf (".$result->[1].")");
        return undef;
    }

    # verify that required fields are present
    foreach (@required) 
    {
        my $missingFields = "";
        # Can't test if $return->{$_} is true because it may be a 0
        if ( ! ( defined($return->{$_}) && ($return->{$_} ne "none") ) )
        {
            $missingFields .= " $_";
        }
        if ($missingFields ne "")
        {
            logks("NOTICE", "$macaddr postconf missing field(s): $missingFields");
            return undef;
        }
    }
    return $return;
}


#------------------------------------------------------------------------------------
#   get_ks_list   
#
#   parameters
#       $dbh    : handle to the database
#
#   return value
#       success : array containing list of operating systems in the database
#       failure : undef
#
#------------------------------------------------------------------------------------
sub get_ks_list {

    my $needConnect = 0;
    my $dbh = shift();
    if (! $dbh)
    {
        $needConnect = 1;
        $dbh = ks_dbConnect();
        return undef if (!$dbh); 
    }
    my @return;
    my $qry1 = "SELECT osload FROM os_list WHERE is_ks='t'";
    my $sth1 = $dbh->prepare($qry1);
    $sth1->execute();
    while (my @row = $sth1->fetchrow_array()) 
    {
        push @return, $row[0];
    }
    $sth1->finish();
    $dbh->disconnect() if ($needConnect == 1);
    return @return;
}



#------------------------------------------------------------------------------------
#   hwcheck()
#
#   parameters
#       $dbh    : handle to the database
#
#   return value
#       $count      :   number of times cpu_model is listed in the database?
#       $cpu_models :   various cpu models
#
#   Overview
#       This is a legacy piece of code that I am not exactly sure is accurate or even if
#       it is used.
#
#------------------------------------------------------------------------------------
sub hwcheck {

    my $dbh;
    $dbh = shift() || ($dbh = ks_dbConnect());
    return undef if (!$dbh); 
    my @cpu_models;
    my $count = {};

    my $result1 = $dbh->selectall_arrayref("SELECT part_name FROM hardware_list WHERE part_type = 'cpu_model'");
    foreach (@$result1) {
        push @cpu_models, "$_->[0]";
        if ($_->[0] =~ /mp (2600|2800)/) {
            $count->{"$_->[0] 1"} = 0;
            $count->{"$_->[0] 2"} = 0;
        }
        else {
            $count->{$_->[0]} = 0;
        }
    }
    #print join("\n", @cpu_models)."\n\n";

    my $servers_sth = $dbh->prepare("SELECT mac_address, t2.value AS cpu0_model, t3.value AS mem FROM kickstart_map t1, hardware t2, hardware t3 WHERE t1.new_status = 'ready' AND t2.mac_list_id = t1.mac_list_id AND t3.mac_list_id = t1.mac_list_id AND t2.param = 'cpu0_model' AND t2.value = ? AND t3.param = 'mem'");

    foreach (@cpu_models) {
        my $servers = $dbh->selectall_arrayref($servers_sth, undef, $_);
        foreach my $row (@$servers) {
            my ($mac, $cpu, $mem) = @$row;

            if ($cpu =~ /mp (2600|2800)/) {
                if ($mem < 1000000000) { # Less than 1GB, invalid config
                    $count->{unknown} += 1;
                }
                elsif ($mem > 2000000000) { # 2GB
                    $count->{"$cpu 2"} += 1;
                    #print "$mac $cpu 2 $mem ".$count->{"$cpu 2"}."\n";
                }
                else { # Anything left should be 1GB
                    $count->{"$cpu 1"} += 1;
                    #print "$mac $cpu 1 $mem ".$count->{"$cpu 1"}."\n";
                }
            }
            else {
                $count->{$row->[1]} += 1;
            }
        }
    }
    
    $servers_sth->finish();

    @cpu_models = grep(!/amd athlon\(tm\) mp (2600|2800)/, @cpu_models);
    push @cpu_models, "amd athlon(tm) mp 2600 1";
    push @cpu_models, "amd athlon(tm) mp 2600 2";
    push @cpu_models, "amd athlon(tm) mp 2800 1";
    push @cpu_models, "amd athlon(tm) mp 2800 2";
    push @cpu_models, "unknown";
    $count->{unknown} += $dbh->selectall_arrayref("SELECT count(mac_address) FROM kickstart_map t1, hardware t2 WHERE t1.new_status = 'ready' AND t2.mac_list_id = t1.mac_list_id AND t2.param = 'cpu0_model' AND t2.value NOT IN ( SELECT part_name FROM hardware_list WHERE part_type = 'cpu_model')")->[0]->[0];

    return $count, @cpu_models;
}


#------------------------------------------------------------------------------------
# get_mac_by_ip()
#
#   parameters
#       $dbh    :   open database handle
#       $ipaddr :   ip address that we wish to get the mac address for
#
#   return value
#       success :   mac address of the server assigned the particular IP
#       failure :   undef        
#
#   Overview
#       Used to get the mac address of a server assigned a particular IP address
#
#------------------------------------------------------------------------------------
sub get_mac_by_ip 
{
    my ($dbh, $ipaddr) = @_;
    my $return;
    (($dbh) && ($ipaddr)) || return undef;

    my $result = $dbh->selectall_arrayref("SELECT mac_address FROM kickstart_map WHERE ip_address = ? ORDER BY last_update DESC LIMIT 1", undef, $ipaddr);
    if (my $row = $result->[0]) 
    {
        $return = $row->[0];
    }
 
        return $return;
}


#------------------------------------------------------------------------------------
# new_get_mac_by_ip()
#
#   parameters
#       $dbh    :   open database handle
#       $ipaddr :   ip address that we wish to get the mac address for
#
#   return value
#       success :   mac address of the server assigned the particular IP
#       failure :   undef        
#   Overview
#       Used to get the mac address of a server assigned a particular IP address
#       (same as above with updated query)
#
#------------------------------------------------------------------------------------

sub new_get_mac_by_ip 
{
    my ($dbh, $ipaddr) = @_;
    my $return;
    (($dbh) && ($ipaddr)) || return undef;

    my $result = $dbh->selectall_arrayref("SELECT t2.mac_address FROM xref_macid_ipaddr t1, mac_list t2 WHERE t1.ip_address = ? AND t1.mac_list_id = t2.id ORDER BY t2.date_added DESC LIMIT 1", undef, $ipaddr);

    if (my $row = $result->[0]) 
    {
        $return = $row->[0];
    }

    return $return;
}

#------------------------------------------------------------------------------------
# get_mac_from_log()
#
#   parameters
#       $ipaddr :   ip address that we wish to get the mac address for
#
#   return value
#       success :   mac address of the server assigned the particular IP
#       failure :   undef        
#
#   Overview
#       Used to get the mac address of a server assigned a particular IP address from
#       /exports/kickstart/logs/daemon.log
#
#------------------------------------------------------------------------------------

sub get_mac_from_log
{
    my ($ipaddr) = @_;
    ($ipaddr) || return undef;
    my $return;

    # This should return the last dhcp acknowledgment for the IP in question from the daemon.log
    my $DHCPACK = `grep DHCPACK /exports/kickstart/logs/daemon.log | grep $ipaddr | tail -1`;

    $DHCPACK =~ /(^.*DHCPACK on $ipaddr to )(([0-9A-F]{2}:){5}[0-9A-F]{2})( via .*)/i ;
    $return = $2;

    return $return;
}


#------------------------------------------------------------------------------------
# isValidOS
#
#   parameters
#       $dbh    :   open database handle
#       $osload :   osvalue to check for in database
#
#   return value
#       success :   1
#       failure :   0
#
#   Overview
#       Checks database to see if the osload value we got is valid.  There are better 
#       methods to do this, but this is being left in place to support code until we can
#       get such a method in place
#
#------------------------------------------------------------------------------------


sub isValidOS 
{
        my ($dbh, $osload) = @_;

        my $result = $dbh->selectall_arrayref("SELECT id FROM os_list WHERE osload = ? ", undef, $osload);
        if ($result->[0]) { return 1; }
        else { return 0; }
}


#------------------------------------------------------------------------------------
# isValidPanel
#
#   parameters
#       $panel  :   name of panel to seach for
#
#   return value
#       success :   1
#       failure :   0
#
#   Overview
#       Good idea   :   Ensure that the panel is valid
#       Bad idea    :   Check the panel name against a freaking huge list of valid panel names
#                       in a library function during the provisioning process after we already
#                       had to hard code the same panel name in ocean in Computer.php
#
#------------------------------------------------------------------------------------
sub isValidPanel {
    my $panel = shift();

    # This should be somewhere else, but until then, we are at least splitting the lines up
    # based on the panel type so it is not all one huge line
    my @validPanels = qw(none);
    push @validPanels, qw(cpanel);
    push @validPanels, qw(ensim35 ensim36 ensim40 ensim40win ensimProXwin100 ensimProXwinUnl ensimProXlin100 ensimProXlinUnl);
    push @validPanels, qw(helm31 helm32 helm4);
    push @validPanels, qw(plesk7 plesk7r plesk75 plesk75r plesk8 plesk81 plesk82 plesk83 plesk9);

    my $result = grep(/^$panel$/, @validPanels);

    return $result;
}



#------------------------------------------------------------------------------------
# update_ks
#
#   parameters
#       $macaddr    :   mac address of server to update
#       $osload     :   os we want to install
#       $dbh        :   open database handle (optional)
#
#   return value
#       success     :   1
#       failure     :   0
#
#   Overview
#       This is used to validate the provisioning of a server.  This includes checking
#       the current status of the server, checking the requested osload value, etc.
#       If everything checks out okay, it sets the status of the server in the database
#       to updateks.  Otherwise the status is set to updateks_fail and provisioning
#       will not continue
#
#------------------------------------------------------------------------------------
sub update_ks 
{
        my ($macaddr, $osload, $dbh) = @_;
        my $needConnect = 0;
        if (!$dbh)
        {
            $dbh = ks_dbConnect();
            $needConnect = 1;
        }
        return undef if (!$dbh); 
        my $return = 0;

        # make sure the OS load we are requesting exists
        if (!isValidOS($dbh, $osload)) {
                logks("info", "$macaddr $osload is invalid");
                return 0;
        }

        my @installs = get_ks_list($dbh);
        my $macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);

        if ($macobj->status() eq "new") {
                if (grep(/^$osload$/, @installs)) {
                        $dbh->disconnect() if ($needConnect == 1);
                        logks("info", "$macaddr is new or retired");
                        return 0;
                }
        }

        $macobj->osload($osload);
        my $target = sprintf "%s/%s", $Config->{ks_pxeconf}, $macobj->pxe();
        if (! -f $target) {
                logks("info", "$macaddr $target does not exist");
                return 0;
        }

        $macobj->update();

        my $newstatus;
        my $errors = $macobj->error();
        if (scalar(@{$errors}) > 0) {
                $newstatus = "updateks_fail";
                logks("INFO", "$macaddr STATUS -> updateks_fail");
                logks("ERR", "$macaddr errors: ".join(" : ", @{$errors}));
                $return = 0;
        }
        else {
                logks("info", "$macaddr STATUS -> updateks");
                $newstatus = "updateks";
                $return = 1;
        }

        $macobj->status($newstatus);
        $macobj->update();
        $dbh->disconnect() if ($needConnect == 1);

        return $return;
}


#------------------------------------------------------------------------------------
# adm_online
#
#   parameters
#       $dbh    :   open database handle
#
#   return value
#       whatever the database returns
#
#   Overview
#       This function returns the number of servers that are not customer 4 servers
#       that are marked as online in the admin database.  This cannot actually run from
#       the kickstart server since it is not allowed to connect to the admin server but
#       it has been kept around for completeness
#
#------------------------------------------------------------------------------------
sub adm_online 
{
        my $dbh = shift();
        ($dbh) || ($dbh = adm_ksConnect());
        return undef if not ($dbh);

    # 8 9 10 20 22
        my $qry1 = "SELECT count(t1.id)
        FROM customer_products t1,xref_products_product_type t2
        WHERE t1.products_id = t2.products_id
        AND t1.customers_id != 4
        AND t1.products_status_id = 8
        AND t2.product_type_id = 1";
        my $sth1 = $dbh->prepare($qry1);
        $sth1->execute();
        my @row = $sth1->fetchrow_array();
        $sth1->finish();

        return $row[0];
}




1;
