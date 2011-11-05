#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      Bootinfo.pm
#
# Overview:
#   Script contains functions required to gather the data gets sent whilst a server boots
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::Bootinfo;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw();
    %EXPORT_TAGS = ( 'all' => [ qw(
        dhcp_refresh
        update_pxe
        new_update_pxe
        clean_mac
    ) ]);
    @EXPORT_OK = ( @{ $EXPORT_TAGS{'all'} } );

}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use LWP::UserAgent;
use Compress::Zlib;
use IO::Socket::INET;

#############################
#    Serverbeach modules    #
#############################
use SB::Config;
use SB::MACFun;
use SB::Common "untaint";

$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

# ===============================================================================================
# Package scoped variable definitions
# ===============================================================================================


# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# dhcp_refresh()
#
#   parameters
#       none
#
#   return value
#       success     :   0
#       not root    :   127
#
#   Overview
#       Replaces the dhcp.conf file and restarts the daemon if a newer file exists
#
#------------------------------------------------------------------------------------
sub dhcp_refresh {
        if ($< != 0) { return 127; }
        system($Config->{'ks_bin'}."/dhcpconf.pl","-t");
        my $diff = system("diff","$Config->{'dhcpconf'}",
                "$Config->{'dhcpconf'}.new");
        if ($diff != 0) {
                rename("$Config->{'dhcpconf'}.new", "$Config->{'dhcpconf'}");
                system($Config->{'dhcpinit'},"restart");
        }
        else {
                unlink("$Config->{'dhcpconf'}.new");
        }
        return 0;
}



#------------------------------------------------------------------------------------
# new_update_pxe()
#
#   parameters
#       $macaddr    :   mac address of a server
#       $target     :   target of symlink
#
#   return value
#       success :   0
#       failure :   1
#
#   Overview
#       Creates the symlink target for pxe booting 
#
#------------------------------------------------------------------------------------
sub new_update_pxe {
    my ($macaddr, $target) = @_;

    $macaddr = untaint('macaddr', $macaddr);
    $target  = untaint('any', $target);
    ($macaddr && $target) || return 0;

    (my $pxemac = $macaddr) =~ s/:/-/g;
    $pxemac = "01-".$pxemac;

    chdir("/tftpboot/pxe/pxelinux.cfg/");
    if (-l "$pxemac") {
        my $pxetarget = readlink($pxemac);
        if ($pxetarget ne $target) {
            unlink($pxemac) == 1 || return 0;
            symlink($target,$pxemac) == 1 || return 0;
        }
    }
    else {
        symlink($target,$pxemac) == 1 || return 0;
    }

    1;
}



#------------------------------------------------------------------------------------
# update_pxe()
#
#   parameters
#       $lmac   :   mac address
#       $target :   target
#
#   return value
#       success :   1
#       failure :   0
#
#   Overview
#       Writes a mac address and target to port 6969
#
#------------------------------------------------------------------------------------
sub update_pxe {
        my ($lmac, $target) = @_;

        $lmac = untaint('macaddr', $lmac);
        $target = untaint('any', $target);
        (($lmac) && ($target)) || return 0;

        my $sock = IO::Socket::INET->new(
                'PeerAddr' => '127.0.0.1',
                'PeerPort' => 6969,
                'Proto' => 'tcp') or die "$!";

        if ($sock) {
                print $sock "wikiwiki $lmac $target\n";
                close ($sock);
                return 1;
        }
        else { return 0; }

}



#------------------------------------------------------------------------------------
# clean_mac()
#
#   parameters
#       $input  :   input value
#
#   return value
#       returns modified input (that I am assuming is a mac address)
#
#   Overview
#       This function is not being used.  It is just being included so that all of the
#       functions in sbks.pm get successfully migrated.
#
#------------------------------------------------------------------------------------

sub clean_mac {
        my $input = $_[0];
        $input =~ s/0(\w)/$1/g;
        $input = lc($input);
        return $input;
}



1
