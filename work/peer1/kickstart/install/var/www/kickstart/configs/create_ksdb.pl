#!/usr/bin/perl -w

use strict;
use DBI;

my $dbh;
my $exists;

my $newDbUser = "kickstart";
my $newDbPass = "l33tNix";
my $newDbName = "kickstart";

$dbh = DBI->connect("dbi:Pg:dbname=template1", "", "", { RaiseError => 1, AutoCommit => 1 });

if (($dbh) && ($dbh->ping())) {
	print "Connection to template1 open.\n";
}
else {
	print "Connection failed!\n";
}

my $userExists = $dbh->selectall_arrayref("SELECT count(usename) FROM pg_user WHERE usename = ?", undef, $newDbUser)->[0]->[0];

if ($userExists) {
	print "User exists, skipping creation.\n";
}
else {
	print "Creating kickstart user .. ";
	my $createUserRows = $dbh->do("CREATE USER $newDbUser WITH PASSWORD ?", undef, $newDbPass);
	if ($createUserRows) { print "done.\n"; }
	else {
		print "failed: ".$dbh->errstr."\n";
		$dbh->disconnect();
		exit 1;
	}
}

my $dbExists = $dbh->selectall_arrayref("SELECT count(datname) FROM pg_database WHERE datname = ?", undef, $newDbName)->[0]->[0];

if ($dbExists) {
	print "Database exists, skipping creation.\n";
}
else {
	print "Creating sbks DB .. ";
	my $createDbRows = $dbh->do("CREATE DATABASE $newDbName WITH OWNER $newDbUser");
	if ($createDbRows) { print "done.\n"; }
	else {
		print "failed: ".$dbh->errstr."\n";
		$dbh->disconnect();
		exit 1;
	}
}

$dbh->disconnect();

sub tableExists {
	my $table = shift();

	my $result  = $dbh->selectall_arrayref("SELECT count(tablename) FROM pg_tables WHERE tablename = ?", undef, $table)->[0]->[0];

	$result;
}

$dbh = DBI->connect("dbi:Pg:dbname=$newDbName;host=localhost", "$newDbUser", "$newDbPass", { RaiseError => 1, AutoCommit => 1 });

if (($dbh) && ($dbh->ping())) { print "Connection to $newDbName open.\n"; }
else { print "Connection failed!\n"; }

if (tableExists("xref_macid_osload")) {
	$dbh->do("DROP TABLE xref_macid_osload"); }
if (tableExists("xref_macid_ipaddr")) {
	$dbh->do("DROP TABLE xref_macid_ipaddr"); }
if (tableExists("macid_status_current")) {
	$dbh->do("DROP TABLE macid_status_current"); }
if (tableExists("macid_status_history")) {
	$dbh->do("DROP TABLE macid_status_history"); }
if (tableExists("macid_reboot_history")) {
	$dbh->do("DROP TABLE macid_reboot_history"); }
if (tableExists("macid_product_history")) {
	$dbh->do("DROP TABLE macid_product_history"); }
if (tableExists("macid_error_history")) {
	$dbh->do("DROP TABLE macid_error_history"); }

if (tableExists("sb_datacenter")) { $dbh->do("DROP TABLE sb_datacenter"); }
if (tableExists("sb_switch")) { $dbh->do("DROP TABLE sb_switch"); }
if (tableExists("hardware_list")) { $dbh->do("DROP TABLE hardware_list"); }
if (tableExists("os_list")) { $dbh->do("DROP TABLE os_list"); }
if (tableExists("pxe_list")) { $dbh->do("DROP TABLE pxe_list"); }
if (tableExists("task_list")) { $dbh->do("DROP TABLE task_list"); }
if (tableExists("status_list")) { $dbh->do("DROP TABLE status_list"); }
if (tableExists("hardware")) { $dbh->do("DROP TABLE hardware"); }
if (tableExists("postconf")) { $dbh->do("DROP TABLE postconf"); }
if (tableExists("licenses")) { $dbh->do("DROP TABLE licenses"); }

