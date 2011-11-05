#!/usr/bin/perl -w

use strict;

sub add_ips_deb {
    my @ipaddrs = @_;
    my $count = 0;
    my @ifaces;

    my $intfmt = 
"auto eth0:%d
iface eth0:%d inet static
    address %s
    netmask 255.255.255.255

";

    open IFH, "</etc/network/interfaces";
    while (<IFH>) {
        if (/iface eth0:(\d+)/) {
            my $last = $1;
            if ($last >= $count) { $count = $last + 1; }
        }
    }
    close IFH;

    open OFH, ">>/etc/network/interfaces";
    foreach my $ipaddr (@ipaddrs) {
        printf OFH $intfmt, $count, $count, $ipaddr;
        $count++;
    }
    close OFH;

    system("/etc/init.d/networking restart");

    1;
}

sub add_ips_rh {
    my @ipaddrs = @_;
    my $count = 0;

    my $devfmt =
"DEVICE=eth0:%d
IPADDR=%s
NETMASK=255.255.255.255
ONBOOT=yes
";

    opendir(DH, "/etc/sysconfig/network-scripts/");
    my @cfgs = grep(/ifcfg-eth0:\d+/, readdir(DH));
    closedir(DH);

    foreach (@cfgs) {
        /ifcfg-eth0:(\d+)/;
        my $last = $1;
        if ($last >= $count) { $count = $last + 1; }
    }

    foreach my $ipaddr (@ipaddrs) {
        open CFG, ">/etc/sysconfig/network-scripts/ifcfg-eth0:$count";
        printf CFG $devfmt, $count, $ipaddr;
        close CFG;
        $count++;
    }

    my $flag;
    open IFH, "</etc/sysconfig/network-scripts/ifcfg-eth0";
    open OFH, ">/tmp/ifcfg-eth0";
    while (<IFH>) {
        if (/NOALIASROUTING=/) {
            print OFH "NOALIASROUTING=yes\n";
            $flag = 1;
        }
        else {
            print OFH $_;
        }
    }
    close IFH;

    if (!$flag) { print OFH "NOALIASROUTING=yes\n"; }
    close OFH;
    unlink("/etc/sysconfig/network-scripts/ifcfg-eth0");
    rename("/tmp/ifcfg-eth0","/etc/sysconfig/network-scripts/ifcfg-eth0");

    system("/etc/init.d/network restart");

    1;
}

# This is for testing ..
#my $ks = {
#    IPADDR => "69.44.57.7",
#    CFG_IPADDR1 => "69.44.152.0",
#    CFG_IPADDR2 => "69.44.152.1",
#    CFG_IPADDR3 => "69.44.152.2"
#};

# First, check if we have additional IP addresses to configure

my @ipkeys = grep(/CFG_IPADDR\d+/, sort(keys(%$ks)));
if (scalar(@ipkeys) < 1) { return 1; }

my @ipaddrs;
foreach (@ipkeys) {
    push(@ipaddrs, $ks->{$_});
}

if (-f "/etc/debian_version") {
    add_ips_deb(@ipaddrs);
}
elsif (-f "/etc/redhat-release") {
    add_ips_rh(@ipaddrs);
}
else {
    exit 1;
}

1;
