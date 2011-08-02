#!/usr/bin/perl -w

use strict;
use LWP::Simple;

my $tmpdir = $ks->{'sbpost'}."/webmin";
mkdir("$tmpdir");
chdir("$tmpdir") || exit 1;

my $baseurl = "http://".$ks->{'ks_ipaddr'}."/installs/panels/webmin";
my $script = "installer.sh";
my $keyfile = "jcameron-key.asc";

postlog("INFO: Downloading Webmin Installer");
my $postres = lwpfetch('url' => "$baseurl/$script", 'file' => "$tmpdir/$script");
if (($postres->[0] == 0) && (-f "$tmpdir/$script")) {
	postlog("INFO: Download of Webmin Installer successful");
}
else {
	postlog("INFO: Download of Webmin Installer failed");
	exit 1;
}

postlog("INFO: Downloading Webmin Repo Key");
$postres = lwpfetch('url' => "$baseurl/$keyfile", 'file' => "$tmpdir/$keyfile");
if (($postres->[0] == 0) && (-f "$tmpdir/$keyfile")) {
	postlog("INFO: Download of Webmin Repo Key successful");
}
else {
	postlog("INFO: Download of Webmin Repo Key failed");
	exit 1;
}

chmod(0755, "$tmpdir/$script");
symlink("$tmpdir/$script", $ks->{'scriptd'}."/90web");

1;
