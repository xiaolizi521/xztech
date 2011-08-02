#!/usr/bin/perl -w

BEGIN {
    use lib qw(/exports/kickstart/lib);
    require 'sbks.pm';
}

use strict;
use SB::Config;

my ($doReboot, $doCmd);

# set to 1 to reboot - CHANGE IF DESIRED
$doReboot = 0;

# command to run - MUST CHANGE
$doCmd = "/etc/init.d/local";

my $macaddr = $ARGV[0];

if (($macaddr) && ($macaddr =~ /^((\w{2}:){5}\w{2})$/)) {
        $macaddr = lc($1);
} else { 
    print "Can't figure out mac";
    exit 1; 
}

$ksdbh = ks_dbConnect();

my $toDos = $ksdbh->selectall_arrayref("
    SELECT
        t1.ip_address
    FROM
        xref_macid_ipaddr t1,
        mac_list t2
    WHERE
        t1.mac_list_id = t2.id
    AND t2.mac_address = '$macaddr';");

foreach my $row (@$toDos) {
    my $ipAddress = $row->[0];

    print "Doing $macaddr ($ipAddress) .. ";

    sbadmWrapper($ipAddress, $doCmd);
    
    if ($doReboot == 1) {
        sbadmWrapper($ipAddress, "/usr/bin/sb_reboot");
    }
    
    print "done\n";
}

$ksdbh->disconnect();

1;

