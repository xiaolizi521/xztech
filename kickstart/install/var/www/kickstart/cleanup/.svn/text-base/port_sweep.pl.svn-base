#!/usr/bin/perl -w

BEGIN { 
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;
use Getopt::Long;

my ($dc_abbr, $empty, $unknown);

$dc_abbr = $Config->{dc_abbr};

GetOptions(empty => \$empty, unknown => \$unknown);

my @ports = ();
my $content;

if ($empty) {
    my $empty_result = lwpfetch(
        sprintf("%s/list_ports.php", $Config->{pit_baseurl}),
        { dc_abbr => $dc_abbr, type => "empty" }, undef, undef);
    if ($empty_result->[0]) {
        $content = $empty_result->[1];
    }
    else {
        print "Unable to get list of empty ports\n";
        exit 1;
    }
}

if ($unknown) {
    my $unknown_result = lwpfetch(
        sprintf("%s/list_ports.php", $Config->{pit_baseurl}),
        { dc_abbr => $dc_abbr, type => "unknown" }, undef, undef);
    if ($unknown_result->[0]) {
        $content = $unknown_result->[1];
    }
    else {
        print "Unable to get list of unknown ports\n";
        exit 1;
    }
}

#print $content."\n\n";
my @lines = split(/\n/, $content);
print scalar(@lines)." ports to reboot\n";
foreach my $line (@lines) {
    my $rebootInfo = {};
    print "### ".$line."\n";
    my @pairs = split(/&/, $line);
    foreach my $pair (@pairs) {
        next if ($pair eq "");
        my ($key, $value) = split(/=/, $pair);
        $rebootInfo->{$key} = $value;
        #print "$key => $value\n";
    }
    #next unless ($rebootInfo->{switch} =~ /^d3/);
    printf("Rebooting: %s:%s-%d\t@%s %s:%s:%s\n", $rebootInfo->{dc_abbr}, 
        $rebootInfo->{switch}, $rebootInfo->{switch_port},
        $rebootInfo->{reboot_server}, $rebootInfo->{serial_port},
        $rebootInfo->{board_address}, $rebootInfo->{board_port});

    #portvlan($rebootInfo, 405);
    RapidReboot($rebootInfo);
}

1;
