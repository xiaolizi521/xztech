#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my @files = qw(default mhrescue localboot);

	foreach my $myFile (@files) {  
  		`perl -pi -e 's/KS_IPADDR/$Config->{'ks_public_ipaddr'}/g' /tftpboot/pxe/pxelinux.cfg/$myFile`; 
	}
