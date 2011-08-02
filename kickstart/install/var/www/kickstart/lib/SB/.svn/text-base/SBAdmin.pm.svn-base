#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      SBAdmin.pm
#
# Overview:
#   Provides access to calls requiring sbadmin.  Currently, the only function is sbadmWrapper which
#   is a wrapper for calling the sbadm_wrapper.pl script.  This needs to be corrected in the future
#   and start including more functions that require sbadmn access.  Possible such as changing
#   the _softboot function to a sbadmWrapper call with "reboot" as the command
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::SBAdmin;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw(sbadmWrapper);
    %EXPORT_TAGS = ( 'all' => [ qw(
        sbadmWrapper
    ) ]);
    @EXPORT_OK = ( @{ $EXPORT_TAGS{'all'}  } );

}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use LWP::UserAgent;
use Compress::Zlib;

#############################
#    Serverbeach modules    #
#############################
use SB::Config;

$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

# ===============================================================================================
# Package scoped variable definitions
# ===============================================================================================


# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# sbadmWrapper()
#
#   parameters
#       $ipaddr :   IP address of the server in question
#       $command:   id of command to run on server
#
#   return value
#       success :   1
#       failure :   0
#
#   Overview
#       This is used to pass a command to the sbadm_wrapper.pl script.  Not modifying
#       the function at this time, but there seems to be logic error involved in this
#
#------------------------------------------------------------------------------------

sub sbadmWrapper 
{
    my $ipaddr  = shift();
    my $command = shift();
    my $wrapper = $Config->{ks_bin}."/sbadm_wrapper";

    if (!-e $wrapper) {
        logks('info', "$wrapper missing!");
        return 0;
    }
    
    my @stat = stat($wrapper);

    my $uid = $stat[4];
    my $mode = sprintf("%04o", $stat[2] & 07777);

    if ($uid != 0 || $mode != "0700") {
        logks('info', "$wrapper has wrong mode $mode");
        return 0;
    }

    if ($command eq "clearparts") { $command = "/usr/bin/sb_clearparts"; }
    if ($command eq "reboot") { $command = "/usr/bin/sb_reboot"; }

    logks('info', "$wrapper $ipaddr $command");
    
    my $result = `$wrapper $ipaddr "$command"`;
    if ($result =~ /SUCCESS/) { return 1; }
    else { return 0; }
}


1
