#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my ($macaddr, $dbh, $macobj);

$macaddr = untaint('macaddr', $ARGV[0]);
($macaddr) || exit 1;

$dbh = ks_dbConnect();
$macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
print $macobj->retire()."\n";
$macobj->update();
update_pxe($macaddr, "sbrescue");

$dbh->disconnect();

1;
