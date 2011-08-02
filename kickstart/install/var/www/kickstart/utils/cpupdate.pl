#!/opt/perl/bin/perl -w

use strict;
use File::Copy;
use LWP::Simple;

# http://layer1.cpanel.net/latest
my $filename = "latest";
my $url = "http://layer1.cpanel.net/$filename";
my $base = "/exports/installs/panels/cpanel";

my $res = head($url);
if ($res->is_success()) {
	my $oldsize = (stat("$base/$filename"))[7];
	my $newsize = $res->content_length();
	print "Old size: $oldsize\n";
	print "New size: $newsize\n";
	if ($newsize != $oldsize) {
		print "Fetching $url\n";
		my $getres = getstore($url, "/tmp/$filename");
		if ($getres == 200) {
			print "Moving $base/$filename to $base/old/$filename\n";
			move("$base/$filename", "$base/old/$filename");
			print "Moving /tmp/$filename to $base/$filename\n";
			move("/tmp/$filename", "$base/$filename");
			chmod(0644, "$base/$filename");
		}
		else {
			print "Download of $filename failed\n";
		}
	}
	else {
		print "No update needed\n";
	}
}
else {
	print "Update check failed\n";
}

1;
