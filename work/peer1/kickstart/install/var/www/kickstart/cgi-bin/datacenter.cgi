#!/usr/bin/perl
# datacenter.cgi
#
# This script is meant to emulate the DCC audit call for data centers
# that only have a WinStart server (i.e. Managed Hosting) using the
# existing MACFun library.
# ======================================================================

#Program Library usage and pragma defintions
use strict;
use warnings;
use Digest::CRC qw(crc32);
use CGI ':standard';
use CGI ':cgi-lib';
use Data::Dumper;
use XML::Simple;

# BEGIN block (executed first)
BEGIN {
    use lib '/exports/kickstart/lib';
    require 'sbks.pm';
}

# Local Variable Declaration
my ($data, $hash, $post, $postdata, $macaddr, $ipaddr, $status);

# Parse the XML
if (@ARGV > 0) {
    # Reading the XML from command-line arguments
    $data = XMLin($ARGV[0]);
    $ENV{'REMOTE_ADDR'} = "10.1.0.234";
} else {
    # Read the post data from the query
    print "Content-type: text/html\n\n";
    read(STDIN, $postdata, $ENV{'CONTENT_LENGTH'});
    $data = XMLin($postdata);
}

# This part of the XML is relatively standard. This isn't really
# used by WinStart that we can tell but we don't know if we can
# remove it yet.
$hash->{'product'} = join " ",
    $data->{'vendor'},
    $data->{'product'};
$hash->{'mobo_hash'} = crc32($hash->{'product'});
$hash->{'mem'} = 0;
$hash->{'ipaddr'} = $ENV{'REMOTE_ADDR'};

# Call the subroutine to walk the XML tree
parseXML($data, $hash);

# Remove remove leading and trailing characters, carriage returns,
# and extra spaces within the XML values without modifying keys.
foreach (keys %{$hash}) {
    $hash->{$_} =~ s/[\s]+/ /g;
    chomp $hash->{$_};
}

# Get Database Connection Handle
my $dbh = ks_dbConnect();

# Instansiate new MacFun Object passing the datbase handle
# and the MAC Address
my $macobj = MACFun->new(dbh => $dbh, macaddr => $hash->{'macaddr'});

# Log that the client contacted the server
kslog('info', "$hash->{'macaddr'} STATUS -> $hash->{'status'}");
kslog('info', "$hash->{'macaddr'} IPADDR -> $hash->{'ipaddr'}");

# Update the hardware list
$macobj->task("audit");
$macobj->hardware($hash);
$macobj->update();

# This came from /opt/kickstart/install/var/www/kickstart/bin/scan_for_new
if (!$hash->{hdd_hda_model} && !$hash->{hdd_sda_model}) {
    # This appears in Winstart under "Status History"
    $macobj->status("audit_fail");

    # This appears in Winstart under "Messages"
    $macobj->logError("audit_fail (missing HDD)");

    # Log this error via syslog
    kslog('info', "$hash->{'macaddr'} STATUS -> audit_fail (missing HDD)");
} else {
    # Register the server with kickstart
    $macobj->status("ready");
}

# Update the database
$macobj->update();

# This is for the response returned to the client
$hash->{'status'} = $macobj->status();

# Disconnect from the database
$dbh->disconnect;

# Let the server audit know this has been completed
print "Location: WinStart\n";

# Print out what key/value pairs were stored
my $format = "%18.18s | %-55.55s\n";
printf $format, "KEY", "VALUE";
printf $format, "-"x18, "-"x55;
foreach (sort {$a cmp $b} keys %{$hash}) {
    printf $format, $_, $hash->{$_};
}

exit(0);

