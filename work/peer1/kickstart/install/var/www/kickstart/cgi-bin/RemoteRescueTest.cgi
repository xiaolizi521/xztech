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

if ($ENV{REMOTE_ADDR} && $ENV{REMOTE_ADDR} eq "66.139.72.250") {
    print header();
    print "hello";
    $post = new CGI;
    $postdata = $post->Vars();
    if ($postdata->{macaddr}) {
        $macaddr = untaint('macaddr', $postdata->{macaddr});
    }
}
elsif (@ARGV == 1) {
    $macaddr = untaint('macaddr', $ARGV[0]);
}
else { exit 0; }

if (!$macaddr) {
    kslog("err", "Invalid or null MAC address supplied");
}

print "hello2<br>";
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

print "hello3<br>";

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
    print "hello4<br>";
    print $macaddr."<br>";
    print $macObj->pxe()."<br>";
    print update_pxe($macaddr, $macObj->pxe());
	if (!update_pxe($macaddr, $macObj->pxe())) {
	    kslog("info", "$macaddr unable to update PXE target");
        $return_status = "failure";
        $return_error = "update_pxe_fail";
        print "hello6<br>";
	}
    else {
        print "hello5<br>";
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
