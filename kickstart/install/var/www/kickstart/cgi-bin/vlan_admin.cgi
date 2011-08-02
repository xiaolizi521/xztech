#!/usr/bin/perl -w
# =======================================================================
# Company:              Peer1
# Copyright(c):         Peer1 2009
# Project:              Kickstarti/PrivNet
# Code Devloper:        Caleb Collins ccollins@peer1.com
# Creation Date:        02/20/2009
#
# File Type:            CGI
# File Name:            vlan_admin.cgi 
#
# Description:
# This CGI will take POST'd values of (vlan, public, private, & status)
# and take the appropiate action based on the status. This will include
# adding, removing, updating, & viewing VLANs. It will not allow any
# modification to the 200/405 VLANs.
# =======================================================================

# Pull in the SB libs
BEGIN {
        use lib '/exports/kickstart/lib';
        require 'sbks.pm';
}

# Define required perl libs 
use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;
use DBI;

# Setup vars
my ( $vlan, $status, $public, $private );

# Setup CGI POST 
my $post = new CGI;
my $postdata = $post->Vars();
print header;

# Validate POST data with untaint
sub valid_net() {
	# CGI POST vlan
	$vlan = untaint('digits', $postdata->{'vlan'});
	($vlan) || kslog("err", "Invalid or null vlan ID");
	# CGI POST public
	$public = untaint('cidr', $postdata->{'public'});
	($public) || kslog("err", "Invalid or null IP Address"); 
	# CGI POST private
	$private = untaint('cidr', $postdata->{'private'});
	($private) || kslog("err", "Invalid or null IP Address");
}

# Build functions for add/remove
sub vlan_add() {
	my $vlan_up = `/exports/kickstart/bin/vlan_restart`;
}

sub vlan_remove() {
	my $vlan_down = `/exports/kickstart/bin/vlan_down vlan$vlan down`;
	my $vlan_up = `/exports/kickstart/bin/vlan_restart`;
}

# CGI POST status
$status = untaint('words', $postdata->{'status'});
($status) || kslog("err", "Invalid or null status");

# Connect to KS DB
$dbh = ks_dbConnect();

# IP Check
my $ip = $ENV{'REMOTE_ADDR'};

# Add VLAN
if ($status eq "add") {
	valid_net();
	if ($vlan =~ m/(405|200)/) {
		kslog("err", "$ip Tried to remove $vlan");
		print "status=failed\n";
		exit 1
	}
        $dbh->do("INSERT INTO vlans(id, public_network, private_network) VALUES (?, ?, ?)", undef, ($vlan, $public, $private)) or die ("Cannot INSERT");
	vlan_add();
	kslog("info", "$ip Added VLAN $vlan PubNet=$public PrivNet=$private");
        print "status=success\n";
# Remove VLAN
} elsif ($status eq "remove") {
	valid_net();
        $sth=$dbh->prepare("DELETE FROM vlans where id='$vlan' and public_network='$public' and private_network='$private'");
        $sth->execute();
	vlan_remove();
	kslog("info", "$ip Removed VLAN $vlan PubNet=$public PrivNet=$private");
        print "status=success\n";
# Update VLAN
} elsif ($status eq "update") {
	valid_net();
        $sth=$dbh->prepare("UPDATE vlans SET public_network='$public', private_network='$private' where id='$vlan'");
        $sth->execute();
	vlan_remove();
	kslog("info", "$ip Updated VLAN $vlan to PubNet=$public PrivNet=$private");
        print "status=success\n";
# View all VLANs 
} elsif ($status eq "view") {
	$sth=$dbh->prepare("SELECT id, public_network, private_network from vlans");
	$sth->execute();
	$hostname = (`hostname`);
	print "<html><head>\n";
	print "<title>SB VLANs</title></head>\n";
	print "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#FF0000\" vlink=\"#800000\">\n";
	print "<b><div align='center'>$ip viewing $hostname VLAN DB entries</div></b>\n";
	print "<table bgcolor='#000000' border='1' width='350' align='center'>\n";
	print "<tr bgcolor='#ffffff'><td>VLAN ID</td><td>Public Network</td><td>Private Network</td>\n";
	while ($ref = $sth->fetchrow_hashref()) {
		print "<tr bgcolor='#ffffff' width='100%'><td>$ref->{'id'}</td><td>$ref->{'public_network'}</td><td>$ref->{'private_network'}</td></tr><br>";
	}
	print "</table>";
	print "</body></html>\n";
# Failsafe
} else {
        print "status=failed\n";
	kslog("err", "Please verify you passed correct vlan, public, private, & status to CGI");
        exit 1;
}

# Disconnect DB
$dbh->disconnect;
