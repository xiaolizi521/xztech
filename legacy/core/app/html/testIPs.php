<?
require_once("CORE_app.php");

###################################################
###################################################

$cidr = "72.3.131.0/26";
$network = plain_host($cidr);
$netmask = netmask($cidr);
$base_ip = getBaseIP($network, $netmask);
$first_usable_ip = getFirstUsableIP($cidr);
$last_usable_ip = getLastUsableIP($cidr);
$total_usable = getTotalUsableIPs($cidr);

$server_ips = getPrimaryIPRange($cidr);

print "Base IP: $base_ip<p>";
print "Netmask: $netmask<p>";
print "Base IP for the $cidr block is $base_ip<br>";
print "First usable IP for the $cidr block is $first_usable_ip<br>";
print "Last usable IP for the $cidr block is $last_usable_ip<br>";
print "Total usable IPs for $cidr block is $total_usable<br>";
print "Server IPs:<br>";
print $server_ips[0]."->".$server_ips[ count($server_ips)-1 ]."<br>";
foreach($server_ips as $ip) {
    print $ip."<br>";
}
print "<p>";
?>