if (tableExists("vlans")) { $dbh->do("DROP TABLE vlans"); }
if (tableExists("mac_list")) { $dbh->do("DROP TABLE mac_list"); }

# sb_datacenter ( table ) depends ( none )
$dbh->do("CREATE TABLE sb_datacenter ( id serial NOT NULL, name text NOT NULL, dc_abbr text NOT NULL )");
if ($dbh->errstr()) {
	print "sb_datacenter table creation failed: ".$dbh->errstr."\n";
}
else { print "sb_datacenter table creation complete.\n"; }

my @datacenter_list = (
	[ "SAT3 - Paragon", "SAT" ],
	[ "IAD1 - Herndon", "IAD" ],
	[ "IAD2 - Herndon", "IAD2" ],
);
my $datacenter_handle = $dbh->prepare("INSERT INTO sb_datacenter (name, dc_abbr) VALUES (?,?)");
foreach my $aref (@datacenter_list) {
	$datacenter_handle->execute(@{$aref});
}
$datacenter_handle->finish();

# vlans ( table ) depends ( none )
$dbh->do("CREATE TABLE vlans ( id int PRIMARY KEY, public_network cidr, private_network cidr )");
if ($dbh->errstr()) {
	print "vlans table creation failed: ".$dbh->errstr."\n";
}
else { print "vlans table creation complete.\n"; }

## sb_switch ( table ) depends ( vlans )
#$dbh->do("CREATE TABLE sb_switch ( id serial NOT NULL, name text NOT NULL, vlan_id int REFERENCES vlans(id) ON DELETE SET NULL ON UPDATE CASCADE, reboot_server inet, reboot_serial_port integer, reboot_board_address integer )");
#if ($dbh->errstr()) {
#	print "sb_switch table creation failed: ".$dbh->errstr."\n";
#}
#else { print "sb_switch table creation complete.\n"; }

# hardware_list ( table ) depends ( none )
$dbh->do("CREATE TABLE hardware_list ( id serial NOT NULL, part_type text NOT NULL, part_name text NOT NULL )");
if ($dbh->errstr()) {
	print "hardware_list table creation failed: ".$dbh->errstr."\n";
}
else { print "hardware_list table creation complete.\n"; }

my @hardware_list = (
	[ "cpu_model", "amd duron(tm) processor" ],
	[ "cpu_model", "amd athlon(tm) xp 2100" ],
	[ "cpu_model", "amd athlon(tm) xp 2200" ],
	[ "cpu_model", "amd athlon(tm) xp 2600" ],
	[ "cpu_model", "amd athlon(tm) xp 2800" ],
	[ "cpu_model", "amd athlon(tm) xp 3000" ],
	[ "cpu_model", "amd athlon(tm) mp 2600" ],
	[ "cpu_model", "amd athlon(tm) mp 2800" ],
	[ "hdd_model", "hds722580vlat20" ],
	[ "hdd_model", "ic35l090avv207-0" ],
	[ "hdd_model", "maxtor 6e040l0" ],
	[ "hdd_model", "maxtor 6y060l0" ],
	[ "hdd_model", "maxtor 6y080l0" ],
	[ "hdd_model", "st360015a" ]
);
my $hardware_handle = $dbh->prepare("INSERT INTO hardware_list (part_type, part_name) VALUES (?,?)");
foreach my $aref (@hardware_list) {
	$hardware_handle->execute(@{$aref});
}
$hardware_handle->finish();

# os_list ( table ) depends ( none )
$dbh->do("CREATE TABLE os_list ( id serial PRIMARY KEY, osload text NOT NULL, is_ks boolean DEFAULT 'f' NOT NULL )");
if ($dbh->errstr()) {
	print "os_list table creation failed: ".$dbh->errstr."\n";
}
else { print "os_list table creation complete.\n"; }

