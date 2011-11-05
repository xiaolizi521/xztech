#!/usr/bin/perl -wT

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

my ($post, $postdata, $result, $reboot_server, $serial_port, $board_address, $board_port, $board_id);

print header;

$post = new CGI;
$postdata = $post->Vars();

my $switchPortInfo = {};
$reboot_server = $postdata->{'reboot_server'};
$serial_port = $postdata->{'serial_port'};
$board_address = $postdata->{'board_address'};
$board_port = $postdata->{'board_port'};
$board_id = $postdata->{'board_id'};

($reboot_server) || kslog('err', "Invalid or null reboot_server: $reboot_server");
($board_port) || kslog('err', "Invalid or null board_port: $board_port");
($board_id) || kslog('err', "Invalid or null board_id: $board_id");

$switchPortInfo->{reboot_server} = $reboot_server;
$switchPortInfo->{serial_port} = $serial_port;
$switchPortInfo->{board_address} = $board_address;
$switchPortInfo->{board_port} = $board_port;
$switchPortInfo->{board_id} = $board_id;

kslog('info', "Attempting port RR: $reboot_server $serial_port $board_address $board_port $board_id");

$result = RapidReboot($switchPortInfo);

kslog('info', "PortReboot return status $result");

if ($result && $result == 2) { print "status=success\n"; }
elsif ($result && $result == 1) { print "status=error\n"; }
else { print "status=failure\n"; }

1;
