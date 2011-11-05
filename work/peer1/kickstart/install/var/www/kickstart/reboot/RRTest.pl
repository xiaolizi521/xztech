#!/usr/bin/perl -w

BEGIN {
    use lib qw(/exports/kickstart/lib);
    require 'sbks.pm';
}

use strict;

my $macAddress = $ARGV[0];

$macAddress = untaint('macaddr', $macAddress);

if (!$macAddress) { exit 0; }

$ksdbh = ks_dbConnect();

my $macObj = MACFun->new(dbh => $ksdbh, macaddr => $macAddress);
my $ipAddress = $macObj->ipaddr();
$ksdbh->disconnect();

if ($ipAddress eq "0.0.0.0") {
    print $macAddress." has no IP to test\n";
    exit 1;
}
else {
    my $icmpPing = Net::Ping->new("icmp", 1);
    if (!$icmpPing->ping($ipAddress)) {
        print $macAddress." ".$ipAddress." does not ping\n";
        exit 1;
    }
    else {
        print $macAddress." ".$ipAddress." initial ping test successful\n";
    }
}

my $switchPortInfo = switchPortInfo($macAddress);
#foreach my $key (sort(keys(%$switchPortInfo))) {
#    print "$key => $switchPortInfo->{$key}\n";
#}
RapidReboot($switchPortInfo, "cycle");

my $icmpPing = Net::Ping->new("icmp", 1);
if (!$icmpPing->ping($ipAddress)) {
    print $macAddress." ".$ipAddress." does not ping - SUCCESS\n";
}
else {
    print $macAddress." ".$ipAddress." still pings - FAILURE\n";
}

1;
