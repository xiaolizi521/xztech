#!/usr/bin/perl -w
# =======================================================================
# Company:              Peer1
# Copyright(c):         Peer1 2008
# Project:              Kickstart
# Code Devloper:        Caleb Collins/Carlos Avila
# Creation Date:        12/09/2008
#
# File Type:            CGI
# File Name:            hw_raidsetup.cgi
#
# Description:
# This CGI will check if the conditions are appropiate to queue de server
# for a hardware RAID setup process. If the conditions are met the server
# will be configured and eventually provisioned.
#
# UPDATE UPDATE UPDATE UPDATE
# This CGI is for testing only, now all functions have been put into
# updateks.cgi. ccollins@peer1.com
# =======================================================================

BEGIN {
        use lib "/exports/kickstart/lib";
        require 'sbks.pm';
}

use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;

print header;

# Variable Defitions
my ( $dbh, $macaddr, $macobj, $status, $postconf, $status );

my $post     = new CGI;
my $postdata = $post->Vars();

$macaddr = untaint( 'macaddr', $postdata->{'macaddr'} );
($macaddr) || kslog( 'err', "Invalid or null macaddr" );

$dbh      = ks_dbConnect();
$macobj   = MACFun->new( dbh => $dbh, macaddr => $macaddr );
$status   = $macobj->status;
$postconf = $macobj->postconf();

unless ( $postconf->{customer_number} ) {
        print "status=failed";
        kslog( 'err', "[$macaddr] No postconf found" );
}

$macobj->pxe("sbrescue");
$macobj->task("hw_raidsetup");
$macobj->update();
update_pxe( $macaddr, "sbrescue" );
rebootByMac($macaddr);
print "status=success";
kslog( 'info', "[$macaddr] Will be configured with a custom RAID setup" );

$dbh->disconnect;
1;