my @os_list = (
	[ "localboot", "f" ],
	[ "burnin", "f" ],
	[ "wait", "f" ],
	[ "zerofill", "f" ],
	[ "sbrescue", "f" ],
	[ "rhrescue", "f" ],
	[ "ghost", "f" ],
	[ "memtest", "f" ],
	[ "rh72ins", "f" ],
	[ "rh72ks", "t" ], 
	[ "rh72esm", "t" ],
	[ "rh73ins", "f" ],
	[ "rh73ks", "t" ], 
	[ "rh80ins", "f" ],
	[ "rh80ks", "t" ], 
	[ "rh9ins", "f" ], 
	[ "rh9ks", "t" ],  
	[ "rhel3ins", "f" ],
	[ "rhel3ks", "t" ],
	[ "fc1ks", "t" ],  
	[ "fc2ks", "t" ],  
	[ "deb30ks", "t" ],
	[ "win2k", "t" ],  
	[ "win2k3std", "t" ],
	[ "win2k3web", "t" ],
	[ "beta2k", "t" ],
	[ "beta2k3std", "t" ],
	[ "beta2k3web", "t" ],
	[ "default", "f" ],
	[ "fc3ks", "t" ],
	[ "rhel4ks", "t" ],
);
my $os_handle = $dbh->prepare("INSERT INTO os_list (osload, is_ks) VALUES (?,?)");
foreach my $aref (@os_list) {
	$os_handle->execute(@{$aref});
}
$os_handle->finish();

# pxe_list ( table ) depends ( none )
$dbh->do("CREATE TABLE pxe_list ( id serial PRIMARY KEY, pxefile text NOT NULL )");
if ($dbh->errstr()) {
	print "pxe_list table creation failed: ".$dbh->errstr."\n";
}
else { print "pxe_list table creation complete.\n"; }

my @pxe_list = qw( ghost localboot memtest rh72esm rh72ins rh72ks rh73ins rh73ks rh80ins rh80ks rh9ins rh9ks rhel3ins rhel3ks rhrescue sbrescue win2k-itl win2k-rtl win2k3std-itl win2k3std-rtl win2k3web-itl win2k3web-rtl fc1ks fc2ks fc3ks rhel4ks );
my $pxe_handle = $dbh->prepare("INSERT INTO pxe_list (pxefile) VALUES(?)");
foreach my $pxefile (@pxe_list) {
	$pxe_handle->execute($pxefile);
}
$pxe_handle->finish();

# task_list ( table ) depends ( none )
$dbh->do("CREATE TABLE task_list ( id serial PRIMARY KEY, taskfile text NOT NULL )");
if ($dbh->errstr()) {
	print "task_list table creation failed: ".$dbh->errstr."\n";
}
else { print "task_list table creation complete.\n"; }

my @task_list = qw( audit beta2k-copy bootserver burnin deb30ks default sbrescue waitmode win2k-copy win2k3std-copy win2k3web-copy zerofill remoterescue );
my $task_handle = $dbh->prepare("INSERT INTO task_list (taskfile) VALUES(?)");
foreach my $taskfile (@task_list) {
	$task_handle->execute($taskfile);
}
$task_handle->finish();

# status_list ( table ) depends ( none )
$dbh->do("CREATE TABLE status_list ( id int PRIMARY KEY, status text NOT NULL, is_fail boolean DEFAULT 'f' )");
if ($dbh->errstr()) {
	print "status_list table creation failed: ".$dbh->errstr."\n";
}
else { print "status_list table creation complete.\n"; }

