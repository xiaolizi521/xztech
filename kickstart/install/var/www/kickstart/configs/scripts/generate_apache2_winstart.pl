#!/usr/bin/perl -w

BEGIN {
        use lib qw(/exports/kickstart/lib);
        require 'sbks.pm';
}

use strict;

my ($dbh, $httpconf, $httpconfssl, $apacheroot, $wwwuser, $template, $template_ssl);
$apacheroot = "/etc/apache2";
$httpconf   = "/etc/apache2/sites-available/winstart";
$httpconfssl = "/etc/apache2/sites-available/winstart-ssl";
$template = "/exports/kickstart/configs/templates/apache2/sites-available/winstart";
$template_ssl = "/exports/kickstart/configs/templates/apache2/sites-available/winstart-ssl";


sub get_nets {
        my (@ret1, @ret2);
        my $qry1 = "SELECT host(network(public_network)) AS pu_net,
                host(netmask(public_network)) AS pu_mask,
                host(network(private_network)) AS pr_net,
                host(netmask(private_network)) AS pu_mask
                FROM vlans WHERE id NOT IN (1,405) ORDER BY id ASC"; 
        my $sth1 = $dbh->prepare($qry1);
        $sth1->execute();
        my($pu_net,$pu_mask,$pr_net,$pr_mask);
        $sth1->bind_columns(\($pu_net,$pu_mask,$pr_net,$pr_mask));
        while ($sth1->fetch()) {
                push(@ret1, "$pu_net/$pu_mask");
                push(@ret2, "$pr_net/$pr_mask");
        }
        $sth1->finish();
        return [ \@ret1, \@ret2 ];
}

# MAIN
$dbh = ks_dbConnect();
my $nets = get_nets();
$dbh->disconnect();

if ( ! -d "/etc/apache2") {
        die "No apache found\n";
}

open PASSWD, "</etc/passwd";
while (<PASSWD>) {
        if (/^apache:/) { $wwwuser = "apache"; last; }
        if (/^www-data:/) { $wwwuser = "www-data"; last; }
}
close PASSWD;

my @public = @{$nets->[0]};
my @private = @{$nets->[1]};

my @newconf;
open IFH, "<$template";
while (<IFH>) {
        next if (/^$|^#/);
        chomp;
        $_ =~ s/^\s+//g;

	if (/\@\@KS_PUBLIC_IPADDR\@\@/) {
                $_ =~ s/\@\@KS_PUBLIC_IPADDR\@\@/$Config->{'ks_public_ipaddr'}/g;
        }
        if (/\@\@KS_HOST\@\@/) {
                $_ =~ s/\@\@KS_HOST\@\@/$Config->{'ks_host'}/g;
        }
        if (/\@\@KS_IPADDR\@\@/) {
                $_ =~ s/\@\@KS_IPADDR\@\@/$Config->{'ks_ipaddr'}/g;
        }

        push(@newconf, $_);
}
close IFH;

open OFH, ">$httpconf" || die "open $httpconf: $!\n";
foreach my $line (@newconf) {
        print OFH $line . "\n"
}
close OFH;

if ( $template_ssl ) {

@newconf = ();

open IFH, "<$template_ssl";
while (<IFH>) {
        next if (/^$|^#/);
        chomp;
        $_ =~ s/^\s+//g;

        if (/\@\@KS_PUBLIC_IPADDR\@\@/) {
                $_ =~ s/\@\@KS_PUBLIC_IPADDR\@\@/$Config->{'ks_public_ipaddr'}/g;
        }
        if (/\@\@KS_HOST\@\@/) {
                $_ =~ s/\@\@KS_HOST\@\@/$Config->{'ks_host'}/g;
        }
        if (/\@\@KS_IPADDR\@\@/) {
                $_ =~ s/\@\@KS_IPADDR\@\@/$Config->{'ks_ipaddr'}/g;
        }

        push(@newconf, $_);
}
close IFH;

open OFH, ">$httpconfssl" || die "open $httpconf: $!\n";
foreach my $line (@newconf) {
        print OFH $line . "\n"
}
        close OFH;
}

