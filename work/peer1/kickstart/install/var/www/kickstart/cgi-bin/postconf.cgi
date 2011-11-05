#!/usr/bin/perl -w

# =======================================================================
# Company:              Server Beach
# Copyright(c):         Server Beach 2006-2007
# Project:              Kickstart Sub-System
# Code Devloper:        SB Development Team
# Creation Date:        unknown
#
# File Type:            cgi executeable
# File Name:            postconf.cgi 
#
# Discription:
# This task file is used for burinin of a new system.
#
# ======================================================================='

# ############################################
# File inlcudes.
# ############################################
BEGIN {
	use lib '/exports/kickstart/lib';
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

# Local Varible Declaration
my ($post, $postdata, $macaddr, $update, $dbh, $macobj, $postconf);

# Setup CGI Perl Processing object
$post = new CGI;
$postdata = $post->Vars();
print header;

$macaddr = untaint('macaddr', $postdata->{'macaddr'});
($macaddr) || kslog("err", "Invalid or null macaddr");

$update = untaint('yorn', $postdata->{'update'});
($update) || ($update="yes");

# Setup Datbase Connection handle and Instantiate MAC Functions Object
$dbh = ks_dbConnect();

# Creat new MACFun Object passing the db connector
# and MAC Address information.
$macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
$postconf = $macobj->postconf();

# If no customer numbe is found then fetch it.
if (!$postconf->{customer_number}) { 
        $postconf = fetch_postconf($macaddr); 
}

# Write out the customer number information.
if ($postconf->{customer_number}) {
	$postconf->{'static'} = $Config->{'ks_ipaddr'};
	$postconf->{'ksserver'} = $Config->{'ks_ipaddr'};
	$postconf->{'ks_ipaddr'} = $Config->{'ks_ipaddr'};
	$postconf->{'ks_public_ipaddr'} = $Config->{'ks_public_ipaddr'};
        $postconf->{'osload'} = $macobj->osload();

	while (my($key, $value) = each(%{$postconf})) {
		next if ($key eq "VLAN");
		print "$key=$value\n";
	}

	if ($update eq "yes") {
		$macobj->status("postconf");
		kslog("info", "$macaddr STATUS -> postconf");
	}
}
$macobj->update();

$dbh->disconnect;

1;