my @status_list = (
	[ "1", "new", "f" ],
	[ "2", "burnin", "f" ],
	[ "3", "burnin_done", "f" ],
	[ "4", "zerofill", "f" ],
	[ "5", "zerodone", "f" ],
	[ "6", "audit", "f" ],
	[ "7", "audit_done", "f" ],
	[ "8", "ready", "f" ],
	[ "9", "wait", "f" ],
	[ "10", "updateks", "f" ],
	[ "11", "reboot", "f" ],
	[ "12", "booting", "f" ],
	[ "13", "ksscript", "f" ],
	[ "14", "postconf", "f" ],
	[ "15", "licenses", "f" ],
	[ "16", "cpl_wait", "f" ],
	[ "17", "esm_wait", "f" ],
	[ "18", "psa_wait", "f" ],
	[ "19", "ks_wait", "f" ],
	[ "20", "win2k_part", "f" ],
	[ "21", "win2k_partdone", "f" ],
	[ "22", "win2k_copy", "f" ],
	[ "23", "win2k_copydone", "f" ],
	[ "24", "win2k_preinst", "f" ],
	[ "25", "win2k_inst", "f" ],
	[ "26", "win2k_post", "f" ],
	[ "27", "postboot", "f" ],
	[ "59", "kickstarted", "f" ],
	[ "60", "online", "f" ],
	[ "64", "burnin_fail", "t" ],
	[ "65", "zero_fail", "t" ],
	[ "66", "audit_fail", "t" ],
	[ "70", "updateks_fail", "t" ],
	[ "71", "reboot_fail", "t" ],
	[ "72", "booting_fail", "t" ],
	[ "73", "ksscript_fail", "t" ],
	[ "74", "postconf_fail", "t" ],
	[ "75", "licenses_fail", "t" ],
	[ "76", "cpl_fail", "t" ],
	[ "77", "esm_fail", "t" ],
	[ "78", "psa_fail", "t" ],
	[ "80", "win2k_partfail", "t" ],
	[ "82", "win2k_copyfail", "t" ],
	[ "120", "ksfail", "t" ],
	[ "121", "online_reboot", "f" ],
	[ "122", "online_rescue", "f" ],
	[ "181", "online_reboot_fail", "t" ],
	[ "182", "online_rescue_fail", "t" ],
	[ "253", "holding", "f" ],
	[ "254", "bootserver", "f" ],
	[ "255", "retired", "f" ]
);
my $status_handle = $dbh->prepare("INSERT INTO status_list (id, status, is_fail) VALUES (?,?,?)");
foreach my $aref (@status_list) {
	$status_handle->execute(@{$aref})
}
$status_handle->finish();

# mac_list ( table ) depends ( none )
$dbh->do("CREATE TABLE mac_list ( id serial NOT NULL PRIMARY KEY, mac_address macaddr NOT NULL, date_added timestamp with time zone DEFAULT now() )");
if ($dbh->errstr()) {
	print "mac_list table creation failed: ".$dbh->errstr."\n";
}
else { print "mac_list table creation complete.\n"; }

# hardware ( table ) depends ( mac_list )
$dbh->do("CREATE TABLE hardware ( mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, param text NOT NULL, value text NOT NULL )");
if ($dbh->errstr()) {
	print "hardware table creation failed: ".$dbh->errstr."\n";
}
else { print "hardware table creation complete.\n"; }

# postconf ( table ) depends ( mac_list )
$dbh->do("CREATE TABLE postconf ( mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, param text NOT NULL, value text NOT NULL )");
if ($dbh->errstr()) {
	print "postconf table creation failed: ".$dbh->errstr."\n";
}
else { print "postconf table creation complete.\n"; }

# licenses ( table ) depends ( mac_list )
$dbh->do("CREATE TABLE licenses ( mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, licenses text NOT NULL )");
if ($dbh->errstr()) {
	print "licenses table creation failed: ".$dbh->errstr."\n";
}
else { print "licenses table creation complete.\n"; }

# xref_macid_osload ( table ) depends ( mac_list, os_list, pxe_list, task_list )
$dbh->do("CREATE TABLE xref_macid_osload ( mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, os_list_id int REFERENCES os_list(id) ON DELETE SET NULL ON UPDATE CASCADE, pxe_list_id int REFERENCES pxe_list(id) ON DELETE SET NULL ON UPDATE CASCADE, task_list_id int REFERENCES task_list(id) ON DELETE SET NULL ON UPDATE CASCADE )");
if ($dbh->errstr()) {
	print "xref_macid_osload table creation failed: ".$dbh->errstr."\n";
}
else { print "xref_macid_osload table creation complete.\n"; }

# xref_macid_ipaddr ( table ) depends ( mac_list, vlans )
$dbh->do("CREATE TABLE xref_macid_ipaddr ( mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, vlan_id int REFERENCES vlans(id) ON DELETE SET NULL ON UPDATE CASCADE, ip_address inet DEFAULT '0.0.0.0' NOT NULL )");
if ($dbh->errstr()) {
	print "xref_macid_ipaddr table creation failed: ".$dbh->errstr."\n";
}
else { print "xref_macid_ipaddr table creation complete.\n"; }

