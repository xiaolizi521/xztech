#!/usr/bin/perl -w
# =======================================================================
# Company:              PEER 1 Network Enterprises, Inc.
# Copyright(c):         PEER 1 Network Enterprises, Inc. 2008
# Project:              Kickstart Sub-System (Postconf)
# Code Developer:       Product Engineering
# Creation Date:        2008-10-30
#
# File Type:            Perl Script
# File Name:            shepherd.mod                
#
# Description:
# Postconf module that installs System Shepherd. A monitoring system 
# provided owned by Absolute Performance, Inc.
# =======================================================================

use strict;

#$ks is provided by the main postconf.txt taskfile. It contains all the 
#values passed by postconf to the client system.
my $ks             = $ks;
my $mon_pkg        = '';
my $mon_name       = '';
my $mon_version    = '';
my $mon_revision   = '';
my $workdir        = "$ks->{'sbpost'}/${mon_pkg}";

exists $ks->{MONITORING} || postlog("FATAL: No monitoring software defined!");

if( lc( $ks->{MONITORING} ) eq "sshepherd" ){
	$mon_name     = 'SysShep-s-RHAS4';
	$mon_version  = '5.0.0';
        $mon_revision = '1';
        $mon_pkg      = "${mon_name}-${mon_version}-${mon_revision}.i386";
	#download package from KS
	fetchMonPackage();
        #Install the package
        installMonPackage();
        #Configure the client
        installMonClient();
} 

#install and configure the monitoring client software.
sub installMonClient {
	#In order to configure non-interactively you must pass 
	#the following 5 arguments to the setup (in order):
	my $SS_USERNAME  = 'agent3682x176'; #User name
	my $SS_PASSWORD  = 'pw3682'; #Password (case sensitive)
	my $SS_BACKEND   = 'http://core.dev.peer1.sysshep.com/agents/39'; #System Shepherd Backend FQDN
	my $SS_TEMPLATE  = ''; #Base Template to use
	my $SS_INSTALLER =  "/opt/${mon_name}/bin/configure";
	chmod(0755, "${SS_INSTALLER}");
	my $ss_install = `${SS_INSTALLER} ${SS_USERNAME} ${SS_PASSWORD} ${SS_BACKEND} ${SS_TEMPLATE}`
	postlog("INFO: ${ss_install}");
}

#install the (previously) downloaded rpm/deb package
sub installMonPackage {
	my $cmd         = '';
	my $pkg_manager = lc( getPackageManager() );
	my $params      = "${workdir}/${mon_pkg}.${pkg_manager}";
	if( $pkg_manager eq 'rpm' ) {
		`rpm -Uvh $params`;
	} elsif ( $pkg_manager eq 'deb' ) {
		`dpk -i $params`;
	} else {
		postlog("FATAL: Unsupported package manager");
	}
}

#find out if we need a rpm or deb package and download it
sub fetchMonPackage {
	my $pkg_manager = lc( getPackageManager() );
	my $baseurl     = sprintf("http://%s/installs/linux/serverbeach/", $ks->{ks_ipaddr} );
	my $fullurl     = $baseurl . $mon_pkg . ".${pkg_manager}";
	my $postres     = lwpfetch(url => $fullurl, file => "${workdir}/${mon_pkg}.${pkg_manager}");
	if ( ($postres->[0] == 0) ) {
        postlog("INFO: Download of install script ($file) successful");
        } else {
        postlog("FATAL: Download of install script ($file) failed");
        }
	return $$postres;
}

sub getPackageManager {
	my $os_issue = `cat /etc/issue`;
	if( $os_issue =~ m/.*(Red Hat|Fedora|Centos).*/ ) {
		return 'rpm';
	} elsif ( $os_issue =~ m/.*(Debian|Ubuntu).*/ ) {
		return 'deb';
	} else {
		postlog("FATAL: Unable to identify Operating System");
		return undef;
	}
}


1;
