#!/usr/bin/perl -w

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use strict;
use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;

my ($dbh, $post, $postdata, $mode, $macaddr);

my $norm_color = "#F0F0F0";
my $warn_color = "#FFFF00";
my $emerg_color = "#EE2C2C";

print header();


#$dbh = ks_dbConnect();

#my $result = $dbh->selectall_arrayref("SELECT mac_list_id,mac_address,vlan_id,ip_address,osload,pxefile,taskfile,old_status,new_status,extract(epoch from last_update) as last_update,extract(epoch from now()) as timestamp FROM kickstart_map WHERE mac_address = ? order by timestamp DESC", undef, $macaddr);
#	my $row = $result->[0];
#	printf("macid=%s,macaddr=%s,vlan=%s,ipaddr=%s,osload=%s,pxe=%s,task=%s,old_status=%s,new_status=%s,last_update=%s,timestamp=%s", @{$row});
#	exit 0;
#}

# dm@12-17-2006: added font styles and corrected missing html tags (body, closing head)


print "<html>
<head>
	<title>".$Config->{'ks_host'}."</title>
	<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"300\">
    	<meta HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
        <style>
                th,td,body {
                        font-size: 8pt;
                }
        </style>
</head>
<body>
	<center>
	<H1>".$Config->{'ks_host'}."</H1>
<h2>";

system("bash /exports/kickstart/bin/ks_server_check.sh");

print "<h2></body></html>";
