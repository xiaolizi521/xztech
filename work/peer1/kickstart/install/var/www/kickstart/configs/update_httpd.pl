#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

#-----------------------------------------------------------
#This sub queries the kickstart database looking for 
#network configuration information of all the vlans that 
#kickstart will be responsible of. 
#------------------------------------------------------
sub get_nets {
	my $dbh = shift;
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

#------------------------------------------
# MAIN
#   Execution path starts here
#------------------------------------

#-------------------------------------------------
#Define global variables 
#------------------------------------------------
my @public; #public networks info
my @private; #private networks info
my $nets; #placeholder for array ref of all networks.
my $dbh; #connection to the kickstart database
my $httpconf; #path to apache's config file
my $apacheroot; #path to the apache's config folder on /etc
my $wwwuser; #used to store the username configured for the apache service
my @newconf; #new conf for apache will be stored here
my $apachectl; #executable for apachectl

#--------------------------
#Let's find the correct path for the Apache
#service installed on this sytem
#---------------------
if ( -d "/etc/httpd") {
	$apacheroot = "/etc/httpd";
	$httpconf = "/etc/httpd/conf/httpd.conf";
        $apachectl = "apachectl";
}
elsif ( -d "/etc/apache") {
	$apacheroot = "/etc/apache";
	$httpconf = "/etc/apache/conf.d/sbks.conf";
        $apachectl = "apachectl";
}
elsif ( -d "/etc/apache2" ) {
        $apacheroot = "/etc/apache2";
        $httpconf = "/etc/apache2/sites-available/sbks.conf";
        $apachectl = "apache2ctl";
}
else { die "No apache found\n"; }

#--------------------------
#Find the username assigned to the Apache
#service.
#---------------------
open PASSWD, "</etc/passwd";
while (<PASSWD>) {
	if (/^apache:/) { $wwwuser = "apache"; last; }
	if (/^www-data:/) { $wwwuser = "www-data"; last; }
}
close PASSWD;

#------------------------------------
# Find the network information that 
# will be used to configure the public and
# the private inerfaces
#--------------------------------
$dbh = ks_dbConnect();
$nets = get_nets( $dbh );
$dbh->disconnect();
@public = @{$nets->[0]};
@private = @{$nets->[1]};

#-----------------------------------------
#Lets start building the configuration...
#-----------------------------------

open IFH, "</exports/kickstart/configs/templates/apache/httpd.conf";
while (<IFH>) {
	next if (/^$|^#/);
	chomp;
	$_ =~ s/^\s+//g;
	if (/\@\@WWWUSER\@\@/) {
		$_ =~ s/\@\@WWWUSER\@\@/$wwwuser/g;
		push(@newconf, $_);
		next;
	}

	if (/\@\@APACHEROOT\@\@/) {
		$_ =~ s/\@\@APACHEROOT\@\@/$apacheroot/g;
		push(@newconf, $_);
		next;
	}

	if (/\@\@KS_PUBLIC_IPADDR\@\@/) {
		$_ =~ s/\@\@KS_PUBLIC_IPADDR\@\@/$Config->{'ks_public_ipaddr'}/g;
		push(@newconf, $_);
		next;
	}

	if (/\@\@DEFAULT\@\@/) {
		push(@newconf, "Allow from 127.0.0.1");
		push(@newconf, "Allow from $Config->{'ks_ipaddr'}");
		push(@newconf, "Allow from $Config->{'ks_public_ipaddr'}");
		if( $Config->{'adm_www_host'} ) { #Needed for winstart
		        push(@newconf, "Allow from $Config->{'adm_www_host'}");
		}
		next;
	}

	if (/\@\@PRIVATE\@\@/) {
		foreach my $network (@private) {
			push(@newconf, "Allow from $network");
		}
		next;
	}

	if (/\@\@PUBLIC\@\@/) {
		foreach my $network (@public) {
			push(@newconf, "Allow from $network");
		}
		next;
	}

	push(@newconf, $_);

}
close IFH;

open OFH, ">$httpconf" || die "open $httpconf: $!\n";
foreach my $line (@newconf) {
	print OFH $line."\n"
}
close OFH;

if (system( $apachectl, "configtest" ) == 0) {
	system( $apachectl, "graceful" );
}
else { print "Configuration problem!\n"; }






