#!/usr/bin/perl -w

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

my $debug = 1;

my $uid = 65535;
open IFH, "</etc/passwd";
while (<IFH>) {
	if (/^(apache|www-data):/) {
		chomp;
		$uid = (split(/:/, $_))[2];
		last; } else { next; }
}
close IFH;
if (($< != $uid) && ($< != 0)) { exit 1; }

print header();

my $post = new CGI;
my $postdata = $post->Vars();

my $macaddr = untaint('macaddr', $postdata->{'macaddr'});
($macaddr) || kslog("err", "Invalid or null macaddr");
my $customer_number = untaint('digits', $postdata->{'customer_number'});
my $server_number = untaint('digits', $postdata->{'server_number'});
my $username = untaint('words', $postdata->{'username'});
my $reason = untaint('any', $postdata->{'reason'});
my $doit = $postdata->{doit};

# These may be provided from the console script
my $dc_abbr = $postdata->{dc_abbr};
my $switch_name = $postdata->{switch_name};
my $switch_port = $postdata->{switch_port};
my $ip_address = untaint("ipaddr", $postdata->{ip_address});

# First we need to find out where we are located
my $location;
if ($dc_abbr && $switch_name && $switch_port) {
    $location = { dc_abbr => $dc_abbr,
        switch_name => $switch_name,
        switch_port => $switch_port };
    kslog("info", "$macaddr - console location $dc_abbr:$switch_name-$switch_port") if ($debug);

}
elsif (my @info = macFinder($macaddr)) {
    ($dc_abbr, $switch_name, $switch_port) = @info;
    $location = { dc_abbr => $dc_abbr,
        switch_name => $switch_name,
        switch_port => $switch_port };
    kslog("info", "$macaddr - network location $dc_abbr:$switch_name-$switch_port") if ($debug);
}
else {
    kslog("info", "$macaddr - could not get location") if ($debug);
}

# Next we get the primary IP address for the customer
if (!$ip_address) {
    $ip_address = getCustomerProductIp($customer_number, $server_number);
	if ($ip_address) {
	    kslog("info", "$macaddr - $customer_number-$server_number $ip_address") if ($debug);
	}
	else {
	    kslog("info", "$macaddr - $customer_number-$server_number has no IP") if ($debug);
	}
}

print "customer_number=\"$customer_number\"
server_number=\"$server_number\"
username=\"$username\"
reason=\"$reason\"
dc_abbr=\"$dc_abbr\"
switch_name=\"$switch_name\"
switch_port=\"$switch_port\"
ip_address=\"$ip_address\"
";

if ($doit) {
    # We need to link the server first so the switch/port information is 
    # available before we attempt to swap VLANs

    kslog ("info", "linking server");
    my $linkInfo = {
        customerId => $customer_number,
        customerProductIdnum => $server_number,
        dc_abbr => $Config->{dc_abbr},
        switch => $switch_name,
        port => $switch_port,
        macaddr => $macaddr,
        message => $reason };

    my $linkResult = linkServer($linkInfo);

    if ($linkResult->[0]) {
		print "link=success\n";
		kslog("info", sprintf("%s linked to %s-%s at %s:%s/%s (%s : %s)", $macaddr, $customer_number, $server_number, $location->{dc_abbr}, $location->{switch_name}, $location->{switch_port}, $username, $reason));
    }

    kslog ("info", "marking online");
	my $online_result = lwpfetch(
        sprintf("%s/cgi-bin/register.cgi", $Config->{'ks_baseurl'}),
        { macaddr => $macaddr, ipaddr => $ip_address, status => "online" },
        undef);
	if ($online_result->[0] && ($online_result->[1] =~ /success/)) {
		print "online=success\n";
	}
}

exit 0;

