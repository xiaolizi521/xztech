#!/usr/bin/perl -w

BEGIN {
    use lib qw(/exports/kickstart/lib);
    require 'sbks.pm';
}

use strict;
use Data::Dumper;

$ksdbh = ks_dbConnect();

my $osMap = $ksdbh->selectall_hashref("SELECT osload, id FROM os_list", "osload");
my $pxeMap = $ksdbh->selectall_hashref("SELECT pxefile, id FROM pxe_list", "pxefile");
my $taskMap = $ksdbh->selectall_hashref("SELECT taskfile, id FROM task_list", "taskfile");
my $statusMap = $ksdbh->selectall_hashref("SELECT status, id FROM status_list", "status");

my $remoteDb = DBI->connect("dbi:Pg:dbname=kickstart;host=69.44.56.85", "kickstart", "l33tNix", { AutoCommit => 1, RaiseError => 1} );
if ($remoteDb->ping()) { print "Remote DB connected\n"; }

my $remoteQ = $remoteDb->prepare("SELECT * FROM kickstart_map WHERE mac_address = ?");
my $hardwareQ = $remoteDb->prepare("SELECT param,value FROM hardware WHERE mac_list_id = ?");

my $updateOsloadQ = $ksdbh->prepare("UPDATE xref_macid_osload SET os_list_id = ?, pxe_list_id = ?, task_list_id = ? WHERE mac_list_id = ?");
my $updateIpAddrQ = $ksdbh->prepare("UPDATE xref_macid_ipaddr SET ip_address = ? WHERE mac_list_id = ?");
my $insertHardwareQ = $ksdbh->prepare("INSERT INTO hardware VALUES (?, ?, ?)");

open INPUT, "<Phase6_maclist.txt";
while (<INPUT>) {
    chomp;
    my $macAddr = $_;
    my $macAddrId = $ksdbh->selectall_arrayref("SELECT id FROM mac_list WHERE mac_address = ?", undef, $macAddr)->[0]->[0];
    if (!$macAddrId) { print "Could not get ID for $macAddr\n"; last; }

    print "$macAddr $macAddrId\n";

    my $remoteInfo = $remoteDb->selectall_hashref($remoteQ, "mac_address", undef, $macAddr)->{$macAddr};
    my $hardwareInfo = $remoteDb->selectall_arrayref($hardwareQ, undef, $remoteInfo->{mac_list_id});

    my $osListId = $osMap->{$remoteInfo->{osload}}->{id};
    my $pxeListId = $pxeMap->{$remoteInfo->{pxefile}}->{id};
    my $taskListId = $taskMap->{$remoteInfo->{taskfile}}->{id};
    my $ipAddress = $remoteInfo->{ip_address};
    print "$macAddr OS: $osListId PXE: $pxeListId TASK: $taskListId IP: ".$remoteInfo->{ip_address}."\n";

    $updateOsloadQ->execute($osListId, $pxeListId, $taskListId, $macAddrId);
    $updateIpAddrQ->execute($ipAddress, $macAddrId);

    foreach my $row (@$hardwareInfo) {
        my $param = $row->[0];
        my $value = $row->[1];
        $insertHardwareQ->execute($macAddrId, $param, $value);
    }
}
close INPUT;

$remoteDb->disconnect();
$ksdbh->disconnect();

1;
