#!/usr/bin/perl -w

use strict;


# so we get a array with the ip address, netmask, and interface
sub add_sec_ip_deb {
    my $ipaddr = shift;
    my $netmask = shift;
    my $iface = shift; 

    my $intfmt = 
"
# configured with secipaddr.mod
auto %s
iface %s inet static
    address %s
    netmask %s

";

    open OFH, ">>/etc/network/interfaces";
        printf OFH $intfmt, $iface, $iface, $ipaddr, $netmask; 
    close OFH;
    
    # this can get changed to ifup $iface 
    #system("/etc/init.d/networking restart");

    1;
}

sub add_sec_ip_rh {
    my $ipaddr = shift;
    my $netmask = shift;
    my $iface = shift; 
    my $hwaddr = &getmac($iface); 


    my $intfmt =
"DEVICE=%s
IPADDR=%s
NETMASK=%s
ONBOOT=yes
HWADDR=%s
";
    
    open OFH, ">/etc/sysconfig/network-scripts/ifcfg-$iface";
        printf OFH $intfmt, $iface, $ipaddr, $netmask, $hwaddr; 
    close OFH;
   	
    #system("/etc/init.d/network restart");

    1;
}

my $secip = $ks->{SECIP} ; 
my $secnm = $ks->{SECNM} ; 

if ( $secip eq "none" ) { 
    postlog( "FATAL: SECIP misconfigured esclate", &getmac(&find_pri_nic) );
    exit 3; 
} elsif ( $secnm eq "none" ) { 
    postlog( "FATAL: SECNM misconfigured esclate", &getmac(&find_pri_nic) );
    exit 3;
} else { 
    my $iface_sec = &find_sec_nic ; 
    my $iface_pri = &find_pri_nic ; 
    our $mac = &getmac($iface_pri); 
    postlog("INFO: mac for primary NIC $iface_pri is $mac");
    # below ends up being the error message you see when it is busted
    postlog("INFO: secondary NIC $iface_sec ");

    if ( $iface_sec ) { 
        if (-f "/etc/debian_version") {
            add_sec_ip_deb($secip,$secnm,$iface_sec);
        } 
        elsif (-f "/etc/redhat-release") {
            add_sec_ip_rh($secip,$secnm,$iface_sec);
        } else {
            postlog("FATAL: OS not supported for private net", $mac);
            exit 3 ; 
        }
    } else { 
        postlog("FATAL: NO secondary interface plugged in", $mac); 
        exit 3 ; 
    }
}
