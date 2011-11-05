#!/usr/bin/perl -w

use strict;
use DBI;
use POSIX;

sub moveMacList {
    my ($oldDbh, $newDbh) = @_;

    print "Moving MAC list :\n";

    print "\tGetting data from old database .. ";
	my $mac_list = $oldDbh->selectall_arrayref("SELECT mac_address, first_seen FROM mac_list ORDER BY first_seen");
    print "done.\n";
	
    print "\tInserting data into new database .. ";
	$newDbh->begin_work();
	my $mac_insert_sth = $newDbh->prepare("INSERT INTO mac_list (mac_address, date_added) VALUES (?,?)");
	
	foreach my $row (@{$mac_list}) {
	    my $mac_address = $row->[0];
	    my $date_added = strftime("%F %T", localtime($row->[1]));
	    $mac_insert_sth->execute($mac_address, $date_added);
	}
	
	$mac_insert_sth->finish();
	$newDbh->commit();
    print "done.\n";

}

sub moveHardware {
    my ($oldDbh, $newDbh, $macids) = @_;

    print "Moving MAC hardware :\n";

    if (!$macids) {
        $macids = $newDbh->selectall_hashref("SELECT id, mac_address, date_added FROM mac_list ORDER BY id", "mac_address");
    }

    print "\tGetting data from old database .. ";
	my $hardware = $oldDbh->selectall_arrayref("SELECT mac_address, param, value FROM mac_list t1, hardware t2 WHERE t2.macid = t1.id ORDER BY t1.id");
    print "done.\n";

    print "\tRemoving data from new database .. ";
    my $deleted = $newDbh->do("DELETE FROM hardware");
    print $deleted . " rows deleted.\n";
	
    print "\tInserting data into new database .. ";
	$newDbh->begin_work();
	my $hardware_insert_sth = $newDbh->prepare("INSERT INTO hardware (mac_list_id, param, value) VALUES (?,?,?)");
	
	foreach my $row (@{$hardware}) {
	    my ($mac_address, $param, $value) = @{$row};
	    $hardware_insert_sth->execute($macids->{$mac_address}->{id}, $param, $value);
	}
	
	$hardware_insert_sth->finish();
	$newDbh->commit();
    print "done.\n";

}

sub moveStatus {
    my ($oldDbh, $newDbh, $macids, $statids) = @_;

    print "Moving MAC status :\n";

    if (!$macids) {
        $macids = $newDbh->selectall_hashref("SELECT id, mac_address, date_added FROM mac_list ORDER BY id", "mac_address");
    }
    if (!$statids) {
        $statids = $newDbh->selectall_hashref("SELECT id, status FROM status_list ORDER BY id", "status");
    }

    print "\tGetting data from old database .. ";
    my $status = $oldDbh->selectall_arrayref("SELECT mac_address, t3.status AS old_status, t4.status AS new_status, timestamp FROM xref_macid_statid_current t1, mac_list t2, status_list t3, status_list t4 WHERE t2.id = t1.macid AND t3.id = t1.old_statid AND t4.id = t1.new_statid");
    print "done.\n";

    print "\tRemoving data from new database .. ";
    my $deleted = $newDbh->do("DELETE FROM macid_status_current");
    print $deleted . " rows deleted.\n";

    print "\tInserting data into new database .. ";
    $newDbh->begin_work();
    my $status_insert_sth = $newDbh->prepare("INSERT INTO macid_status_current (mac_list_id, old_status_id, new_status_id, date_added) VALUES (?,?,?,?)");

    foreach my $row (@{$status}) {
        my $mac_address = $row->[0];
        my $old_status = $row->[1];
        my $new_status = $row->[2];
	    my $date_added = strftime("%F %T", localtime($row->[3]));
        #print "$mac_address $old_status\t$new_status\t$date_added\n";
        $status_insert_sth->execute($macids->{$mac_address}->{id}, $statids->{$old_status}->{id}, $statids->{$new_status}->{id}, $date_added);
    }

    $status_insert_sth->finish();
    $newDbh->commit();
    print "done.\n";

}

