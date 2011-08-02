#!/usr/bin/perl -w

BEGIN {
    use lib qw(/exports/kickstart/lib);
    require 'sbks.pm';
}

use strict;
use Data::Dumper;

open LOG, ">massRRtest.log";
my $oldfh = select(LOG); $| = 1; select($oldfh);

$ksdbh = ks_dbConnect();

my $servers = $ksdbh->selectall_arrayref("SELECT mac_address FROM kickstart_map WHERE new_status = ? ORDER BY last_update ASC", undef, "ready");

my @tested;
foreach my $row (@$servers) {
    my $macAddress = $row->[0];

    my $macObj = MACFun->new(dbh => $ksdbh, macaddr => $macAddress);
    my $ipAddress = $macObj->ipaddr();

    if ($ipAddress eq "0.0.0.0") {
        print $macAddress."\thas no IP to test\n";
        next;
    }

    my $icmpPing = Net::Ping->new("icmp", 1);
    if (!$icmpPing->ping($ipAddress)) {
        print $macAddress."\t".$ipAddress." does not ping\n";
        next;
    }
    else {
        print $macAddress."\t".$ipAddress." initial ping test successful\n";
    }

    my $switchPortInfo = switchPortInfo($macAddress);
    my $boardName = $switchPortInfo->{switch}."-".$switchPortInfo->{board_number};

    if (grep(/^$boardName$/, @tested)) {
        print "Already tested $boardName\n";
        next; }

    my $location = $switchPortInfo->{dc_abbr}.":".$switchPortInfo->{switch}.
        "-".$switchPortInfo->{switch_port};

    my $rr_res = RapidReboot($switchPortInfo, "cycle");

    if (!$icmpPing->ping($ipAddress)) {
        print "$macAddress $location $boardName $rr_res SUCCESS\n";
        print LOG "$macAddress $location $boardName $rr_res SUCCESS\n";
        push @tested, $boardName;
    }
    else {
        print "$macAddress $location $boardName $rr_res FAILURE\n";
        print LOG "$macAddress $location $boardName $rr_res FAILURE\n";
    }
}

$ksdbh->disconnect();

close LOG;

1;
