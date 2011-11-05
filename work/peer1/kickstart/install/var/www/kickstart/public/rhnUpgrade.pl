#!/usr/bin/perl -w

use strict;

open LOG, ">rhnUpgrade.log";

open INPUT, "<rhes3_servers_iad2.txt";
while (<INPUT>) {
	chomp;
	my ($customers_id, $customer_product_idnum, $ip_address) = split(/\s+/, $_);
	my $command = "curl --silent http://64.34.160.84/rhnUpgrade | customers_id=$customers_id customer_product_idnum=$customer_product_idnum bash";
	my @args = ("ssh","-q",$ip_address,$command);
	my $sysres = system(@args);
	if ($sysres == 0) {
		print "# $ip_address OK\n";
		print LOG "# ".localtime()." $ip_address OK\n";
	}
	else {
		print "# $ip_address NO\n";
		print LOG "# ".localtime()." $ip_address NO\n";
	}
}
close INPUT;

close LOG;

1;
