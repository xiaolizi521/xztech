#!/opt/perl/bin/perl -w

use lib qw(/exports/kickstart/lib);
use strict;
use SB::Config;
require 'sbks.pm';

my ($kdbh, $adbh);

sub check {
	my $input = shift();
	my $ldbh = $adbh;
	my @return;
	my $inventory;

	my $qry1 = "SELECT dc_abbr,switch,port,inventory_product_id
	FROM network_map t1
	WHERE t1.macaddress = ?";
	my $sth1 = $ldbh->prepare($qry1);
	$sth1->execute($input);
	if ($sth1->rows() == 1) {
		my @row = $sth1->fetchrow_array();
		@return = ($row[0],$row[1],$row[2]);
		$inventory = $row[3];
	}
	else {
		@return = ('xxx',0,0);
	}
	$sth1->finish();

	my $qry2 = "SELECT customer_product_id
	FROM xref_inventory_product_customer_product
	WHERE inventory_product_id = ?";
	my $sth2 = $ldbh->prepare($qry2);
	$sth2->execute($inventory);
	if ($sth2->rows() == 1) {
		my @row = $sth2->fetchrow_array();
		push(@return, $row[0]);
	}
	else {
		push(@return, 0);
	}

	return \@return;
}

$adbh = adm_dbConnect();
$kdbh = ks_dbConnect();

my $status;
if ($ARGV[0]) { $status = $ARGV[0]; }

my @macaddrs = ();

if ($status) {
    my $result = $kdbh->selectall_arrayref("SELECT mac_address FROM kickstart_map WHERE new_status = ?", undef, $status);
    foreach (@$result) { push(@macaddrs, $_->[0]); }
}
else {
    open LIST, "<poll.txt";
    while (<LIST>) { chomp; push(@macaddrs, $_); }
    close LIST;
}

foreach my $macaddr (@macaddrs) {
	my $info = check($macaddr);
	printf "%s,%s,%s,%s,%s\n", $macaddr, @{$info};
}

$adbh->disconnect();
$kdbh->disconnect();

1;
