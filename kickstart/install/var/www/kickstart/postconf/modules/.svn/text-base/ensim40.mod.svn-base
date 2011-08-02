#!/usr/bin/perl -w

# How to run me!
# perl -e 'my $ks={}; $ks->{static}="10.11.100.2"; $ks->{PUSER}="delmendo"; $ks->{PPASS}="password"; eval `cat /tmp/ensim35.mod`'

use strict;

my $tmpdir = $ks->{sbpost}."/ensim";
my $licdir = $ks->{sbpost}."/licenses";
my $scriptd = $ks->{sbpost}."/script.d";
mkdir($tmpdir);
chdir($tmpdir) || exit 1;

#opendir DH, "$licdir";
#my @licenses = grep { /^WPL.*\.lic$/ && -s "$licdir/$_" } readdir(DH);
#closedir DH;
#if (scalar(@licenses) == 0) { postlog("FATAL: Ensim license not found"); }

my $file = sprintf("ensim-%s-everything.tgz", $ks->{fullprod});
my $baseurl = sprintf("http://%s/installs/panels/ensim/linux/4.0",
	$ks->{ks_ipaddr});
my $fullurl = "$baseurl/$file";

my $postres = lwpfetch(url => $fullurl, file => "$tmpdir/$file");
if (($postres->[0] == 0) && (-f "$tmpdir/$file")) {
	postlog("INFO: Download of $file successful");
} else {
	postlog("FATAL: Download of $file failed");
}

postlog("INFO; Unpacking $file");
if (system("tar zxf $file") == 0) {
	postlog("INFO; Unpacking $file complete");
	unlink($file);
} else {
	postlog("FATAL: Unpacking $file failed");
}

chmod(0755, "$tmpdir/installer.sh");
symlink("$tmpdir/installer.sh", $ks->{scriptd}."/90esm");

1;
