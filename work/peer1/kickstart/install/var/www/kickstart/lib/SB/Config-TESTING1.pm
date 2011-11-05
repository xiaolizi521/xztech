package SB::Config;

use strict;
use warnings;

BEGIN {
	use Exporter();
	our ( $VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS );

	$VERSION     = 1.02;
	@ISA         = qw(Exporter);
	@EXPORT      = qw($Config $ksdbh);
	%EXPORT_TAGS = ();
	@EXPORT_OK   = qw();
}
our @EXPORT_OK;

our ($Config);

$Config = {
	dc_abbr   => "TESTING",
	dc_number => "1",

	adm_www_host => undef,
	adm_www_user => undef,
	adm_www_pass => undef,

	net_www_host => undef,
	net_www_user => undef,
	net_www_pass => undef,

	ocean_host => undef,
	ocean_user => undef,
	ocean_pass => undef,

	net_db_host => undef,
	net_db_name => undef,
	net_db_user => undef,
	net_db_pass => undef,

	ks_db_host => "127.0.0.1",
	ks_db_name => "kickstart",
	ks_db_user => "kickstart",
	ks_db_pass => "l33tNix",

	ks_home           => "/exports/kickstart",
	ks_domain         => "sat5.testing.peer1.com",
	ks_host           => "ks1.sat5.testing.peer1.com",
	ks_ipaddr         => "10.1.0.2",
        ks_public_ipaddr        => "70.33.255.229",
        ks_public_network       => "70.33.255.224",
        ks_public_netmask       => "255.255.255.224",
        ks_public_gateway       => "70.33.255.225",

	pit_www_host => undef,
	pit_www_user => undef,
	pit_www_pass => undef,

	dhcpconf => "/etc/dhcp3/dhcpd.conf",
	dhcpinit => "/etc/init.d/dhcp3-server",

	ks_pxeconf     => "/tftpboot/pxe/pxelinux.cfg",
	lwp_agent      => "SBKS/0.1 ",
	bootServerMacs => [""]
};

$Config->{'ks_bin'}     = $Config->{'ks_home'} . "/bin";
$Config->{'ks_logs'}    = $Config->{'ks_home'} . "/logs";
$Config->{'ks_state'}   = $Config->{'ks_home'} . "/state";
$Config->{'ks_status'}  = $Config->{'ks_home'} . "/status";
$Config->{'ks_baseurl'} = "http://" . $Config->{'ks_ipaddr'};

if ( $Config->{pit_www_user} and $Config->{pit_www_user} ) {
	$Config->{pit_baseurl} = sprintf(
		"https://%s:%s@%s",
		$Config->{pit_www_user},
		$Config->{pit_www_pass},
		$Config->{pit_www_host}
	);
}
else {
	$Config->{pit_baseurl} = sprintf( "http://%s", $Config->{pit_www_host} );
}

our $ksdbh = "";

1;


