#!/usr/bin/perl -w

use strict;
use LWP::UserAgent;

my $workdir = $ks->{sbpost}."/gpgpu";
my $scriptd = $ks->{sbpost}."/script.d";

mkdir("$workdir");

chdir("$workdir") || exit 1;

my ($arch);

if ( `uname -m` =~ /x86_64|amd64/ )
{    
    my $file = "gpgpu.sh";
    my $baseurl = sprintf("http://%s/installs/gpgpu/",$ks->{ks_ipaddr});
    my $fullurl = "$baseurl/$file";                                
                                                                   
    my $postres = lwpfetch(url => $fullurl, file => "$workdir/$file");
    if (($postres->[0] == 0) && (-f "$workdir/$file")) {
            postlog("INFO: Download of GPGPU installation script successful");
    } else {
            postlog("FATAL: Download of GPGPU installation script ($file) failed");
    }

    chmod(0755, "$workdir/gpgpu.sh");
    symlink("$workdir/gpgpu.sh", $ks->{'scriptd'}."/90gpgpu");
}
else {
    postlog("FATAL: GPGPU is only compatible with 64bit systems.");
}

1;