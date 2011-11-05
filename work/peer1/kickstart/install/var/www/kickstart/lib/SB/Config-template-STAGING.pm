package SB::Config;

use strict;
use warnings;

BEGIN {
	use Exporter();
	our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

	$VERSION = 1.00;
	@ISA = qw(Exporter);
	@EXPORT = qw($Config $ksdbh);
	%EXPORT_TAGS = ();
	@EXPORT_OK = qw();
}
our @EXPORT_OK;

our($Config);

$Config = {
    dc_abbr => "@@DC_ABBR@@",
    dc_number => "@@DC_NUMBER@@",

    adm_www_host => "ocean.staging.serverbeach.com",
    adm_www_user => "kickstart",
    adm_www_pass => "l33tNix",

    net_www_host => "netadmin.staging.serverbeach.com",
    net_www_user => undef, 
    net_www_pass => undef,

    ocean_host  => "ocean.staging.serverbeach.com",
    ocean_user  => "kickstart",
    ocean_pass  => "l33tNix",

    pit_www_host => "ocean.staging.serverbeach.com/provisioneer",
    pit_www_user => "kickstart",
    pit_www_pass => "l33tNix",

    net_db_host	=> "netadmin.staging.serverbeach.com",
    net_db_name	=> "serverbeach",
    net_db_user	=> "sb_user",
    net_db_pass	=> undef,

	ks_db_host	=> "127.0.0.1",
	ks_db_name	=> "kickstart",
	ks_db_user	=> "kickstart",
	ks_db_pass	=> "l33tNix",

	ks_home		=> "/exports/kickstart",
	ks_domain	=> "kslan.@@DC_ABBR@@.serverbeach.net",
	ks_host		=> "ks1.@@DC_ABBR@@.kslan.serverbeach.net",
	ks_ipaddr	=> "10.@@DC_NUMBER@@.0.2",
	ks_public_ipaddr	=> "@@IPADDR@@",
	ks_public_network	=> "@@NETWORK@@",
	ks_public_netmask	=> "@@NETMASK@@",
	ks_public_gateway	=> "@@GATEWAY@@",

    dhcpconf => "/etc/dhcp3/dhcpd.conf",
    dhcpinit => "/etc/init.d/dhcp3-server",

    ks_pxeconf => "/tftpboot/pxe/pxelinux.cfg",
    lwp_agent	=> "SBKS/0.1 ",
    bootServerMacs => [ "" ]
};

$Config->{'ks_bin'} = $Config->{'ks_home'}."/bin";      
$Config->{'ks_logs'} = $Config->{'ks_home'}."/logs";    
$Config->{'ks_state'} = $Config->{'ks_home'}."/state";
$Config->{'ks_status'} = $Config->{'ks_home'}."/status";
$Config->{'ks_baseurl'} = "http://".$Config->{'ks_ipaddr'};

if ($Config->{adm_www_user} and $Config->{adm_www_pass}) {
    $Config->{adm_baseurl} = sprintf("https://%s:%s@%s",
    $Config->{adm_www_user}, $Config->{adm_www_pass}, $Config->{adm_www_host});
}
else {
    $Config->{adm_baseurl} = sprintf("http://%s", $Config->{adm_www_host});
}

if (($Config->{net_www_user}) && ($Config->{net_www_pass})) {
    $Config->{net_baseurl} = sprintf("http://%s:%s@%s",
    $Config->{net_www_user}, $Config->{net_www_pass}, $Config->{net_www_host});
}
else {
    $Config->{net_baseurl} = sprintf("http://%s", $Config->{net_www_host});
}

if (($Config->{ocean_user}) && ($Config->{ocean_pass})) {
    $Config->{ocean_baseurl} = sprintf("https://%s:%s@%s",
        $Config->{ocean_user}, $Config->{ocean_pass}, $Config->{ocean_host});
}
else {
    $Config->{ocean_baseurl} = sprintf("http://%s", $Config->{ocean_host});
}

if ($Config->{pit_www_user} and $Config->{pit_www_user}) {
    $Config->{pit_baseurl} = sprintf("https://%s:%s@%s",
    $Config->{pit_www_user}, $Config->{pit_www_pass}, $Config->{pit_www_host});
}
else {
    $Config->{pit_baseurl} = sprintf("http://%s", $Config->{pit_www_host});
}

$Config->{portctl_cgi} = $Config->{net_baseurl}."/cgi-bin/port_control.cgi";
$Config->{vlanctl_cgi} = $Config->{net_baseurl}."/cgi-bin/vlan_control.cgi";
$Config->{switchctl_cgi} = $Config->{net_baseurl}."/cgi-bin/switch_control.cgi";

our $ksdbh = "";

1;

