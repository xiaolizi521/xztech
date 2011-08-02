#!/usr/bin/perl -w                   

BEGIN {                              
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';           
}                                    

use strict;                          
use CGI ':standard';                 
use CGI ':cgi-lib';                  

my ($post, $postData);

print header();

$post = new CGI;
my $active = $post->param("active");
my $update = $post->param("update");

my $where_clause;

if ($active eq "true") {
    $where_clause = "AND t1.active='t'";
} elsif ($active eq "false") {
    $where_clause = "AND t1.active='f'";
} else {
    $where_clause = "";
}

$ksdbh = ks_dbConnect();

my $last24 = $ksdbh->selectrow_arrayref("   SELECT
                                            count(*) as total
                                            FROM
                                            rapid_reboot_queue
                                            WHERE active='f'
                                            AND started > (
                                            now() - '24 hours'::interval)");

my $result = $ksdbh->selectall_arrayref("   SELECT
                                            t1.mac_address, 
                                            t2.status, 
                                            t1.started,
                                            t1.last_updated
                                            FROM rapid_reboot_queue t1,
                                                 rapid_reboot_status_list t2
                                            WHERE t1.status = t2.id
                                            $where_clause
                                            ORDER BY t1.status");
print "total=".$last24->[0]."\r\n";

foreach my $row (@$result) {
    printf "mac_address=%s&status=%s&started=%s&last_updated=%s\r\n", @$row;
}

if ($update eq "true") {
    $ksdbh->do("    UPDATE rapid_reboot_queue
                    SET active='f'
                    WHERE active='t'
                    AND status > 5", undef);
    printf "update=done\r\n";
}

$ksdbh->disconnect();

1;
