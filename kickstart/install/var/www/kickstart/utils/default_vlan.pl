#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

exit unless ($ARGV[0]);

my $macaddr = untaint('macaddr', $ARGV[0]);
($macaddr) || die "invalid macaddr";

#my $switchPortInfo = switchPortInfo($macaddr);
#portvlan($switchPortInfo, 0);

my $switchPortInfo = switchPortInfo($macaddr);
portControl($switchPortInfo, { vlan => 0 });

