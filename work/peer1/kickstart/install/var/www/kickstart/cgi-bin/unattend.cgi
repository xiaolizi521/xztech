#!/usr/bin/perl -w
# =======================================================================
# Company:              ServerBeach
# Copyright(c):         ServerBeach, Ltd. 2006-2007
# Project:              Kickstart Sub-System
# Pri. Code Devloper:   SB Development Team
# Creation Date:        N/A 
#
# File Type:            CGI    
# File Name:            unattend.cgi
# Dependencies:         N/A    
#
# Discription:
# This cgi script is used to dynamically select the unattend file for
# windows installs.  This script take in the MAC Address of the server and 
# in turn returns the windows unattend file.
# =======================================================================
BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

my ($post, $postdata, $macaddr, $dbh, $macobj, $postconf, $osload, $template);
my ($rpass, $compname, $workgroup);

$post = new CGI;
$postdata = $post->Vars();

print header;

$macaddr = untaint('macaddr', $postdata->{'macaddr'});
($macaddr) || kslog("err", "Invalid or null macaddr");

$dbh = ks_dbConnect();
$macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);

$postconf = $macobj->postconf();
$osload = $macobj->osload();
$template = '';

# Ugly, but it works
# 1: win2k 2: win 3: 2k
# 1: beta2k 2: beta 3: 2k
# 1: win2k3std 2: win 3: 2k3 4: std
# 1: beta2k3std 2: beta 3: 2k3 4: std
# 1: win2k3web 2: win 3: 2k3 4: web
# 1: beta2k3web 2: beta 3: 2k3 4: web
$osload =~ /^((win|beta)(2k3?)(std|web|ent|mssbp)?)$/;
my $winver = $3;

if ($winver eq "2k") {
	$template = $Config->{'ks_home'}."/kscfg/win2kunattend.temp";
}
elsif ($winver eq "2k3") {
	$template = $Config->{'ks_home'}."/kscfg/win2k3unattend.temp";
}
else {
	exit 0;
}

# Get the Root Password, Computer Name, and Workgroup Name
$rpass = $postconf->{'RPASS'};

$compname = $postconf->{'IPADDR'};
	$compname =~ s/.*\.//g;
	$compname = "SERVER".$compname;

$workgroup = $postconf->{'DOMAIN'};
	$workgroup =~ s/\..*$//g;
	$workgroup =~ s/(\w{15}).*/$1/g;
	$workgroup = uc($workgroup);

# Write entry to log file and write the unattend template out to a file.
kslog("info", "$macaddr got $template");
open IFH, "<$template";
while (<IFH>) {
	s/\%WORKGROUP\%/$workgroup/g;
	s/\%COMPNAME\%/$compname/g;
	s/\%RPASS\%/$rpass/g;
	print;
}
close IFH;

$macobj->update();
$dbh->disconnect;
exit 0;
