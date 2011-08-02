#!/usr/bin/perl -w

BEGIN {
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';
}

use strict;
use CGI ':standard';
use CGI ':cgi-lib';

my ($post, $postdata, $macaddr);
my ($return_status, $return_error);

if (@ARGV == 1 ) {
    $macaddr = untaint('macaddr', $ARGV[0]);
} elsif ($ENV{REMOTE_ADDR} ) {
    print header();
    $post = new CGI;
    $postdata = $post->Vars();
    if ($postdata->{macaddr}) {
        $macaddr = untaint('macaddr', $postdata->{macaddr});
    }
} else {
	exit 0;
} 


if (!$macaddr) {
    kslog("err", "Invalid or null MAC address supplied");
}

# We need to:
# - change OS load to sbrescue
# - reboot server
#
# register.cgi needs to report when a server enters rescue mode
#

$ksdbh = ks_dbConnect();

my $macObj = MACFun->new(dbh => $ksdbh, macaddr => $macaddr);
$macObj->osload("sbrescue");
if ($postdata->{internal} == 1) {
    $macObj->task("sbrescue");
}
else {
    $macObj->task("remoterescue");
}
$macObj->update();

#kslog("info", "DEBUG: MAC ".$macObj->macaddr()." TASK ".$macObj->task());
#print "status=success";
#exit 0;

my $errors = $macObj->error();

if ($errors->[0]) {
    kslog("info", "macObj errors: ".join("#", @{$macObj->error()}) );
    $return_status = "failure";
    $return_error = "general";
}
else {
	if (!update_pxe($macaddr, $macObj->pxe())) {
	    kslog("info", "$macaddr unable to update PXE target");
        $return_status = "failure";
        $return_error = "update_pxe_fail";
	}
    else {
	    kslog("info", "RemoteRescue: $macaddr PXE target updated");
		if (!rebootByMac($macaddr)) {
		    kslog("info", "RemoteRescue: $macaddr could not be rebooted");
            $return_status = "failure";
            $return_error = "online_reboot_fail";
        }
        else {
            kslog("info","RemoteRescue: $macaddr rebooted successfully");
        }
    }
}

if ($return_status) {
    print "status=$return_status&error=$return_error";
}
else {
    print "status=success";
}

$ksdbh->disconnect();
1;
