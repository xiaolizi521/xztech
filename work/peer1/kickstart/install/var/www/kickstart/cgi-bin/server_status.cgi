#!/usr/bin/perl -w                   
# =======================================================================
# Company:              Peer1
# Copyright(c):         Peer1 2008
# Project:              Kickstart
# Code Devloper:        Carlos Avila
# Creation Date:        04/24/2008
#
# File Type:            CGI
# File Name:            server_status.cgi
#
# Description:
# This CGI simply returns the current status of a server on the kickstart
# database. It will return status name not the status ID. It takes a
# MAC address from POST.
#
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

my $post = new CGI;
my $post_data;
my $mac_address = $post->param("macaddr");
my $ks_db;
my $query_status = <<END;
SELECT status_list.status 
FROM macid_status_current 
        LEFT JOIN mac_list ON 
        ( macid_status_current.mac_list_id = mac_list.id ) 
        LEFT JOIN status_list ON 
        ( macid_status_current.new_status_id = status_list.id ) 
WHERE mac_list.mac_address = ? ;
END

printErr( "Found empty MAC address", "mac_blank" )
  unless $mac_address;
$mac_address = trim($mac_address);

printErr( "MAC Adress format $mac_address is invalid.", "mac_invalid" )
  unless ( $mac_address =~ /^([0-9a-f]{2}([:-]|$)){6}$/i );

my $row;
my $result = "";
$ks_db  = ks_dbConnect();
$result = $ks_db->selectall_arrayref( $query_status, undef, $mac_address );
$row    = $result->[0];
$ks_db->disconnect();
printErr( "MAC Adress $mac_address not found on DB.", "not_found" ) unless $row;
kslog( "INFO", "$mac_address Requested status" );
print( @{$row} );

sub trim {
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}

sub printErr {
	my $message = shift;
	my $reason  = shift;
	$reason = "script_failed" unless $reason;
	print $reason;
	kslog( "ERR", $message );

}