sub parseXML {
    my ($data, $key) = @_;
    my $keys = [ 'processor', 'display', 'memory', 'storage', 'network', 'bridge' ];

    if (!inArray($keys, $key)) {
        foreach (keys %{$data}) {
            if ($data->{$_} =~ m/HASH/) {
                parseXML($data->{$_}, $_);
            }
        }
        return;
    }

    elsif ($key eq "processor") {
        readProcessorInfo($data, "");
    }

    elsif ($key eq "display") {
        $hash->{'vga0'} = $data->{'product'};
    }

    elsif ($key eq "memory") {
        readMemoryInfo($data, "");
    }

    elsif ($key eq "storage") {
        readDiskInfo($data, "");
        if (defined $data->{'description'} && $data->{'description'} eq "RAID bus controller") {
            $hash->{"raid_controller"} = $data->{'vendor'} . " " . $data->{'product'};
        }
    }

    elsif ($key eq "network") {
        readNetworkInfo($data, "");
    }

    elsif ($key eq "bridge") {
        readBridgeInfo($data, "");
    }
}

sub readProcessorInfo {
    my ($data, $key) = @_;

    # Return if we don't have a hash (end-of-line)
    if ($data !~ m/^HASH/) {
       return;
    }
    if (defined $data->{'width'}) {
        my $proc = 0;
        $proc = substr($key,4) unless $key eq "";
        $hash->{"cpu${proc}_vendor"} = $data->{vendor};
        $hash->{"cpu${proc}_model"} = $data->{product};
        $hash->{"cpu${proc}_size"} = $data->{size}->{value};
    } else {
        # Recurse to find CPUs
        foreach (keys %{$data}) {
            readProcessorInfo($data->{$_}, $_);
        }
    }
}

sub readNetworkInfo {
    my ($data, $key) = @_;

    # Return if we don't have a hash (end-of-line)
    if ($data !~ m/^HASH/) {
       return;
    }

    if (defined $data->{'serial'}) {
        $hash->{$data->{'logicalname'}} = $data->{'vendor'} . " " . $data->{'product'};
        $hash->{macaddr} = $data->{'serial'} if $data->{'logicalname'} eq "eth0";
    } else {
        # Recurse to find adaptors
        foreach (keys %{$data}) {
            readNetworkInfo($data->{$_}, $_);
        }
    }
}

sub readBridgeInfo {
    my ($data, $key) = @_;

    # Exit if we've reached the end of our branch
    return if ($data !~ m/^HASH/);

    if ($key eq "storage") {
        readDiskInfo($data, "");
    } elsif (defined $data->{'description'} && $data->{'description'} =~ m/ethernet/i) {
        readNetworkInfo($data, "");
    } else {
        # Recurse to find adaptors
        foreach (keys %{$data}) {
            readBridgeInfo($data->{$_}, $_);
        }
    }
}

sub readDiskInfo {
    my ($data, $key) = @_;

    # Return if we don't have a hash (end-of-line)
    if ($data !~ m/^HASH/) {
       return;
    }

    # If the disk has size and a logical name, report it
    if (defined $data->{'size'} && defined $data->{'logicalname'}) {
        my $device = substr($data->{'logicalname'}, 5);
        $hash->{"hdd_${device}_size"} = $data->{'size'}->{'value'};
        $hash->{"hdd_${device}_model"} = $data->{'description'};
    } else {
        # Recurse to find disks
        foreach (keys %{$data}) {
            readDiskInfo($data->{$_}, $_);
        }
    }
}

sub readMemoryInfo {
    my ($data, $key) = @_;

    # Return if we don't have a hash (end-of-line)
    if ($data !~ m/^HASH/) {
       return;
    }

    # Add the size of detected RAM to the hash
    if (defined $data->{'size'} && defined $data->{'size'}->{'value'}) {
        $hash->{"mem"} += $data->{'size'}->{'value'};
    } else {
        # Recurse to find memory
        foreach (keys %{$data}) {
            readMemoryInfo($data->{$_}, $_);
        }
    }
}

sub inArray {
    my ($keys, $key) = @_;
    foreach (@{$keys}) {
        return 1 if defined $key && $_ eq $key;
    }
    return 0;
}

__END__
vim: ts=4 sts=4 sw=4 ft=perl nu expandtab cindent
vim: cinkeys=0{,0},0),\:,!^F,o,O,e*
