#!/usr/bin/perl -wT

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my ($dbh);

# Name: vinfo
# Description: take a row of data as an arrayref and create a hash of values.
# Returns: hashref ($h)
sub vinfo {
	my $i = shift();
	my $h = {};
	$h->{'vlan'} = $i->[0];
	$h->{'network'} = $i->[1];
	$h->{'netmask'} = $i->[2];
	$h->{'bcast'} = $i->[3];

	$i->[1] =~ /(.*)\.(\d{1,3})$/;
	my $first = $1; my $last = $2;
	$h->{'gateway'} = "$first.".($2 + 2);
	$h->{'tftpserver'} = "$first.".($2 + 2);
	$h->{'ksserver'} = $Config->{'ks_ipaddr'};
	if ($i->[0] == 405) {
		$h->{'first'} = "$first.".($2 + 100);
	}
	else {
		$h->{'first'} = "$first.".($2 + 4);
	}

	$i->[3] =~ /(.*)\.(\d{1,3})$/;
	$first = $1; my $blast = $2;
	$h->{'last'} = "$first.".($blast - 1);

	return $h;
}

# Name: mkvlan
# Description: take a hashref of values and print corresonding dhcpd.conf info
# Returns: 0 on success, 1 on failure
sub mkvlan {
	my $i = shift();
	print CONF qq|
# VLAN$i->{vlan}
subnet $i->{network} netmask $i->{netmask} \{
	authoritative;
	range dynamic-bootp $i->{first} $i->{last};
	option broadcast-address $i->{bcast};
	option subnet-mask $i->{netmask};
	option routers $i->{gateway};
	option www-server $i->{ksserver};
	option kickstart-server $i->{ksserver};
	option netbios-name-servers $i->{ksserver};

	if substring (option vendor-class-identifier, 0, 9) = "PXEClient" \{
		site-option-space "pxelinux";
		option pxelinux.magic f1:00:74:7e;
		option pxelinux.configfile "pxelinux.cfg/sbrescue";
		option pxelinux.pathprefix "/pxe/";
		option pxelinux.reboottime 60;
		next-server $Config->{ks_ipaddr};
		option tftp-server-name "$Config->{ks_ipaddr}";
		if exists dhcp-parameter-request-list \{
			append dhcp-parameter-request-list 208, 209, 210, 211;
		\}
		filename "/pxe/pxelinux.0";
	\}
\} # END VLAN$i->{vlan}
|;
} # END mkvlan

# MAIN
$dbh = ks_dbConnect();

if ((scalar(@ARGV) == 1) && ($ARGV[0] eq '-f')) {
        open CONF, ">$Config->{'dhcpconf'}";
}
elsif ((scalar(@ARGV) == 1) && ($ARGV[0] eq '-t')) {
        open CONF, ">$Config->{'dhcpconf'}.new";
}
else {  
        open CONF, ">-";
}

# Print static header
print CONF "log-facility local5;
default-lease-time 14400;
max-lease-time 14400;
ddns-update-style none;
one-lease-per-client on;
deny duplicates;
option domain-name \"$Config->{'ks_domain'}\";
option netbios-node-type 2;
option ntp-servers $Config->{'ks_ipaddr'};
option domain-name-servers $Config->{'ks_ipaddr'};
option kickstart-server code 200 = ip-address;

## PXE MAGIC STUFF ##
option space pxelinux;
option pxelinux.magic           code 208 = string;
option pxelinux.configfile      code 209 = text;
option pxelinux.pathprefix      code 210 = text;
option pxelinux.reboottime      code 211 = unsigned integer 32;
#####################

subnet $Config->{'ks_public_network'} netmask $Config->{'ks_public_netmask'} {
	not authoritative;
}
";

# Print dynamic VLAN information
my $qry1 = "SELECT
	id,
	host(network(private_network)) AS network,
	host(netmask(private_network)) AS netmask,
	host(broadcast(private_network)) AS bcast
FROM
	vlans
WHERE
	private_network IS NOT NULL
AND
	id != 1
ORDER BY
	id ASC
";
my $sth1 = $dbh->prepare($qry1);
$sth1->execute();
while (my $row = $sth1->fetchrow_arrayref()) {
	# hash reference
	next if ($row->[0] == 0);
	my $h = vinfo($row);
	mkvlan($h);
	#push(@vlist, $h);
}
$sth1->finish();

# Print rest of static information
print CONF q|
host booter1.lax1 {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/booter";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 00:0e:0c:9c:c6:ce;
        fixed-address 10.5.0.10;
}
host booter1.iad2 {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/bootserver-power";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 0:2:b3:ca:5:25;
        fixed-address 10.3.0.10;
}
host booter1.sat3 {
	hardware ethernet 0:50:70:31:2b:ba;
	fixed-address 192.168.201.10;
}
host booter2.sat3 {
	hardware ethernet 0:50:70:31:1e:b9;
	fixed-address 192.168.201.20;
}
host booter3.sat3 {
	hardware ethernet 00:02:b3:48:46:32;
	fixed-address 192.168.201.30;
}

host booter1.stg1 {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/bootserver-power";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 00:0D:61:80:7C:98;
        fixed-address 10.11.0.10;
}

host booter1.sat5 {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/bootserver-power";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 00:30:48:52:6d:70; 
        fixed-address 10.7.0.10;
}

host booter2.sat5 {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/booter";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 00:30:48:53:05:80;
        fixed-address 10.7.0.11;
}

host booter3.sat5 {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/bootserver-power2";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 00:0d:61:80:7b:3e;
        fixed-address 10.7.0.15;
}

host booter1.sat5.testing.peer1.com {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/booter-testing";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 00:0d:61:80:7c:98;
        fixed-address 10.1.0.10;
}


host booter1.dev1 {
        site-option-space "pxelinux";
        option pxelinux.magic f1:00:74:7e;
        option pxelinux.configfile "pxelinux.cfg/new-booter";
        option pxelinux.pathprefix "/pxe/";
        option pxelinux.reboottime 60;
        filename "/pxe/pxelinux.0";
        hardware ethernet 00:0d:61:80:7d:66;
        fixed-address 10.6.0.10;
}


group {
	one-lease-per-client on;
	deny duplicates;
	site-option-space "pxelinux";
	option pxelinux.magic f1:00:74:7e;
	option pxelinux.configfile "";
	#option pxelinux.configfile =
	#	concat("pxelinux.cfg/",binary-to-ascii(16,8,":",hardware));
	option pxelinux.pathprefix "/pxe/";
	option pxelinux.reboottime 60;
	filename "/pxe/pxelinux.0";

	#append dhcp-parameter-request-list 200;
|;

# Print all the known mac addresses
my $qry2 = "SELECT mac_address
FROM mac_list
WHERE mac_address != '00:02:b3:ca:05:25' ORDER BY id ASC";
my $sth2 = $dbh->prepare($qry2);
$sth2->execute();
while (my @row = $sth2->fetchrow_array()) {
	my $host = my $mac = $row[0];
	$host =~ s/://g;
	next if ($host eq "000000000000");
	#$mac = clean_mac($mac);
	print CONF "\thost $host { hardware ethernet $mac; }\n";
}
$sth2->finish();
print CONF "}\n";
close CONF;

$dbh->disconnect();
