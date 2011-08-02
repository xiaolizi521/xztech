#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      Logger.pm
#
# Overview:
#   Provides logging functions for other perl scripts in the kickstart system
#
#   Valid log levels for all logging functions are
#           EMERG       :   not used in our system
#           ALERT       :   not used in our system
#           CRIT        :   Unrecoverable system error
#           ERR         :   Error has occurrend
#           WARNING     :   Issue has occurred that could lead to error if not handled
#           NOTICE      :   Slightly more important than info
#           INFO        :   Standard info from processing
#           DEBUG       :   Extra information when script running in debug mode, used for
#                           troubleshooting
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::Logger;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw(logks logsys);
    %EXPORT_TAGS = ();
    @EXPORT_OK = qw();

}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use Sys::Syslog qw(:DEFAULT setlogsock);


$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# logks()
#
#   parameters
#       $loglvl     : syslog error level
#       $logmsg     : log message
#
#   return value
#       Always return true (if there is an error logging an error, how do we log it?)
#
#   Overview
#       logging function for provisioning messages and errors
#
#------------------------------------------------------------------------------------

sub logks {
        my ($loglvl, $logmsg) = @_;
        (($loglvl) && ($logmsg)) || return 1;
        $loglvl = uc($loglvl);

        my $sock_type = 'unix';
        my $ident = $0;
        $ident =~ s/.*\///g;
        my $logopt = 'pid';
        my $facility = 'local6';

        setlogsock($sock_type);
        openlog($ident, $logopt, $facility);
        syslog($loglvl, "[$loglvl] $logmsg");
        closelog();

        return 0;
}

#------------------------------------------------------------------------------------
# logsys()
#
#   parameters
#       $loglvl     : syslog error level
#       $logmsg     : log message
#
#   return value
#       Always return true (if there is an error logging an error, how do we log it?)
#
#   Overview
#       logging function for system messages and errors where there is no association
#       with a particular system being provisioned
#
#------------------------------------------------------------------------------------

sub logsys {
        my ($loglvl, $logmsg) = @_;
        (($loglvl) && ($logmsg)) || return 1;
        $loglvl = uc($loglvl);

        my $sock_type = 'unix';
        my $ident = $0;
        $ident =~ s/.*\///g;
        my $logopt = 'pid';
        my $facility = 'local6';

        setlogsock($sock_type);
        openlog($ident, $logopt, $facility);
        syslog($loglvl, "[$loglvl] $logmsg");
        closelog();

        return 0;
}

1
