#!/usr/bin/perl -w

BEGIN { 
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my $kdbh = ks_dbConnect();

#"select t1.macaddress,t2.customer_product_id from inventory_mac_addr t1, xref_inventory_product_customer_product t2 where t1.inventory_product_id=t2.inventory_product_id and t1.id not in (select inventory_mac_addr_id from xref_switch_mac_addr)";

my @unlinked = ();
my $unlinked_servers_result = lwpfetch(
    sprintf("%s/list_unlinked_servers.php", $Config->{pit_baseurl}),
    { dc_abbr => $Config->{dc_abbr} }, undef, undef);
if ($unlinked_servers_result->[0]) {
	@unlinked = split(/\n/, $unlinked_servers_result->[1]);
}
else {
	my $logstring = sprintf("Unable to fetch list of unlinked servers for %s: %s", $Config->{dc_abbr}, $unlinked_servers_result->[1]);
	print $logstring."\n";
	kslog("info", $logstring);
	exit 1;
}

my $ksinfo_sth = $kdbh->prepare("SELECT mac_address,new_status FROM kickstart_map WHERE mac_address = ?");

foreach my $macaddr (@unlinked) {
    next if (grep(/^$macaddr$/, @{$Config->{bootServerMacs}}));
    next if ($macaddr eq "00:12:17:2f:ca:3a"); # IT switch on IAD2:a3-48
    next if ($macaddr eq "00:0e:0c:66:46:c3"); # 15906-1
    next if ($macaddr =~ /^10:4c:/); #15906-1
    #next if ($macaddr =~ /^00:0[34]:/); # For customer 4312-9 and his VMs
    #next if ($macaddr =~ /^00:50:/); # VMware?

	my $ksinfo = $kdbh->selectall_hashref($ksinfo_sth, 'mac_address', undef, $macaddr)->{$macaddr};

	if ($ksinfo->{new_status} =~ /(holding|ready|updateks)/) { next; }

	print "$macaddr not linked to a customer\n";

	my $server = MACFun->new(dbh => $kdbh, macaddr => $macaddr);
	$server->status("updateks");
	$server->osload("default");
	$server->update();

    my $errors = $server->error();
    if ($errors->[0]) {
        print "Errors: ".join(" : ", @$errors)."\n";
    }

    register($server, "unknown");

    new_update_pxe($macaddr, "sbrescue");
}

$kdbh->disconnect();

1;
