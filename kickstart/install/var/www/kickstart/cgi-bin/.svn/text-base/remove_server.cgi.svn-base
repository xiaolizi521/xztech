#!/usr/bin/perl -w                   
# =======================================================================
# Company:              Peer1
# Copyright(c):         Peer1 2008
# Project:              Kickstart
# Code Devloper:        Carlos Avila
# Creation Date:        04/24/2008
#
# File Type:            CGI
# File Name:            remove_server.cgi
#
# Description:
# This CGI will remove a mac address from the mac_list table on the
# kickstart databse. This will effectively remove the server from the
# system. It also has support to remove the mac from the dhcpd config file
# and the symlink placed in /tftpboot but these features are currently not
# in use.
# =======================================================================

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':standard';
use CGI ':cgi-lib';
use File::Copy;
use POSIX;

print header();

my $ks_db;
my $mac_list_id;
my $post         = new CGI;
my $mac_address  = $post->param("macaddr");
my $dhcpd_config = "/etc/dhcp3/dhcpd.conf";

my $query_mac_list = <<END;
SELECT id 
FROM mac_list 
WHERE mac_address = ? ;
END
my $query_delete_mac_list = <<END;
DELETE FROM mac_list
WHERE id = ? ;
END

printErr("Found empty MAC address.") unless $mac_address;
$mac_address = trim($mac_address);

printErr("$mac_address MAC Adress format is invalid.")
  unless ( $mac_address =~ /^([0-9a-f]{2}([:-]|$)){6}$/i );

$mac_list_id = &getMacListId($mac_address)
  || printErr("$mac_address Adress not found on DB");

&deleteMacListId($mac_list_id)
  || printErr("$mac_address Couldn't be deleted server from DB");

&update_pxe( $mac_address, 'none' )
  || printErr("$mac_address Couldn't delete the symlink");

kslog( "INFO", "$mac_address ID: $mac_list_id has been deleted from the DB." );
print "status=success";

sub deleteMacFromTftp() {
	my $mac = shift;
	my @args = ( "/exports/kickstart/bin/remove_symlink.pl", $mac );
	return system(@args);
}

sub deleteMacFromConf() {
	my $mac              = shift;
	my $tmp_dhcpd_config = "/tmp/dhcpd.conf.tmp";
	my $found_flag       = 0;
	open my $orig_fh, "<", $dhcpd_config
	  or kslog( "ERR", "Unable to open $dhcpd_config" );
	open my $new_fh, ">", $tmp_dhcpd_config
	  or kslog( "ERR", "Unable to create $tmp_dhcpd_config" );
	while (<$orig_fh>) {
		if ( $_ =~ /hardware\s+ethernet\s*$mac/ ) {
			$found_flag = 1;
			next;
		}
		print $new_fh $_;
	}
	close $orig_fh;
	close $new_fh;
	move( $tmp_dhcpd_config, $dhcpd_config )
	  or kslog( "WARNING", "Unable to move temp file." );
	return $found_flag;
}

sub deleteMacListId() {
	my $result = 0;
	my $id     = shift;
	my $delete_handle;
	$ks_db         = ks_dbConnect();
	$delete_handle = $ks_db->prepare($query_delete_mac_list);
	$result        = $delete_handle->execute($id);
	$ks_db->disconnect();
	return $result;
}

sub getMacListId() {
	my $row;
	my $rows_;
	my @rows_array;
	my $mac = shift;
	$ks_db = ks_dbConnect();
	$rows_ = $ks_db->selectall_arrayref( $query_mac_list, undef, $mac );
	$row   = $rows_->[0];
	$ks_db->disconnect();
	if ( !$row ) { return 0; }
	@rows_array = @{$row};
	return $rows_array[0];
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


