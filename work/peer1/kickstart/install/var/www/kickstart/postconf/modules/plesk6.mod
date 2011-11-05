#!/usr/bin/perl -w

use strict;
use LWP::UserAgent;

my $workdir = $ks->{'sbpost'}."/plesk";
my $licdir = $ks->{'sbpost'}."/licenses";
my $scriptd = $ks->{'sbpost'}."/script.d";
mkdir("$workdir");
chdir("$workdir") || exit 1;

# Check for a Plesk license
opendir DH, "$licdir";
my @licenses = grep { /^PLSK.*\.sh$/ && -s "$licdir/$_" } readdir(DH);
closedir DH;
if (scalar(@licenses) == 0) { postlog("FATAL: Plesk license not found"); }

my $psaver = "6.0.2";
my $file = sprintf("psa-%s-everything.tgz", $ks->{fullprod});
my $baseurl = sprintf("http://%s/installs/panels/plesk/linux/$psaver",
        $ks->{ks_ipaddr});
my $fullurl = "$baseurl/$file";                                
                                                               
my $postres = lwpfetch(url => $fullurl, file => "$workdir/$file");
if (($postres->[0] == 0) && (-f "$workdir/$file")) {
        postlog("INFO: Download of $file successful");
} else {
        postlog("FATAL: Download of $file failed");
}

system("tar","zxf",$file) == 0 ||
	postlog("FATAL: Unpacking installer failed");
unlink($file);

chmod(0755, "$workdir/installer.sh");
symlink("$workdir/installer.sh", $ks->{'scriptd'}."/90psa");

1;
