#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my ($macaddr, $action, $switch, $port);

$macaddr = $ARGV[0];
$action  = $ARGV[1];

if (($macaddr) && ($macaddr =~ /^((\w{2}:){5}\w{2})$/)) {
	$macaddr = lc($1);
}
else { exit 1; }

if (!$action || $action eq "") { $action = "cycle"; }

if ($macaddr) {
	print "Reboot $macaddr : ";
	my $result = rebootByMac($macaddr, 1, $action);
	print "$result\n";
}
else { exit 1; }

1;
