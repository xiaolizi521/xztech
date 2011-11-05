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
# a specific image. The pxefile is chosen by looking at xref_macid_osload
# on the kickstart DB.
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
my $post = new CGI;
my $pxefile;
my $mac_address = $post->param("macaddr");
my $mac_address_id;

my $query_mac_list = <<END;
SELECT id 
FROM mac_list 
WHERE mac_address = ? 
END
my $query_pxefile = <<END;
SELECT id 
FROM pxe_list 
WHERE pxefile = ?
END
my $query_xref_macid_osload = <<END;
SELECT pxefile 
FROM xref_macid_osload 
	LEFT JOIN pxe_list 
	ON ( xref_macid_osload.pxe_list_id = pxe_list.id ) 
WHERE mac_list_id = ? 
END

printErr("MAC Adress expected. Found empty string.") unless $mac_address;
$mac_address = trim($mac_address);
$mac_address =~ /^([0-9a-f]{2}([:-]|$)){6}$/i
  || printErr("$mac_address MAC Adress format is invalid.");

$ks_db = ks_dbConnect();

$mac_address_id = &getMacListId( $ks_db, $mac_address )
  || printErr("$mac_address address not found on DB.");

$pxefile = &getPxeFile( $ks_db, $mac_address_id )
  || printErr("$mac_address pxefile not found for this system");

kslog( "INFO", "$mac_address setting new target: $pxefile." );
update_pxe( $mac_address, $pxefile )
  || printErr("Failed pxed transaction. Is pxed running?");

$ks_db->disconnect();

print "status=success";

sub isValidPxe {
	my ( $dbh, $pxe ) = @_;

	my $result = $dbh->selectall_arrayref( $query_pxefile, undef, $pxe );
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

sub getMacListId() {
	my $row;
	my $rows_;
	my @rows_array;
	my ( $dbh, $mac ) = @_;
	$rows_ = $dbh->selectall_arrayref( $query_mac_list, undef, $mac );
	$row = $rows_->[0];
	$row || return 0;
	@rows_array = @{$row};
	return $rows_array[0];
}

sub getPxeFile() {
	my $row;
	my $rows_;
	my @rows_array;
	my ( $dbh, $mac_id ) = @_;
	$rows_ =
	  $dbh->selectall_arrayref( $query_xref_macid_osload, undef, $mac_id );
	$row = $rows_->[0];
	$row || return 0;
	@rows_array = @{$row};
	return $rows_array[0];
}


