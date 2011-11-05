#!/usr/bin/perl -w
# =======================================================================
# Company:              Server Beach
# Copyright(c):         Server Beach 2006
# Project:              Kickstart Sub-System
# Code Devloper:        SB Development Team
# Creation Date:        2006-09-14
# Last Updated:         2007-07-18
# Last Updated by:      nate durr
#
# File Type:            perl script
# File Name:            ensimProXlin100.mod
#
# Discription:
# This is the default install script for Ensim X (10.0) module for CentOS 4.2 and RHEL.
#
# Input Parameters:
#	N/A
#
# ======================================================================='

# INSTRUCTIONS ON HOW TO RUN THIS SCRIPT
# How to run me!
# perl -e 'my $ks={}; $ks->{static}="10.11.100.2"; $ks->{PUSER}="delmendo"; $ks->{PPASS}="password"; eval `cat /tmp/ensim10.mod`'

use strict;

# Local Variable declaration
my $tmpdir = $ks->{sbpost}."/ensim";
#my $licdir = $ks->{sbpost}."/licenses";
my $scriptd = $ks->{sbpost}."/script.d";

#Create and change directories.
mkdir($tmpdir);
chdir($tmpdir) || exit 1;

#Declare file to be called
my $file = "installer-ensimProXlin100.sh";
my $baseurl = sprintf("http://%s/installs/panels/ensim/linux/10.x",
	$ks->{ks_ipaddr});
my $fullurl = "$baseurl/$file";

my $postres = lwpfetch(url => $fullurl, file => "$tmpdir/$file");
if (($postres->[0] == 0) && (-f "$tmpdir/$file")) {
	postlog("INFO: Download of $file successful");
} else {
	postlog("FATAL: Download of $file failed");
}

chmod(0755, "$tmpdir/installer-ensimProXlin100.sh");
symlink("$tmpdir/installer-ensimProXlin100.sh", $ks->{scriptd}."/90esm");

1;
