#!/usr/bin/perl -w                   

BEGIN {                              
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';           
}                                    

use strict;                          
use CGI ':standard';                 
use CGI ':cgi-lib';                  

my ($post, $postData, @macList);

print header();

$post = new CGI;
@macList = $post->param("macList[]");

if (scalar(@macList) < 1) { exit 0; }

$ksdbh = ks_dbConnect();

my $errorMessages = getErrorMessages(\@macList);

foreach my $row (@$errorMessages) {
    foreach my $key (sort(keys(%$row))) {
        print "$key=$row->{$key}&";
    }
    print "\n";
}

$ksdbh->disconnect();

1;
