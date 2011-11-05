#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      RapidReboot.pm
#
# Overview:
#   Perl library that provides access to rapid reboot functionality
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::RapidReboot;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw(rebootByMac RapidReboot);
    %EXPORT_TAGS = ();
    @EXPORT_OK = qw();

}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use Sys::Syslog qw(:DEFAULT setlogsock);
use Net::Ping ();
use POSIX ":sys_wait_h";
use IO::Socket::INET;

#############################
#    Serverbeach modules    #
#############################
use SB::Logger;
use SB::Config;
use SB::Database;
use SB::Switch qw(switchPortInfo portstate);
use SB::Common ":all";
use SB::MACFun;

$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};


# ===============================================================================================
# package scoped variables
# ===============================================================================================
my $ksdbh;

# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# rebootByMac()
#
#   parameters
#       $macaddr    :   mac address of server in question
#       $RRonly     :   true or false, RRonly status
#       $action
#
#   return value
#       success     :   1
#       failure     :   0
#
#   Overview
#       This function is used to reboot a server in the current DC based upon 
#       the mac address supplied.  First an attempt is made to softboot the server 
#       if it is on a private network.  If the server is not on a private network or if
#       the softboot fails, a call is made to RapidReboot() to send a reboot signal
#
#------------------------------------------------------------------------------------

sub rebootByMac 
{
    my ($macaddr, $RRonly, $action) = @_;
    my $result = 0;
    my $error = 0;

    $macaddr = untaint('macaddr', $macaddr);

    # Just quit now if we didn't get a mac address
    if (!$macaddr) 
    {
        logks("ERR","rebootByMac(): Invalid or no MAC Address supplied");
        return $result;
    }

    if ( (!$ksdbh) || ( !$ksdbh->ping() ) )
    {
        $ksdbh = ks_dbConnect();
    }

    return $result if (!$ksdbh);
    # Create the MACFun object
    my $mobj = MACFun->new(dbh => $ksdbh, macaddr => $macaddr);
    my $ipaddr = $mobj->ipaddr();
    my $status = $mobj->status();

    # Get the switch port information
    my $switchPortInfo = switchPortInfo($macaddr);

    # Did we get a something back from switchPortInfo call?
    if ($switchPortInfo)
    {   
        # If so, check to see if we are in the correct DC.  If so, call portstate to ensure that the
        # port is turned on
        if ($switchPortInfo->{dc_abbr} eq $Config->{dc_abbr}) 
        {
            portstate($switchPortInfo, "on");
        }
        else
        {
            logks("info", "$macaddr is not in this DC ($switchPortInfo->{dc_abbr} ne $Config->{dc_abbr})");
            $error = 1;
        }
    }

    # If the server is on the private network we want to try and do a soft reboot first
    # instead of a hard reboot.
    if ($ipaddr =~ /^(192|10)\./ && !$RRonly && !$error) 
    {
        # Update the status
        if (_UpdateRRStatus($macaddr, 2))
        {
            # Attempt to reboot
            $result = _softboot($ipaddr);

            # Check for success
            if ($result) 
            {
                logks('info', "$macaddr softboot -> SUCCESS");

                # if successful, update the status
                if (!_UpdateRRStatus($macaddr, 4))
                {
                    logks("ERR","$macaddr rebootByMac(): failed to set rapid reboot status = 4");
                    $error=1;
                }
            } 
            else 
            {
                # log the failure
                logks('info', "$macaddr softboot -> FAILURE");
            }
        }
        else
        {
            logks("ERR","$macaddr rebootByMac(): failed to set rapid reboot status = 2");
            $error=1;
        }
    }

    # This is to send the signal out to have the server rebooted.  If the softboot succeeded,
    # $result would be set and this would get skipped.  If we couldn't update the database, 
    # or if the server is not in this DC the error flag should be set and we 
    # need to skip this.  Continuing to process after an error is just asking for trouble
    if (!$result && !$error) 
    {
        if (_UpdateRRStatus($macaddr, 3))
        {
            # try to reboot server
            $result = RapidReboot($switchPortInfo, $action);
            if (!_UpdateRRStatus($macaddr, 4))
            {
                logks("ERR","$macaddr rebootByMac(): failed to update rapid reboot status = 4");
                $error = 1;
            } 
        }
        else
        {
            logks("ERR","$macaddr rebootByMac(): failed to update rapid reboot status = 3");
            $error = 1;
        }   

        if ($result && $result == 2 && !$error) 
        {
            logks('info', "$macaddr RapidReboot -> SUCCESS");
        }
        else 
        {
            logks('NOTICE', "$macaddr RapidReboot -> FAILURE");
            # A return of 1 from RapidReboot is a failure, but due to the lack to a
            # consistent error tracking system a 1 is a success from softboot.  We need
            # to set $result to 0 to ensure the proper behaviour below.
            $result = 0;
        }
    }
    else 
    {
        logks('NOTICE', "$macaddr RapidReboot -> FAILURE");
        $result = 0;
    }

    # Set the new status of the mac object based on whether the reboot was successful
    # and what the current status is set to
    if ($result)
    {
        if ($status =~ /kickstarted|online.*/) 
        {
            $mobj->status("online_reboot");
        } 
        else 
        {
            $mobj->status("reboot");
        }
    } 
    else 
    {
        if ($status =~ /kickstarted|online.*/) 
        {
            $mobj->status("online_reboot_fail");
        } 
        else 
        {
            $mobj->status("reboot_fail");
        }
    }

    $mobj->update();
    $ksdbh->disconnect();
    undef($ksdbh);
    return $result;
}


