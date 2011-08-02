#!/usr/bin/perl -w

BEGIN {
  use lib '/exports/kickstart/lib';
  require 'sbks.pm';
}

use strict;

sub print_date {
    my $thedate = `date`;
    chomp($thedate);
    print "\n------$thedate------\n";
}

sub get_online_server_list {
  my $return = {};
  my $lwpResult = lwpfetch(
                    sprintf("%s/list_online_servers.php", $Config->{pit_baseurl}),
                    { dc_abbr => $Config->{dc_abbr} },
                    undef,
                    undef
                  );
  if ($lwpResult->[0] && $lwpResult->[1]) {
    my @online_servers = split(/\n/, $lwpResult->[1]);
    foreach my $row (@online_servers) {
      my ($macaddr, $ipaddr) = split(/,/, $row);
      $return->{"$macaddr"} = $ipaddr;
    }
  } else {
    printf "Unable to fetch list of online servers for %s: %s\n",
           $Config->{dc_abbr}, $lwpResult->[1];
    exit 1;
  }
  return $return;
}

sub get_master_server_list {
  my @return = ();

  my $lwpResult = lwpfetch(
                    $Config->{pit_baseurl}."/macList.php",
                    { datacenter_id => $Config->{dc_number} }
                  );
  if ($lwpResult->[0] && $lwpResult->[1]) {
    @return = split("\n", $lwpResult->[1]);
  } else {
    printf "Unable to fetch list of servers for %s: %s\n",
           $Config->{dc_abbr}, $lwpResult->[1];
    exit 1;
  }
  return @return;
}

sub get_unlinked_server_list {
  my @return = ();

  my $lwpResult = lwpfetch(
                    $Config->{pit_baseurl}."/list_unlinked_servers.php",
                    { dc_abbr => $Config->{dc_abbr} }
                  );
  if ($lwpResult->[0] && $lwpResult->[1]) {
    @return = split("\n", $lwpResult->[1]);
  } else {
    printf "Unable to fetch list of unlinked servers for %s: %s\n",
           $Config->{dc_abbr}, $lwpResult->[1];
    exit 1;
  }
  return @return;
}

sub get_retired_server_list {
  my $ksdbh = shift();

  my $return = $ksdbh->selectcol_arrayref("
    SELECT mac_address
    FROM kickstart_map
    WHERE new_status = 'new'
      AND ip_address = '0.0.0.0'
      AND osload = 'sbrescue'
      AND pxefile = 'sbrescue'
      AND taskfile = 'default'"
  );
  return @$return;
}

sub is_data_in_postconf {
  my $ksdbh = shift();
  my $macID = shift();

  my $return = $ksdbh->selectrow_arrayref("
    SELECT count(*)
    FROM postconf
    WHERE mac_list_id = $macID"
  )->[0];
  
  return $return;
}

############
### MAIN ###
############

my $debug = 0;
if ($debug) {
  print "DEBUG AHHHHHHHHHHHHHHH\n\n\n\n";
  }

if (($ARGV[0]) && ($ARGV[0] eq "debug")) { $debug = 1; }

my $ksdbh = ks_dbConnect();

my $online_servers = get_online_server_list();
my @servers = get_master_server_list();
my @unlinked = get_unlinked_server_list();
my @retired = get_retired_server_list($ksdbh);

my $db_result = $ksdbh->selectcol_arrayref("
  SELECT mac_address
  FROM kickstart_map
  ORDER BY mac_address"
);

foreach my $mac (@$db_result) {
  print_date();
  if (exists $online_servers->{"$mac"}) {
    print "$mac: MARKING ONLINE\n";
    my $macobj = MACFun->new(dbh => $ksdbh, macaddr => $mac);
    my $ipaddr = $macobj->ipaddr();
    my $status = $macobj->status();
    my $osload = $macobj->osload();
    my $pxe = $macobj->pxe();
    
    if ($ipaddr ne $online_servers->{"$mac"}) {
      print "  IPADDR: $mac: $ipaddr != $online_servers->{$mac}\n";
      $macobj->ipaddr($online_servers->{"$mac"}) unless ($debug);
    }
    if ($status !~ /online(_rescue)?$/) {
      print "  STATUS: $mac: $status != online\n";
      $macobj->status("online") unless ($debug);
    }
    if (!defined($osload)) {
      print "  NO OS: $mac: No OS load, fixing...\n";
      $macobj->osload("localboot") unless ($debug);
      print "done.\n";
    } elsif ($osload ne "localboot") {
      print "  OSLOAD: $mac: $osload != localboot\n"; 
      $macobj->osload("localboot") unless ($debug);
    }

    if ($pxe ne "localboot") {
      print "  PXE: $mac: $pxe != localboot\n";
      $macobj->pxe("localboot") unless ($debug);
    }

    $macobj->update() unless ($debug);
        new_update_pxe($mac, "localboot") unless ($debug);

    } elsif (grep(/^$mac$/, @retired)) {
        print "$mac: already retired, skipping.\n";

    } elsif (!grep(/^$mac$/, @servers)) {
        print "$mac: does not exist, retiring...";
        my $macobj = MACFun->new(dbh => $ksdbh, macaddr => $mac);
        $macobj->retire() unless ($debug);
        print "done.\n";
        new_update_pxe($mac, "sbrescue") unless ($debug);

    } elsif (grep(/^$mac$/, @unlinked)) {
        print "$mac: unlinked server...";
        my $macobj = MACFun->new(dbh => $ksdbh, macaddr => $mac);
        if ($macobj->status() =~ /(holding|ready|updateks)/) {
                print "leaving alone: status is " . $macobj->status() . ".\n";
        } else {
                print "starting reclaim procedure...";
                $macobj->status("updateks") unless ($debug);
                $macobj->osload("default") unless ($debug);
                $macobj->update() unless ($debug);
                new_update_pxe($mac, "sbrescue") unless ($debug);
                register($macobj, "unknown") unless ($debug);
                print "done\n";
        }
    
    } else {
    
    # server does show up in the dc map in ocean and does not belong to
    # a customer; can't be an orphaned server since we mark all orphan servers
    # online however, could it be a virtual mac? virtual macs show up in the dc
    # map, but do they ever get to the kickstart database? assuming that they
    # don't, then how did we get here? assuming that they do, then this must be a
    # virtual mac
    print "[ii] $mac: what to do? am I provisioning?\n" if ($debug);
    
    }
  
  print "  Stuff in postconf...";
  my $macobj = MACFun->new(dbh => $ksdbh, macaddr => $mac);

  if (is_data_in_postconf($ksdbh, $macobj->macID())) {
    print "true\n";
  } else {
    print "false\n";
  }
}
