#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my ($dbh,$iface,$vconfig_bin,$ifconfig_bin);
my $vlans = {};

sub get_vlans {
	my $return = {};
	my $qry1 = "SELECT id,host(network(private_network)) as network,
		host(broadcast(private_network)) as bcast,
		host(netmask(private_network)) as netmask
		FROM vlans
		WHERE id != 1";
	my $sth1 = $dbh->prepare($qry1);
	$sth1->execute();
	my ($vlan, $network, $bcast, $netmask);
	$sth1->bind_columns(\($vlan, $network, $bcast, $netmask));
	while ($sth1->fetch()) {
		$return->{$vlan}->[0] = $network;
		$return->{$vlan}->[1] = $bcast;
		$return->{$vlan}->[2] = $netmask;
	}
	$sth1->finish();
	return $return;
}

sub check_vlan {
	my $vlan = shift();
	(-e "/proc/net/vlan/vlan$vlan") && return 0;
	my @largs = ($vconfig_bin, "add", $iface, $vlan);
	print join(' ', @largs)."\n";
	system("$vconfig_bin add $iface $vlan") == 0 || return 1;
}

$iface = "eth1";
$vconfig_bin = `which vconfig`; chomp $vconfig_bin;
($vconfig_bin) || (exit 1);
$ifconfig_bin = "/sbin/ifconfig";
my @args1 = ($ifconfig_bin, $iface, "up");
print join(' ', @args1)."\n";
system("$ifconfig_bin $iface up") == 0 || return 1;
my @args2 = ($vconfig_bin, "set_name_type", "VLAN_PLUS_VID_NO_PAD");
print join(' ', @args2)."\n";
system("$vconfig_bin set_name_type VLAN_PLUS_VID_NO_PAD") == 0 || return 1;

$dbh = ks_dbConnect();

$vlans = get_vlans();
foreach my $vlan (sort(keys %{$vlans})) {
	check_vlan($vlan);
	my @i = split(/\./, $vlans->{$vlan}->[0]);
	my $addr = join(".", $i[0], $i[1], $i[2], ($i[3]+2));
	my $gw = join(".", $i[0], $i[1], $i[2], ($i[3]+1));

	my @args = ("$ifconfig_bin", "vlan".$vlan, $addr,
		"broadcast", $vlans->{$vlan}->[1],
		"netmask", $vlans->{$vlan}->[2]);
	print join(' ', @args)."\n";
	system(@args);

	next if ($vlan == 405);

    if ($Config->{dc_abbr} eq "SAT3") {
	    @args = ("$ifconfig_bin", "vlan".$vlan.":0", $gw,
		    "broadcast", $vlans->{$vlan}->[1],
		    "netmask", $vlans->{$vlan}->[2]);
	    print join(' ', @args)."\n";
	    system(@args);
    }
}

$dbh->disconnect();

1;
