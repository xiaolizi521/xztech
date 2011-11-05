#!/usr/bin/perl -w

use strict;
use LWP::Simple;

my $tmpdir = $ks->{sbpost}."/plesk";
mkdir("$tmpdir");
chdir("$tmpdir") || exit 1;

#psa-5.0.5-rh7.2.build030207.16.i586.rpm.tar.gz
#psa-5.0.5-rh7.3.build030207.16.i586.rpm.tar.gz
my $psaver = "5.0.5";
my $baseurl = "http://".$ks->{static}."/installs/panels/plesk/linux/$psaver";
my $archive = "psa-".$psaver."-rh".$ks->{'version'}.".build030207.16.i586.rpm.tar.gz";
my $script = "installer.txt";
my @filelist = ($archive, $script, "serverbeach.tar.gz", "serverbeachlogo.gif");

foreach my $file (@filelist) {
	postlog("INFO: Downloading $baseurl/$file");
	postlog("INFO: Destination $baseurl/$file");	   
	my $postres = lwpfetch('url' => "$baseurl/$file",      
		'file' => "$tmpdir/$file");
	if (($postres->[0] == 0) && (-f "$tmpdir/$file")) {
		postlog("INFO: Download of $file successful");
		next;
	}
	else {
		postlog("INFO: Download of $file failed");
		exit 1;
	}
}

## Write information to a file so the installer script can access it.  Icky.
open FH, ">${tmpdir}/info.sh";
print FH "#Plesk Installer Info
ARCHIVE=$archive
ADMPASS=$ks->{PPASS}
";
close FH;

chmod(0755, "$tmpdir/$script");
symlink("$tmpdir/$script",$ks->{scriptd}."/90psa");

1;
