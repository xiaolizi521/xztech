#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my $debug = 1;
if (($ARGV[0]) && ($ARGV[0] eq "doit")) { $debug = 0; }

my $dbh = ks_dbConnect();

my @online_servers = ();
my $online_servers_result = lwpfetch(
    sprintf("%s/list_online_servers.php", $Config->{pit_baseurl}),
    { dc_abbr => $Config->{dc_abbr} }, undef, undef);
if ($online_servers_result->[0]) {
	@online_servers = split(/\n/, $online_servers_result->[1]);
}
else {
	printf "Unable to fetch list of online servers for %s: %s\n",
                $Config->{dc_abbr}, $online_servers_result->[1];
	exit 1;
}

foreach my $row (@online_servers) {
	my ($macaddr, $ipaddr) = split(/,/, $row);
    print "Checking $macaddr with $ipaddr\n";

	my $macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);

	if ($macobj->ipaddr() ne $ipaddr) {
		printf "%s %s != %s\n", $macaddr, $macobj->ipaddr(), $ipaddr;
		$macobj->ipaddr($ipaddr) if ($debug == 0);
	}

	if ($macobj->status() !~ /online(_rescue)?$/) {
		printf "%s %s != %s\n", $macaddr, $macobj->status(), "online";
		$macobj->status("online") if ($debug == 0);
	}

    if (!defined($macobj->osload())) {
        print "No OS load, fixing\n";
        $macobj->osload("localboot");
    }

	if ($macobj->pxe() ne "localboot") {
		printf "%s %s != %s\n", $macaddr, $macobj->pxe(), "localboot";
		$macobj->pxe("localboot") if ($debug == 0);
	}

	$macobj->update();

	next if ($debug == 1);

    new_update_pxe($macaddr, "localboot");
}

$dbh->disconnect();
