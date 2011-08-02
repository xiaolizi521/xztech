#!/usr/bin/perl -w

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

my ($post, $postdata, $macaddr, $update, $fetch, $dbh, $macobj, $encoded);

$post = new CGI;
$postdata = $post->Vars();

print header('application/octet-stream');

$macaddr = untaint('macaddr', $postdata->{'macaddr'});
($macaddr) || kslog("err", "Invalid or null macaddr");

$update = untaint('yorn', $postdata->{'update'});
($update) || ($update="yes");	# Default to yes

$fetch = untaint('yorn', $postdata->{'fetch'});
($fetch) || ($fetch="no");	# Default to no

$dbh = ks_dbConnect();
$macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
$encoded = $macobj->licenses();
($encoded) || ($encoded = fetch_licenses($macaddr));

if ($encoded) {
	print MIME::Base64::decode($encoded);
}

if ($update eq "yes") {
	$macobj->status("licenses");
	kslog("info", "$macaddr STATUS -> licenses");
}

$macobj->update();


$dbh->disconnect();

1;
