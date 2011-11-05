#!/usr/bin/perl -w

use strict;

my $tmpdir = $ks->{'sbpost'}."/ensim";
my $licdir = $ks->{'sbpost'}."/licenses";
my $scriptd = $ks->{'sbpost'}."/script.d";
mkdir($tmpdir);
chdir($tmpdir) || exit 1;

my $license;
if (! -d $licdir) { postlog("FATAL: License directory not found"); }
else {
	opendir DH, "$licdir";
	my @licenses = grep { /^WPL.*\.lic$/ && -s "$licdir/$_" } readdir(DH);
	closedir DH;
	if (scalar(@licenses) == 0) {
		postlog("FATAL: License file not found");
	}
	else {
		$license = $licenses[0];
	}
}

my $baseurl = "http://".$ks->{static}."/installs/panels/ensim";
my $archive = "wpinstall-313.tgz";
my $script = "installer.txt";
my @filelist = ($archive, $script);

foreach my $file (@filelist) {
	my @args = ("wget","-P","$tmpdir","$baseurl/$file");
	system(@args) == 0 || exit 1;
}

## Write information to a file so the installer script can access it.  Icky.
open FH, ">${tmpdir}/info.sh";
print FH "#Ensim Installer Info
ARCHIVE=$archive
ADMUSER=$ks->{PUSER}
ADMPASS=$ks->{PPASS}
LICENSE=$license
";
close FH;

if (! -d "$scriptd") { system("mkdir","-p","$scriptd"); }

open OFH, ">${scriptd}/90esm";
print OFH '#!/bin/bash

set +e
installer="/usr/local/sbpost/ensim/installer.txt"
[ -e ${installer} ] || exit 127
chmod +x ${installer}
${installer}

# Count the number of errors and exit with that value
ERR=`grep -c ^FATAL /usr/local/sbpost/ensim/sbinstall.log`
exit $ERR

';
close OFH;

1;