#------------------------------------------------------------------------------------
# _UpdateRRStatus()
#
#   parameters
#       $macaddr    :   MAC address of the server in question
#       $status     :   status to set rapid reboot to   
#       $ksdbh        :   open database handle (optional)
#
#   return value
#       success     :   1
#       failure     :   undef
#
#   Overview
#       Update the rapid reboot status of a server in the rapid reboot queue
#
#------------------------------------------------------------------------------------

sub _UpdateRRStatus {

    my ($macaddr, $status, $localConnect, $error, $db_action, $result);
    my $debug = 0;

    ($macaddr, $status) = @_;
       
    ($ksdbh) || ($ksdbh = ks_dbConnect());

    # This query is just used to see if we need to insert or update
    my $sql = $ksdbh->prepare("SELECT status
                                     FROM rapid_reboot_queue
                                    WHERE mac_address = ?
                                      AND active='t'");

    $db_action = "SELECT";
    $sql->execute($macaddr);

    # Here's a novel concept, lets check and see if there was an error
    if ($sql->err)
    {
        logks("ERR", "$macaddr UpdateRRStatus(): database $db_action failed: ". DBI::errstr);
        $error=1;
    }
    else
    {
        $result = $sql->fetchall_arrayref();
    }

    # Build the insert/update statements based on the result of the query above
    if ($result->[0]) 
    {
        $sql = $ksdbh->prepare("UPDATE rapid_reboot_queue
                    SET status = ?, last_updated = now()
                    WHERE mac_address = ?");
        $db_action = "UPDATE";
    } 
    else 
    {
        $sql = $ksdbh->prepare("INSERT INTO rapid_reboot_queue
                    (status, mac_address, started, last_updated)
                    VALUES (?, ?, now(), now())");
        $db_action = "INSERT";
    }

    $sql->execute($status,$macaddr);

    if ($sql->err)
    {
        logks("ERR", "$macaddr UpdateRRStatus(): database $db_action failed: ".DBI::errstr);
        $error=1;
    }

    return undef if ($error);

    if ($debug)
    {
        logks("INFO", "$macaddr RapidReboot Status successfully set to $status");
    }

    return 1;
    
}


#------------------------------------------------------------------------------------
# RapidReboot()
#
#   parameters
#       $switchPortInfo :   info about switch location
#       $action         :   action to take
#
#   return value
#       error           :   0
#       failure         :   1
#       success         :   2
#
#   Overview
#       This function is called to talk to the reboot server to reboot a particular
#       server on the rack
#
#------------------------------------------------------------------------------------

sub RapidReboot {
    my ($switchPortInfo, $action) = @_;
    if (!defined($action)) { $action = "cycle" };

    my $RRserver = untaint("ipaddr", $switchPortInfo->{reboot_server});

    # Make sure we have the required information
    foreach (qw(reboot_server serial_port board_address board_port)) 
    {
        if (!defined($switchPortInfo->{$_})) 
        {
            logks("NOTICE", "RapidReboot() missing param: $_");
            return 0;
        }
    }

    ($ksdbh) || ($ksdbh = ks_dbConnect());

    my $RRstring = sprintf("%s:%s:%s-%s",
        $switchPortInfo->{serial_port},
        $switchPortInfo->{board_address},
        $switchPortInfo->{board_port},
        $action);

    logks("info", "RapidReboot() sent ".$RRstring." to ".$RRserver);

    # open a connection to the reboot server
    my $socket = IO::Socket::INET->new(
                'PeerAddr' => $RRserver,
                'PeerPort' => 2250,
                'Proto' => 'tcp') or return 1;

    # send the reboot string
    print $socket $RRstring."\n";

    # get the result
    my $result = <$socket>;

    # close down the socket
    close($socket);

    logks("info", "RapidReboot() got ".$result." from ".$RRserver);

    my $return = 0;
    if (!$result) 
    { 
        $return = 0; 
    }
    elsif ($result =~ /$RRstring-ACK/) 
    { 
        $return = 2; 
    }
    elsif ($result =~ /$RRstring-NACK/) 
    { 
        $return = 1; 
    }
    $ksdbh->do("INSERT INTO rapid_reboot_history (board_id,board_port,return_code) VALUES(?, ?, ?)", undef, $switchPortInfo->{board_id}, $switchPortInfo->{board_port}, $return);
    if (DBI::err)
    {
        logsys("WARNING", "RapidReboot(): INSERT failed: " . DBI::errstr);
    }

    return $return;
}


#------------------------------------------------------------------------------------
# _softboot()
#
#   parameters
#       $ipaddr :   IP address of the server in question
#
#   return value
#       success :   1
#       failure :   undef
#
#   Overview
#       This function spawns off a small process that sends a shutdown -r command to
#       the remote server to reboot it
#
#------------------------------------------------------------------------------------

sub _softboot 
{
    my $ipaddr = shift();                              
    ($ipaddr) || return undef;

    if (-e $Config->{'ks_state'}."/softboot_off") 
    { 
        return undef; 
    }
    
    # This only works if the user ID that called this is setup in the /etc/sudoers file to be able to
    # call the softboot function (or we are root...)
    my $result = `sudo $Config->{ks_bin}/softboot $ipaddr`;
    logks('info', "softboot() result: $result");

    if ($result =~ /SUCCESS/) 
    { 
        return 1; 
    }
    else 
    { 
        return undef; 
    }
}    


1
