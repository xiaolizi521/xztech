#!/usr/bin/perl -w

BEGIN {
    use lib qw(/exports/kickstart/lib);
    require 'sbks.pm';
}

use strict;
use SB::Config;

my $debug = 1;

if ($ARGV[0] && $ARGV[0] eq "doit") { $debug = 0; }

$ksdbh = ks_dbConnect();

my $burninFails = $ksdbh->selectall_arrayref("SELECT mac_address,ip_address FROM kickstart_map WHERE new_status = 'burnin_fail'");

foreach my $row (@$burninFails) {
    my $macAddress = $row->[0];
    my $ipAddress = $row->[1];

    print "Clearing partitions on $macAddress ($ipAddress) .. ";

    if ($debug) {
        print "not really\n";
    } else {
        sbadmWrapper($ipAddress, '/usr/bin/sb_clearparts; /usr/bin/sb_reboot');
        #sbadmWrapper($ipAddress, "/usr/bin/sb_reboot")
        #if (sbadmWrapper($ipAddress, "/usr/bin/sb_clearparts")) {
        print "done\n";
    }
}

$ksdbh->disconnect();

1;

