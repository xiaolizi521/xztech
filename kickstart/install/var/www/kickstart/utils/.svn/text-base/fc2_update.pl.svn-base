#!/usr/bin/perl -w

use lib qw(/exports/kickstart/lib);
use strict;
use File::Copy;
require 'rpmvercmp.pm';

sub build_list {
	my $dir = shift();
	my $href = shift();
	my $qstring = '"%{NAME} %{VERSION} %{RELEASE} %{ARCH}\n"';
	open IFH, "rpm -qp --queryformat $qstring $dir/*.rpm |";
	while (<IFH>) {
		chomp;
		my($name,$version,$release,$arch) = split(' ', $_);
		if ($href->{$name}) {
			my $oldver = $href->{$name}->{'version'};
			my $oldrel = $href->{$name}->{'release'};
			#print "$name\t$version\t$release\n";
			my $cmp = rpmvercmp("$version-$release", "$oldver-$oldrel");
			if ($cmp <= 0) { next; }
		}
		$href->{$name}->{'version'} = $version;
		$href->{$name}->{'release'} = $release;
		$href->{$name}->{'arch'} = $arch;
		$href->{$name}->{'rpmname'} = sprintf("%s-%s-%s.%s.rpm", $name, $version, $release, $arch);
		$href->{$name}->{'filename'} = "$dir/".sprintf("%s-%s-%s.%s.rpm", $name, $version, $release, $arch);
	}
	close IFH;
	return 0;
}

my $update_base = "/mirrors/fedora/fedora/2/i386/RPMS.updates";
my $target = "/exports/installs/linux/fedora/core2/Fedora/RPMS";
my $doit = undef;

if (($ARGV[0]) && ($ARGV[0] eq "doit")) {
	$doit = 1;
}

print "Building list for updates\n";
my $new = {};
build_list($update_base, $new);

print "Building list for current\n";
my $old = {};
build_list($target, $old);

#foreach my $name (keys(%{$old})) {
#	print "$old->{$name}->{'filename'}\n";
#}

foreach my $name (keys(%{$new})) {
	#print "$new->{$name}->{'filename'}\n";
	#print "$old->{$name}->{'filename'}\n";
	if (!$old->{$name}) {
		print "Copying $new->{$name}->{'filename'}\n";
		copy($new->{$name}->{'filename'}, $target) if ($doit);
	}
	else {
		my $newver = $new->{$name}->{'version'};
		my $newrel = $new->{$name}->{'release'};
		my $oldver = $old->{$name}->{'version'};
		my $oldrel = $old->{$name}->{'release'};
		my $cmp = rpmvercmp("$newver-$newrel","$oldver-$oldrel");
		if ($cmp == 1) {
			print "Removing $old->{$name}->{'rpmname'}\n";
			unlink($old->{$name}->{'filename'}) if ($doit);
			print "Copying $new->{$name}->{'rpmname'}\n";
			copy($new->{$name}->{'filename'}, $target) if ($doit);
		}
	}
}

if ($doit) {
    print "Running genhdlist .. ";
    system("/usr/lib/anaconda-runtime/genhdlist","/exports/installs/linux/fedora/core2/");
    print "done.\n";
}

exit 0;

