#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

$ksdbh = ks_dbConnect();

my $holding = $ksdbh->selectall_arrayref("SELECT mac_address FROM kickstart_map WHERE new_status = 'holding' ORDER BY last_update ASC");

foreach my $row (@$holding) {
    my $macaddr = $row->[0];
    my $newOSload = "burnin";
    my $newPXE;

    print "Burning $macaddr : ";

    my $mobj = MACFun->new(dbh => $ksdbh, macaddr => $macaddr);

    $mobj->osload($newOSload);
    print "osload -> $newOSload : ";
    $mobj->update();

    $newPXE = $mobj->pxe();
    my $pxeres = new_update_pxe($macaddr, $newPXE);
    print "pxe -> $newPXE ($pxeres) : ";
    my $rebooted = rebootByMac($mobj->macaddr());
    print "reboot = $rebooted\n";

    my $switchPortInfo = switchPortInfo($mobj->macaddr());
    portvlan($switchPortInfo, 405);

}

$ksdbh->disconnect();

