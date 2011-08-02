# =======================================================================
# Company:              PEER 1 Network Enterprises, Inc.
# Copyright(c):         PEER 1 Network Enterprises, Inc. 2008
# Project:              Kickstart Sub-System (Postconf)
# Code Developer:       SAT Product Engineering - B. D. Prewit
# Creation Date:        2010-01-19
#
# File Type:            Perl Script
# File Name:            mongo-install.mod                
#
# Description:
# Postconf module that installs MongoDB, an opensource database manager
# =======================================================================

use strict;
#use lib '/usr/local/sbpost/lib/SB/Common.pm';
#$ks is provided by the main postconf.txt taskfile. It contains all the 
#values passed by postconf to the client system.
my $ks            = $ks;
my $db_pkg        = 'mongo';
my $db_name	  = $db_pkg;
my $workdir       = "$ks->{'sbpost'}/${db_name}";
my $filename 	  = 'installer.sh';
mkdir("$workdir");
chdir("$workdir") || exit 1;

exists $ks->{DATABASE} or postlog("FATAL: No database software defined!");

if(lc($ks->{DATABASE}) eq "mongodb") {
	$db_pkg        	= 'mongodb';
	$db_name       	= 'mongo';
	my $pkg_type	= getPackageManager();
	#download package from KS
	fetchInstaller();
	my @db_pkg_lst     = ("mongodb.gpg");
	fetchMongoPackages(@db_pkg_lst);
} 

#find out if we need a rpm or deb package and download it
sub fetchInstaller 
{
	my $pkg	= "installer.sh";
	my $baseurl	= sprintf("http://%s/installs/db/mongodb", $ks->{ks_ipaddr} );
	my $postres;
	my $fullurl	= "$baseurl/$pkg";
	$postres	= lwpfetch(url => $fullurl, file => "$workdir/$pkg");
	if (($postres->[0] == 0)) {
		postlog("INFO: Download of install script ($fullurl) successful");
	} 
	else {
		postlog("FATAL: Download of install script ($fullurl) failed");
		die "Installation Failed";
	}
	

	chmod(0755, "$workdir/$pkg");
	symlink("$workdir/$pkg", $ks->{'scriptd'}."/89mongodb");
}

sub fetchMongoPackages 
{
	my @pkg_lst     = @_;
	my $baseurl     = sprintf("http://%s/installs/db/mongodb",  $ks->{ks_ipaddr});
	my $postres;
	my $fullurl;

	foreach my $pkg (@pkg_lst)
	{
		$fullurl        = "$baseurl/$pkg";
		$postres        = lwpfetch(url => $fullurl, file => "$workdir/$pkg");
		if (($postres->[0] == 0)) {
			postlog("INFO: Download of package files ($fullurl) successful");
		}
		else {
			postlog("FATAL: Download of package files ($fullurl) failed");
			die "Installation Failed";
		}
	}
	return $postres;
}

sub getPackageManager 
{
	my $os_issue = `cat /etc/issue`;
	if( $os_issue =~ m/.*(Red Hat|Fedora|Centos).*/i ) {
		return 'rpm';
	}
	elsif( $os_issue =~ m/.*(Debian|Ubuntu).*/ ) {
		return 'deb';
	}
	else {
		postlog("FATAL: Unable to identify Operating System");
		return undef;
	}
}


1;
