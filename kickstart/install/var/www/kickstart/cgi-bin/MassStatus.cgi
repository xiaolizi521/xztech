#!/usr/bin/perl -w                   

BEGIN {                              
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';           
}                                    

use strict;                          
use CGI ':standard';                 
use CGI ':cgi-lib';                  

my ($post, $postData, $macList);

print header();

$post = new CGI;
my @macList = $post->param("macList[]");
my @statusList = $post->param("statusList[]");

my $where_clause;

if (scalar(@macList) > 0) {
    $where_clause = "WHERE mac_address IN ('".join("','", @macList)."')";
}
elsif (scalar(@statusList) > 0) {
    if ($statusList[0] eq "fullview") {
        $where_clause = "WHERE new_status = 'holding' OR (new_status_id >= 10 AND new_status_id NOT IN (60,255))";
    }
    else {
        $where_clause = "WHERE new_status IN ('".join("','", @statusList)."')";
    }
}
else { exit 1; }

$ksdbh = ks_dbConnect();

#my $result = $ksdbh->selectall_hashref("SELECT mac_address,osload,pxefile,taskfile,vlan_id,ip_address,old_status,new_status,(extract(epoch from now()) - extract(epoch from last_update)) AS age, date_trunc('second', last_update) as last_update FROM kickstart_map $where_clause ORDER BY osload, new_status, last_update", "mac_address");
my $result = $ksdbh->selectall_arrayref("SELECT mac_address,osload,pxefile,taskfile,vlan_id,ip_address,old_status,new_status,(extract(epoch from now()) - extract(epoch from last_update)) AS age, date_trunc('second', last_update) as last_update FROM kickstart_map $where_clause ORDER BY osload, new_status, last_update");

foreach my $row (@$result) {
    printf "mac_address=%s&osload=%s&pxefile=%s&taskfile=%s&vlan_id=%s&ip_address=%s&old_status=%s&new_status=%s&age=%s&last_update=%s\r\n", @$row;
}

#foreach my $macAddress (sort(keys(%$result))) {
#    my $row = $result->{$macAddress};
#    print "mac_address=$row->{mac_address}&";
#    foreach my $key (sort(keys(%$row))) {
#        next if ($key eq "mac_address");
#        print $key."=".$row->{$key}."&";
#    }
#    print "\r\n";
#}

$ksdbh->disconnect();

1;
