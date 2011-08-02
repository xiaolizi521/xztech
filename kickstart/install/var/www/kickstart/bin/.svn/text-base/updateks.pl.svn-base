#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my $macaddr = untaint('macaddr', $ARGV[0]);
($macaddr) || exit 1;
my $osload = untaint('words', $ARGV[1]);
($osload) || exit 1;

my $result = update_ks($macaddr, $osload);
print "$macaddr $result\n";
