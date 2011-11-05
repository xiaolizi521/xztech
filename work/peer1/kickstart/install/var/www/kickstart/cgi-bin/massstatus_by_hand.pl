#!/usr/bin/perl -w                   

BEGIN {                              
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';           
}                                    

use strict;                          
use CGI ':standard';                 
use CGI ':cgi-lib';                  

my ($post, $postData, $macList);


$post = new CGI;
my @macList = $post->param("macList[]");
my @statusList = $post->param("statusList[]");

my $where_clause = "WHERE mac_address = '00:50:70:31:17:89';";


$ksdbh = ks_dbConnect();


my $result = $ksdbh->selectall_hashref("SELECT mac_address,osload,pxefile,taskfile,vlan_id,ip_address,old_status,new_status,(extract(epoch from now()) - extract(epoch from last_update)) AS age, date_trunc('second', last_update) as last_update FROM kickstart_map $where_clause", "mac_address");

#foreach my $row (@$result) {
#    printf "mac_address=%s&osload=%s&pxefile=%s&taskfile=%s&vlan_id=%s&ip_address=%s&old_status=%s&new_status=%s&age=%s&last_update=%s\r\n", @$row;
#}

foreach my $macAddress (sort(keys(%$result))) {
    my $row = $result->{$macAddress};
    print "mac_address=$row->{mac_address}\n";
    foreach my $key (sort(keys(%$row))) {
        next if ($key eq "mac_address");
        print $key."=".$row->{$key}."\n";
    }
    print "\r\n";
}

$ksdbh->disconnect();

1;
