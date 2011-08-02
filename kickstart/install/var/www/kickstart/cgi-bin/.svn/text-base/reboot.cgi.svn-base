#!/usr/bin/perl -wT

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

my ($post, $postdata, $result, $macaddr);

print header;

$post = new CGI;
$postdata = $post->Vars();

$macaddr = untaint('macaddr', $postdata->{'macaddr'});
($macaddr) || kslog('err', "Invalid or null macaddr");

$result = rebootByMac($macaddr);

if ($result) { print "status=success\n"; }
else { print "status=failure\n"; }

1;