sub moveStatusHistory {

    my ($oldDbh, $newDbh, $macids) = @_;

    print "Moving MAC status history :\n";

    if (!$macids) {
        $macids = $newDbh->selectall_hashref("SELECT id, mac_address, date_added FROM mac_list ORDER BY id", "mac_address");
    }
    my $statids = $newDbh->selectall_hashref("SELECT id, status FROM status_list ORDER BY id", "status");

    print "\tGetting data from old database .. ";
    my $status = $oldDbh->selectall_arrayref("SELECT mac_address, t3.status AS old_status, t4.status AS new_status, timestamp FROM xref_macid_statid_history t1, mac_list t2, status_list t3, status_list t4 WHERE t2.id = t1.macid AND t3.id = t1.old_statid AND t4.id = t1.new_statid ORDER BY timestamp");
    print "done.\n";

    print "\tRemoving data from new database .. ";
    my $deleted = $newDbh->do("DELETE FROM macid_status_history");
    print $deleted . " rows deleted.\n";

    print "\tInserting data into new database .. ";
    $newDbh->begin_work();
    my $status_insert_sth = $newDbh->prepare("INSERT INTO macid_status_history (mac_list_id, old_status_id, new_status_id, date_added) VALUES (?,?,?,?)");

    foreach my $row (@{$status}) {
        my $mac_address = $row->[0];
        my $old_status = $row->[1];
        my $new_status = $row->[2];
	    my $date_added = strftime("%F %T", localtime($row->[3]));
        #print "$mac_address $old_status\t$new_status\t$date_added\n";
        $status_insert_sth->execute($macids->{$mac_address}->{id}, $statids->{$old_status}->{id}, $statids->{$new_status}->{id}, $date_added);
    }

    $status_insert_sth->finish();
    $newDbh->commit();
    print "done.\n";

}

sub moveVlans {

    my ($oldDbh, $newDbh, $macids) = @_;

    print "Moving vlan information :\n";

    if (!$macids) {
        $macids = $newDbh->selectall_hashref("SELECT id, mac_address, date_added FROM mac_list ORDER BY id", "mac_address");
    }

    print "\tGetting data from old database .. ";
    my $vlans = $oldDbh->selectall_arrayref("SELECT id, public_network, private_network FROM vlan_map ORDER BY id");
    print "done.\n";

    print "\tRemoving data from new database .. ";
    my $deleted = $newDbh->do("DELETE FROM vlans");
    print $deleted . " rows deleted.\n";

    print "\tInserting data into new database .. ";
    $newDbh->begin_work();
    my $vlans_insert_sth = $newDbh->prepare("INSERT INTO vlans (id, public_network, private_network) VALUES (?,?,?)");

    foreach my $row (@{$vlans}) {
        my $id = $row->[0];
        my $public_network = $row->[1];
        my $private_network = $row->[2];
        $vlans_insert_sth->execute($id, $public_network, $private_network);
    }

    $vlans_insert_sth->finish();
    $newDbh->commit();
    print "done.\n";

}

sub moveSwitches {
    my ($oldDbh, $newDbh) = @_;

    print "Moving reboot information :\n";

    print "\tGetting data from old database .. ";
    my $switches = $oldDbh->selectall_arrayref("SELECT switch_name, reboot_server, reboot_serial_port, reboot_board_address FROM reboot_system ORDER BY reboot_server, reboot_serial_port, reboot_board_address");
    print "done.\n";

    print "\tRemoving data from new database .. ";
    my $deleted = $newDbh->do("DELETE FROM sb_switch");
    print $deleted . " rows deleted.\n";

    print "\tInserting data into new database .. ";
    $newDbh->begin_work();
    my $switches_insert_sth = $newDbh->prepare("INSERT INTO sb_switch (name, reboot_server, reboot_serial_port, reboot_board_address) VALUES (?,?,?,?)");

    foreach my $row (@{$switches}) {
        #print join(" ", @{$row})."\n";
        $switches_insert_sth->execute(@{$row});
    }

    $switches_insert_sth->finish();
    $newDbh->commit();
    print "done.\n";

}

