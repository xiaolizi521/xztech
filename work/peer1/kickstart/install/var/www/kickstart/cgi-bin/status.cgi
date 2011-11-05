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

$post = new CGI;
$postdata = $post->Vars();

$mode = $postdata->{'mode'};
$macaddr = untaint('macaddr', $postdata->{'macaddr'});

$dbh = ks_dbConnect();

if (($mode) && ($macaddr)) {
	my $macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
	my $status = $macobj->status();
	print "STATUS=$status";
	exit 0;
}
elsif ($macaddr) {
	my $result = $dbh->selectall_arrayref("SELECT mac_list_id,mac_address,vlan_id,ip_address,osload,pxefile,taskfile,old_status,new_status,extract(epoch from last_update) as last_update,extract(epoch from now()) as timestamp FROM kickstart_map WHERE mac_address = ? order by timestamp DESC", undef, $macaddr);
	my $row = $result->[0];
	printf("macid=%s,macaddr=%s,vlan=%s,ipaddr=%s,osload=%s,pxe=%s,task=%s,old_status=%s,new_status=%s,last_update=%s,timestamp=%s", @{$row});
	exit 0;
}

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
";

my $system = 'off';
if (provcheck($dbh) == 1) { $system = 'on'; }
print "Provisioning: <b>$system</b><br>\n";

my $total_ready = 0;
my @ready = hwcheck($dbh);
my $mref = shift(@ready);
my @models = @ready;
print "<table cellspacing=0 cellpadding=2 border=1>\n";
print "<th>Processor Model</th><th># Ready</th>\n";
foreach (@models) {
	print "<tr><td>$_</td><td align=center>$mref->{$_}</td></tr>\n";
	$total_ready += $mref->{$_};
}
print "<tr><td><b>Total</b></td><td align=center><b>$total_ready</b></td></tr>\n";
print "</table><br>\n";

print "The time is now: ".strftime("%Y.%m.%d-%T", localtime(time()))."<br>\n";

print "<table cellspacing=0 cellpadding=0 border=1>\n";
print '
<th>Last Change</th>
<th>MAC ID</th>
<th>MAC address</th>
<th>VLAN</th>
<th>IP address</th>
<th>OS Load</th>
<th>PXE target</th>
<th>Task</th>
<th>Old Status</th>
<th>New Status</th>
';

# dm@12-17-2006: formatted last_update to be more readable
my $table = $dbh->selectall_arrayref("SELECT mac_list_id, mac_address, vlan_id, ip_address, osload, pxefile, taskfile, old_status, new_status, to_char(last_update, 'Mon DD, YYYY hh:MI AM') FROM kickstart_map WHERE new_status = 'holding' OR (new_status_id >= 10 AND new_status_id NOT IN (60,255)) ORDER BY last_update DESC;");

foreach my $row (@{$table}) {
	#while (my($n,$v) = each (%{$row})) {
	#	print "$n=$v<br>\n";
	#}
    my $href = { mac_list_id => $row->[0], mac_address => $row->[1],
        vlan_id => $row->[2], ip_address => $row->[3],
        osload => $row->[4], pxefile => $row->[5], taskfile => $row->[6],
        old_status => $row->[7], new_status => $row->[8],
        last_update => $row->[9] };

	if ($href->{new_status} eq "ksfail") {
		print "<tr align=center bgcolor=$emerg_color>";
	}
	elsif ($href->{new_status} =~ /fail/) {
		print "<tr align=center bgcolor=$warn_color>";
	}
	else {
		print "<tr align=center bgcolor=$norm_color>";
	}
        # dm@12-17-2006: moved last_update from end of row to beginning, matches sort 
	print "<td>$href->{last_update}</td>";
	print "<td>$href->{mac_list_id}</td>";
	print "<td>$href->{mac_address}</td>";

	if ($href->{vlan_id}) { print "<td>$href->{vlan_id}</td>"; }
	else { print "<td>&nbsp;</td>"; }

	print "<td>$href->{ip_address}</td>";
	print "<td>$href->{osload}</td>";
	print "<td>$href->{pxefile}</td>";

	if (($href->{osload} eq "sbrescue") or ($href->{pxefile} eq "sbrescue")) {
		print "<td>$href->{taskfile}</td>";
	}
	else {
		print "<td>&nbsp;</td>";
	}

	print "<td>$href->{old_status}</td>";
	print "<td><b>$href->{new_status}</b></td>";
	print "</tr>";

        print "<tr><td colspan=5><font size=-1><pre>";

        system("grep -h '$href->{mac_address}' /exports/kickstart/logs/daemon.log.0 /exports/kickstart/logs/daemon.log | tail -n 10");
        print "</pre></font></td><td colspan=5><font size=-1><pre>";
        system("grep -h '$href->{mac_address}' /exports/kickstart/logs/script.log.0 /exports/kickstart/logs/script.log | tail -n 10");
       
        print "</pre></font></td></tr>";

}
print "</table>\n";

print qq{
<p><b>Normal status progression:</b>
<p>Linux Servers:
<pre>
updateks -> reboot -> booting -> ksscript -> postconf -> licenses -> ks_wait  -> kickstarted
updateks -> reboot -> booting -> ksscript -> postconf -> licenses -> cpl_wait -> kickstarted
updateks -> reboot -> booting -> ksscript -> postconf -> licenses -> esm_wait -> kickstarted
updateks -> reboot -> booting -> ksscript -> postconf -> licenses -> psa_wait -> kickstarted
</pre>
<p>Windows 2000/2003:<br>
<pre>
updateks -> reboot -> win2k_part -> win2k_partdone -> win2k_copy -> win2k_copydone -> (long wait) -> kickstarted
</pre>
};

print "</center>
</body>
</html>
";

$dbh->disconnect();

