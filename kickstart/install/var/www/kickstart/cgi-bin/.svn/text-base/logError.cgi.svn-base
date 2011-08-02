#!/usr/bin/perl -w                   

BEGIN {                              
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';           
}                                    

use strict;                          
use CGI ':standard';                 
use CGI ':cgi-lib';                  

my ($post, $postData, $macAddress, $errorMessage);

print header();

$post = new CGI;
#$macAddress = untaint('macaddr', $post->param("macaddr"));
$macAddress = $post->param("macaddr");
$errorMessage = $post->param("error_message");

if (!$macAddress) { kslog("INFO", "No MAC Address") ; exit 0; }
if (!$errorMessage) { kslog("INFO", "No Error Message"); exit 0; }

# Previously, this was not actually logging the error...it needs to.  I am setting the
# log level to WARNING so that the kslog logger will not stop exection.  The previous method
# was to not exit so I want to keep that the same.
kslog("WARNING", "$macAddress $errorMessage");

$ksdbh = ks_dbConnect();

my $macObj = MACFun->new(dbh => $ksdbh, macaddr => $macAddress);
$macObj->logError($errorMessage);

$ksdbh->disconnect();


1;
