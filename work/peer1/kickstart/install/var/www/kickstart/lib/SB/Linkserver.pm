#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      Linkserver.pm
#
# Overview:
#   Provides access to functions required in the linkserver process
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::Linkserver;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw();
    %EXPORT_TAGS = ( 'all' => [ qw(
        linkServer
        macFinder
        getCustomerProductIp 
    ) ]);
    # @EXPORT_OK = qw();
    @EXPORT_OK = ( @{ $EXPORT_TAGS{'all'} } );


}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use Sys::Syslog qw(:DEFAULT setlogsock);


#############################
#    Serverbeach modules    #
#############################
use SB::Common ":all";
use SB::Config;

$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# linkServer() 
#
#   parameters
#       $input  :   hash reference containing all of the required data
#                       (customerId customerProductIdnum dc_abbr switch port macaddr message)
#
#   return value
#       hash array reference containing two values
#           [0] :   success state
#           [1] :   success/error message
#
#   Overview
#       This function is a frontend to post link state information to the admin server
#       
#------------------------------------------------------------------------------------

sub linkServer {
    my $input = shift();

    # Check for all of the required arguements
    my @required = qw(customerId customerProductIdnum dc_abbr switch port macaddr message);
    foreach (@required) 
    {
        next if ( ($input->{$_}) && ($input->{$_} ne "") );
        # If we got here, then a required value is missing.  We want to log the macaddr if 
        # that was not the missing value
        if (defined $input->{macaddr})
        {
            return [ 0, "$input->{macaddr} link_server() Missing info: $_" ];
        }
        else
        {
            return [ 0, "link_server() Missing info: $_" ];
        }
    }

    # setup the information for posting
    my @linkdata;
    push(@linkdata, "action=NetworkManualMacAddressMap");
    foreach (qw(customerId customerProductIdnum dc_abbr switch port message)) 
    {
        push(@linkdata, $_."=".$input->{$_});
    }
    push(@linkdata, "macAddress=".$input->{macaddr});

    # make the call 
    my $result = lwpfetch($Config->{ocean_baseurl}."/index.php", join("&", @linkdata), undef, undef);

    logks('INFO', "$input->{macaddr} linkServer() result: ".$result->[0]);

    if ($result->[0] or $result->[1] =~ /302 Found/) 
    {
        logks('INFO', "$input->{macaddr} linkServer() HTTP result: ".$result->[1]);
        return [ 1, "Success"];
    }
    else 
    { 
        logks('WARNING', "$input->{macaddr} linkServer() failed: ".$result->[0]);
        return [ 0, "link_server() Post failed: ".$result->[1] ];
    }

}



#------------------------------------------------------------------------------------
# macFinder
#
#   parameters
#       $macAddress :   mac address of the server in question
#
#   return value
#       hash reference containing information about location of server (if found)
#           hash keys : dc_abbr, switch_name. switch_port
#
#       returns undefined on error
#
#   Overview
#       When passed a mac address, this function calls the macfinder.cgi script on the
#       netadmin server to get the location
#
#------------------------------------------------------------------------------------

sub macFinder {
    
    my $macAddress = shift();
    my $return = {};

    if (!$macAddress)
    {
        logks("ERR","macFinder(): missing mac address to find");
        return $return;
    }

    # call the macfinder cgi
    my $lwpResult = lwpfetch(
        $Config->{net_baseurl}."/cgi-bin/macfinder.cgi",
        { dc_abbr => $Config->{dc_abbr}, macaddr => $macAddress }
        );

    # get the result
    if ($lwpResult->[0]) 
    {
        chomp($lwpResult->[1]);
        my @pairs = split(/,/, $lwpResult->[1]);
        $return = {};
        # shove all of the returned info into a hash
        foreach (@pairs) 
        {
            my ($n, $v) = split(/=/, $_);
            $return->{$n} = $v;
        }
    }

    # check to see if we got all of the required values
    if ( ! ( exists($return->{dc_abbr}) && exists($return->{switch_name}) && exists($return->{switch_port}) ) )
    {
        logks("WARNING","$macAddress macFinder(): No results returned from macfinder.cgi");
    }
    return $return->{dc_abbr}, $return->{switch_name}, $return->{switch_port};
}



#------------------------------------------------------------------------------------
# getCustomerProductIP 
#
#   parameters
#       $customerId             :   customer number
#       $customerPoductIdnum    :   customer's server number   
#
#   return value
#       success :   ipaddress assigned to the customer
#       failure :   undef
#
#   Overview
#
#------------------------------------------------------------------------------------

sub getCustomerProductIp 
{
    my ($customerId, $customerProductIdnum) = @_;
    my $return;

    if ( (!$customerId) || (!$customerProductIdnum) )
    {
        logks ("ERR","getCustomerProductIp(): missing customer information");
        return $return;
    }

    my $result = lwpfetch($Config->{pit_baseurl}."/getCustomerProductIp.php",
        "customerId=$customerId&customerProductIdnum=$customerProductIdnum");
    if ($result->[0]) 
    {
        $result->[1] =~ /^ipaddr=(.*),status=success$/i;
        $return = untaint("ipaddr", $1);
    }

    if ( ! $return )
    {
        logks("WARNING","getCustomerProductIp(): No IP address found for server $customerId-$customerProductIdnum");
    }
    return $return;
}


1
