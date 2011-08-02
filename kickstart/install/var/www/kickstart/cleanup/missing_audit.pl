#!/usr/bin/perl -w

BEGIN { 
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my @adminMacList;

my $lwpResult = lwpfetch($Config->{pit_baseurl}."/macList.php", 
    { datacenter_id => $Config->{dc_number} });

@adminMacList = split("\n", $lwpResult->[1]);

$ksdbh = ks_dbConnect();

my $dbResult = $ksdbh->selectall_arrayref("SELECT mac_address
    FROM kickstart_map
    ORDER BY mac_address");
foreach my $row (@$dbResult) {
    my $macAddress = $row->[0];
    if (!grep(/^$macAddress$/, @adminMacList)) {
        print $macAddress." does not exist\n";

        my $macObj = MACFun->new(dbh => $ksdbh, macaddr => $macAddress);
        $macObj->retire();
        new_update_pxe($macAddress, "sbrescue");

    }
}

$ksdbh->disconnect;

1;
