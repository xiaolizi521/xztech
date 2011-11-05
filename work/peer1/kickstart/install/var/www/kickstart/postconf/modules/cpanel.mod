#!/usr/bin/perl -w

use strict;

my $tmpdir = $ks->{sbpost}."/cpanel";
mkdir("$tmpdir");
chdir("$tmpdir") || exit 1;

my $file = "cpanel-ALL-everything.tgz";
my $baseurl = sprintf("http://%s/installs/panels/cpanel", $ks->{ks_ipaddr});
my $fullurl = "$baseurl/$file";

my $postres = lwpfetch(url => $fullurl, file => "$tmpdir/$file");
if (($postres->[0] == 0) && (-f "$tmpdir/$file")) {
	postlog("INFO: Download of $file successful");
} else {
	postlog("FATAL: Download of $file failed");
}

if (system("tar zxf $file") == 0) {
	unlink($file);
} else {
        postlog("FATAL: Unpacking $file failed");
}

chmod(0755, "$tmpdir/installer.sh");
unlink($ks->{scriptd}."/90cpl");
symlink("$tmpdir/installer.sh", $ks->{scriptd}."/90cpl");

1;