# macid_status_current ( table ) depends ( mac_list, status_list )
$dbh->do("CREATE TABLE macid_status_current ( mac_list_id int PRIMARY KEY REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, old_status_id int REFERENCES status_list(id) ON DELETE SET NULL ON UPDATE CASCADE, new_status_id int REFERENCES status_list(id) ON DELETE SET NULL ON UPDATE CASCADE, date_added timestamp with time zone DEFAULT now() )");
if ($dbh->errstr()) {
	print "macid_status_current table creation failed: ".$dbh->errstr."\n";
}
else { print "macid_status_current table creation complete.\n"; }

# macid_status_history ( table ) depends ( mac_list, status_list )
$dbh->do("CREATE TABLE macid_status_history ( id serial PRIMARY KEY, mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, old_status_id int REFERENCES status_list(id) ON DELETE SET NULL ON UPDATE CASCADE, new_status_id int REFERENCES status_list(id) ON DELETE SET NULL ON UPDATE CASCADE, date_added timestamp with time zone DEFAULT now() )");
if ($dbh->errstr()) {
	print "macid_status_history table creation failed: ".$dbh->errstr."\n";
}
else { print "macid_status_history table creation complete.\n"; }

# macid_reboot_history ( table ) depends ( mac_list )
$dbh->do("CREATE TABLE macid_reboot_history ( id serial PRIMARY KEY, mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, reboot_status text, date_added timestamp with time zone DEFAULT now() )");
if ($dbh->errstr()) {
	print "macid_reboot_history table creation failed: ".$dbh->errstr."\n";
}
else { print "macid_reboot_history table creation complete.\n"; }

# macid_error_history ( table ) depends ( mac_list, status_list )
$dbh->do("CREATE TABLE macid_error_history ( id serial PRIMARY KEY, mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, old_status_id int REFERENCES status_list(id) ON DELETE CASCADE ON UPDATE CASCADE, new_status_id int REFERENCES status_list(id) ON DELETE CASCADE ON UPDATE CASCADE, error_message text, date_added timestamp with time zone DEFAULT now() )");
if ($dbh->errstr()) {
	print "macid_error_history table creation failed: ".$dbh->errstr."\n";
}
else { print "macid_error_history table creation complete.\n"; }

# macid_product_history ( table ) depends ( mac_list )
$dbh->do("CREATE TABLE macid_product_history ( id serial PRIMARY KEY, mac_list_id int REFERENCES mac_list(id) ON DELETE CASCADE ON UPDATE CASCADE, product text, date_added timestamp with time zone DEFAULT now() )");
if ($dbh->errstr()) {
	print "macid_product_history table creation failed: ".$dbh->errstr."\n";
}
else { print "macid_product_history table creation complete.\n"; }

# CREATE VIEW kickstart_map (mac_list_id, mac_address, os_list_id, osload, pxe_list_id, pxefile, task_list_id, taskfile, vlan_id, ip_address, old_status_id, old_status, new_status_id, new_status, last_update) AS SELECT t1.id, t1.mac_address, t2.os_list_id, t5.osload, t2.pxe_list_id, t6.pxefile, t2.task_list_id, t7.taskfile, t3.vlan_id, t3.ip_address, t4.old_status_id, t8.status, t4.new_status_id, t9.status, t4.date_added FROM mac_list t1, xref_macid_osload t2, xref_macid_ipaddr t3, macid_status_current t4, os_list t5, pxe_list t6, task_list t7, status_list t8, status_list t9 WHERE (t2.mac_list_id = t1.id AND t5.id = t2.os_list_id AND t6.id = t2.pxe_list_id AND t7.id = t2.task_list_id) AND t3.mac_list_id = t1.id AND (t4.mac_list_id = t1.id AND t8.id = t4.old_status_id AND t9.id = t4.new_status_id)
#
$dbh->disconnect();
