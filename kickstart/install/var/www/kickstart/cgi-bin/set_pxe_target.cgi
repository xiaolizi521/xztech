#!/usr/bin/perl -w                   
# =======================================================================
# Company:              Peer1
# Copyright(c):         Peer1 2008
# Project:              Kickstart
# Code Devloper:        Carlos Avila
# Creation Date:        04/24/2008
#
# File Type:            CGI
# File Name:            set_pxe_target.cgi
#
# Description:
# This CGI will create a symlink in /tftpboot/pxe/pxelinux.cfg that points
# to the specified OS image. It can be use to force a system to boot into
# a specific image. The mac address passed to the script doesn't necessary
# have to be on the kickstart DB but the pxefile does.
# =======================================================================

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;

print header();

my $ks_db;
my $post        = new CGI;
my $pxefile     = $post->param("pxefile");
my $mac_address = $post->param("macaddr");

printErr("Found empty MAC Adress.") unless $mac_address;
$mac_address = trim($mac_address);

printErr("$mac_address Found empty pxefile") unless $pxefile;
$pxefile = trim($pxefile);

printErr("$mac_address MAC Adress format is invalid.")
  unless ( $mac_address =~ /^([0-9a-f]{2}([:-]|$)){6}$/i );

$ks_db = ks_dbConnect();
printErr("$mac_address $pxefile is invalid.")
  unless isValidPxe( $ks_db, $pxefile );
$ks_db->disconnect();

update_pxe( $mac_address, $pxefile )
  || printErr("Failed pxed transaction. Is pxed running?");

print "status=success";

sub isValidPxe {
	my ( $dbh, $pxe ) = @_;
	my $result =
	  $dbh->selectall_arrayref( "SELECT id FROM pxe_list WHERE pxefile = ? ",
		undef, $pxe );
	if ( $result->[0] ) { return 1; }
	else { return 0; }
}

sub trim {
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}

sub printErr {
	my $message = shift;
	my $reason  = shift;
	$reason = "status=failed" unless $reason;
	print $reason;
	kslog( "ERR", $message );
}


