#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

(-f "/exports/kickstart/mass.txt") || exit 1;

my @macaddrs;
open IFH, "</exports/kickstart/mass.txt";
while (<IFH>) {
	chomp;
	next if ($_ eq "");
	next if (/^#/);
	my $ref = {};
	my @pairs = split(/,/, $_);
	foreach my $pair (@pairs) {
		my ($n, $v) = split(/=/, $pair);
		$ref->{lc($n)} = $v;
	}
	push(@macaddrs, $ref);
}
close IFH;

my $dbh = ks_dbConnect();

foreach my $href (@macaddrs) {
	next unless ($href->{'macaddr'});

	(my $pxemac = $href->{macaddr}) =~ s/:/-/g;
	$pxemac = "01-".$pxemac;
	my $pxetarget = readlink("/tftpboot/pxe/pxelinux.cfg/".$pxemac);

	my $mobj = MACFun->new(dbh => $dbh, macaddr => $href->{'macaddr'});
	if ($href->{'status'}) {
		$mobj->status($href->{'status'});
		if ($href->{'status'} =~ /kickstarted|online/) {
			$mobj->pxe("localboot");
			$mobj->task("sbrescue");
			if (!$pxetarget || $pxetarget ne "localboot") {
				new_update_pxe($href->{'macaddr'}, "localboot");
			}
		}
	}
	if ($href->{'ipaddr'}) { $mobj->ipaddr($href->{'ipaddr'}); }
	if ($href->{'osload'}) { $mobj->osload($href->{'osload'}); }
	if ($href->{pxe}) {
		$mobj->pxe($href->{pxe});
		if (!$pxetarget || $pxetarget ne $href->{pxe}) {
			new_update_pxe($href->{'macaddr'}, $href->{pxe});
		}
	}
	if ($href->{'task'}) { $mobj->task($href->{'task'}); }
	if ($href->{'vlan'}) { $mobj->vlan($href->{'vlan'}); }
	$mobj->update();
}

$dbh->disconnect();

1;