sub moveIPinfo {
    my ($oldDbh, $newDbh, $macids) = @_;
    print "Moving IP information :\n";

    if (!$macids) {
        $macids = $newDbh->selectall_hashref("SELECT id, mac_address, date_added FROM mac_list ORDER BY id", "mac_address");
    }

    print "\tGetting data from old database .. ";
    my $ipinfo = $oldDbh->selectall_arrayref("SELECT mac_address,vlan AS vlan_id, ipaddr AS ip_address FROM mac_list t1, xref_macid_ipaddr t2 WHERE t2.macid = t1.id");
    print "done.\n";

    print "\tRemoving data from new database .. ";
    my $deleted = $newDbh->do("DELETE FROM xref_macid_ipaddr");
    print $deleted . " rows deleted.\n";

    print "\tInserting data into new database .. ";
    $newDbh->begin_work();
    my $ipinfo_insert_sth = $newDbh->prepare("INSERT INTO xref_macid_ipaddr (mac_list_id, vlan_id, ip_address) VALUES (?,?,?)");

    foreach my $row (@{$ipinfo}) {
        my $mac_address = $row->[0];
        #print join(" ", @{$row})."\n";
        $ipinfo_insert_sth->execute($macids->{$mac_address}->{id}, $row->[1], $row->[2]);
    }

    $ipinfo_insert_sth->finish();
    $newDbh->commit();
    print "done.\n";
}

sub moveOSinfo {
    my ($oldDbh, $newDbh, $macids) = @_;
    print "Moving OS/PXE/TASK information :\n";

    if (!$macids) {
        $macids = $newDbh->selectall_hashref("SELECT id, mac_address, date_added FROM mac_list ORDER BY id", "mac_address");
    }

    my $os_ids = $newDbh->selectall_hashref("SELECT id, osload FROM os_list", "osload");
    #my $pxe_ids = $newDbh->selectall_hashref("SELECT id, pxefile FROM pxe_list", "pxefile");
    my $task_ids = $newDbh->selectall_hashref("SELECT id, taskfile FROM task_list", "taskfile");

    print "\tGetting data from old database .. ";
    my $osinfo = $oldDbh->selectall_arrayref("SELECT mac_address, osload, task AS taskfile FROM mac_list t1, xref_macid_osload t2, xref_macid_taskid t3, os_list t4, task_list t6 WHERE t2.macid = t1.id AND t3.macid = t1.id AND t4.id = t2.os_list_id AND t6.id = t3.taskid ORDER BY t1.id");
    print "done.\n";

    print "\tRemoving data from new database .. ";
    my $deleted = $newDbh->do("DELETE FROM xref_macid_osload");
    print $deleted . " rows deleted.\n";

    print "\tInserting data into new database .. ";
    $newDbh->begin_work();
    my $osinfo_insert_sth = $newDbh->prepare("INSERT INTO xref_macid_osload (mac_list_id, os_list_id, task_list_id) VALUES (?,?,?)");

    foreach my $row (@{$osinfo}) {
        my ($mac_address, $osload, $taskfile) = @{$row};
        #print join(" ", @{$row})."\n";
        $osinfo_insert_sth->execute($macids->{$mac_address}->{id}, $os_ids->{$osload}->{id}, $task_ids->{$taskfile}->{id});
    }

    $osinfo_insert_sth->finish();
    $newDbh->commit();
    print "done.\n";
}

# MAIN #

#my $dbh1 = DBI->connect("dbi:Pg:dbname=sbks;host=localhost", "kickstart", "l33tNix", { RaiseError => 1, AutoCommit => 1 });
#my $dbh2 = DBI->connect("dbi:Pg:dbname=kickstart;host=localhost", "kickstart", "l33tNix", { RaiseError => 1, AutoCommit => 1 });

my $dbh1 = DBI->connect("dbi:Pg:dbname=sbks;host=66.139.72.209", "kickstart", "l33tNix", { RaiseError => 1, AutoCommit => 1});
my $dbh2 = DBI->connect("dbi:Pg:dbname=kickstart;host=localhost", "kickstart", "l33tNix", { RaiseError => 1, AutoCommit => 1 });

#moveMacList($dbh1, $dbh2);

my $new_macids = $dbh2->selectall_hashref("SELECT id, mac_address FROM mac_list ORDER BY id", "mac_address");
my $new_statids = $dbh2->selectall_hashref("SELECT id, status FROM status_list ORDER BY id", "status");

#moveHardware($dbh1, $dbh2, $new_macids);
#moveStatus($dbh1, $dbh2, $new_macids, $new_statids);
moveStatusHistory($dbh1, $dbh2, $new_macids, $new_statids);
#moveVlans($dbh1, $dbh2);
#moveSwitches($dbh1, $dbh2);
#moveIPinfo($dbh1, $dbh2, $new_macids);
#moveOSinfo($dbh1, $dbh2, $new_macids);

$dbh1->disconnect();
$dbh2->disconnect();
