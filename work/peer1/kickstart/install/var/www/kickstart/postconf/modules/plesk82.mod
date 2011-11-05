#!/usr/bin/perl -w

use strict;
use LWP::UserAgent;

my $workdir = $ks->{sbpost}."/plesk";
my $licdir = $ks->{sbpost}."/licenses";
my $scriptd = $ks->{sbpost}."/script.d";
mkdir("$workdir");
chdir("$workdir") || exit 1;

my ($release, $product, $version, $arch);

if (-f "/etc/redhat-release") 
{
    $release = `cat /etc/redhat-release`;
    $release =~ /(.*)\ release\ (.*)\ \((.*)\)/;

    # The first match is the operating system type
    $product = $1;
    # The second match is the verstion
    $version = $2;

    # The following is just used to create the product names and the versions
    if ($product =~ /Red Hat/i)
    {   
        $product = "RedHat";
        $version =~ s/(\d).*/el$1/ ;
    }
    elsif ( $product =~ /CentOS/i )
    {
        $product = "CentOS";
        $version =~ s/(\d).*/$1.x/ ;
    }
}
elsif (-f "/etc/debian_version") {
    $product = "Debian";
    $version = `cat /etc/debian_version`;
    chomp($version);
}

if ( `uname -m` =~ /x86_64|amd64/ )
{    
    $arch = "x86_64";
}
else
{   
    $arch = "i386";
}


# Check for a Plesk license
opendir DH, "$licdir";
my @licenses = grep { /^PLSK.*\.(rpm|sh|txt)$/ && -s "$licdir/$_" } readdir(DH);
closedir DH;
if (scalar(@licenses) == 0) { postlog("FATAL: Plesk license not found"); }

my $psaver = "8.2.0";

my $file = sprintf("psa_%s_%s_%s_%s.tgz",$psaver, $product, $version, $arch);
my $baseurl = sprintf("http://%s/installs/panels/plesk/linux/%s",$ks->{ks_ipaddr},$psaver);
my $fullurl = "$baseurl/$file";                                
                                                               
my $postres = lwpfetch(url => $fullurl, file => "$workdir/$file");
if (($postres->[0] == 0) && (-f "$workdir/$file")) {
        postlog("INFO: Download of psa tarball ($file) successful");
} else {
        postlog("FATAL: Download of psa tarball ($file) failed");
}

$file = "installer.sh";
$baseurl = sprintf("http://%s/installs/panels/plesk/linux/$psaver",$ks->{ks_ipaddr});
$fullurl = "$baseurl/$file";                                
                                                               
$postres = lwpfetch(url => $fullurl, file => "$workdir/$file");
if (($postres->[0] == 0) && (-f "$workdir/$file")) {
        postlog("INFO: Download of install script ($file) successful");
} else {
        postlog("FATAL: Download of install script ($file) failed");
}

chmod(0755, "$workdir/installer.sh");
symlink("$workdir/installer.sh", $ks->{'scriptd'}."/90psa");

1;
