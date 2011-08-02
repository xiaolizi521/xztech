#!/usr/bin/perl -w

use strict;
use RPM::Perlonly;

my $file2 = "/mirrors/redhat/redhat/linux/updates/rhel3/en/os/i386/up2date-4.2.14-1.i386.rpm";
my $file1 = "/exports/installs/linux/redhat/rhel3/RedHat/RPMS/up2date-4.2.5-1.i386.rpm";

my $oldrpm = new RPM::Perlonly $file1;
my $newrpm = new RPM::Perlonly $file2;

print $oldrpm->cmpver($newrpm)."\n";
