#!/usr/bin/perl -w
# =======================================================================
# Company:              Server Beach
# Copyright(c):         Server Beach 2006
# Project:              Kickstart Sub-System
# Code Devloper:        SB Development Team
#
# File Type:            CGI
# File Name:		register.cgi	            
#
# Discription:
# The purpose of this CGI is to register the status of a server at a 
# given point and time the provisioning process.  Thei script takes the 
# MAC address of the server as input to update the kickstart database
# with the current status.
# ======================================================================

#Program Library usage and pragma defintions
BEGIN {
	use lib '/exports/kickstart/lib';
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

# Local Variable Declaration
my ($post, $postdata, $macaddr, $ipaddr, $status);

my $oldfh = select(STDOUT); $| = 1; select($oldfh);

print header(-Content_length => 15);

# Setup CGI Page Object
$post = new CGI;
$postdata = $post->Vars();

# Process information posted to this CGI and store into local
# variables.
$macaddr = untaint('macaddr', $postdata->{'macaddr'});
($macaddr) || kslog('err', "Invalid or null macaddr");

$ipaddr = untaint('ipaddr', $postdata->{'ipaddr'});
($macaddr) || kslog('err', "Invalid or null ipaddr");

$status = untaint('any', $postdata->{'status'});
($macaddr) || kslog('err', "Invalid or null status");

# Get Database Connection Handle
my $dbh = ks_dbConnect();

# Instansiate new MacFun Object passing the datbase handle
# and the MAC Address
my $macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);

# Set the status and the ipaddress of the object and log both to the kslog.
$macobj->status($status);
kslog('info', "$macaddr STATUS -> $status");
$macobj->ipaddr($ipaddr);
kslog('info', "$macaddr IPADDR -> $ipaddr");
$macobj->update();

###########################################################
#Process Task based on status information 
###########################################################
# 1. Check to see if audit is complete
if ($status eq "audit_done") {
	# Update the task
	$macobj->task("audit");
	# Push hardware information to database
	$macobj->hardware($postdata);
}
# 2. Check to see if audit has failed
elsif ($status eq "audit_fail") {
	$macobj->logError("audit_fail (no hardware?)");
}
# 3. Check STATUS if one of the following and go to sbrescue
elsif ($status =~ /^(\w+_copydone|\w+_wait|kickstarted|online)$/) {
	$macobj->pxe("localboot");
	$macobj->task("sbrescue");
	update_pxe($macaddr, "localboot");
}
# 4. 
elsif ($status eq "online_rescue") {
	# Only register status if used by customer
	if ($macobj->task() eq "remoterescue") { register($macobj, "rescue"); }

	# Set the server to localboot so the customer can reboot when done
	$macobj->pxe("localboot");
	$macobj->task("sbrescue");
	update_pxe($macaddr, "localboot");
}

# Perform update.
$macobj->update();

print "status=success\n";

# Drop database handle.
$dbh->disconnect;

1;
