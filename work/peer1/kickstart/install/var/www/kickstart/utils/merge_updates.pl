#!/opt/perl/bin/perl -w

use strict;
use File::Copy;

sub build_list {
	my $dir = shift();
	my $return = {};
	my $qstring = '"%{NAME} %{VERSION} %{RELEASE} %{ARCH}\n"';
	open IFH, "rpm -qp --queryformat $qstring $dir/*.rpm |";
	while (<IFH>) {
		chomp;
		my($name,$version,$release,$arch) = split(' ', $_);
		$return->{$name}->{'version'} = $version;
		$return->{$name}->{'release'} = $release;
		$return->{$name}->{'arch'} = $arch;
		$return->{$name}->{'rpmname'} = sprintf("%s-%s-%s.%s.rpm", $name, $version, $release, $arch);
		$return->{$name}->{'filename'} = "$dir/".sprintf("%s-%s-%s.%s.rpm", $name, $version, $release, $arch);
	}
	close IFH;
	return $return;
}

my $rhver = $ARGV[0];
($rhver) || die "rhver";

my $base = "/mirrors/redhat/redhat/linux/${rhver}/en/os/i386/RedHat/RPMS";
my $updates = "/mirrors/redhat/redhat/linux/updates/${rhver}";
my $target = "/exports/installs/linux/redhat/${rhver}/RedHat/RPMS";

my $old = build_list($base);
my $new = build_list("$updates/en/os/i386");

foreach my $name (sort(keys %{$old})) {

	# Copy newer package if available
	if (defined($new->{$name})) {
		my $newrpm = $new->{$name}->{'rpmname'};
		print "Source: ".$new->{$name}->{'filename'}."\n";
		print "Target: $target/$newrpm\n";
		#copy($new->{$name}->{'filename'}, "$target/$newrpm");
	}
	else {
		my $oldrpm = $old->{$name}->{'rpmname'};
		print "Source: ".$old->{$name}->{'filename'}."\n";
		print "Target: $target/$oldrpm\n";
		#copy($old->{$name}->{'filename'}, "$target/$oldrpm");
	}
}

my $res = system("/usr/lib/anaconda-runtime/genhdlist /exports/installs/linux/redhat/${rhver}");
print "genhdlist: $res\n";

