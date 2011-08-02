#!/usr/bin/perl -w
# =======================================================================
# Company:              Server Beach
# Copyright(c):         Server Beach 2006
# Project:              Kickstart Sub-System
# Code Devloper:        SB Development Team
# Creation Date:        2006-09-14
#
# File Type:            Taskfile
# File Name:            burnin.txt
#
# Discription:
# This script is the primary script that is called directly from the pxe image
# for all Red Hat based kicks (RHBK). It servers as the launch point from which
# a RHBK locates pre, during, and post configuration information.
#
# =======================================================================

# File inlcudes.
BEGIN {
	use lib '/exports/kickstart/lib';
	require 'sbks.pm';
}

use strict;
use LWP::Simple;
use XML::Simple;
use CGI ':cgi-lib';
use CGI ':standard';

# Variable Defitions
my ( $dbh, $ipaddr, $macaddr, $macobj, $status, $osload );

# ############################################
# FUNCTION DEFFITIONS
# ############################################
sub get_kscfg {
	my $ks      = shift();
	my $testing = shift();

	#############################################################
	#Set pointer to the OS kickstart configuration file information
	#############################################################
	my $kscfg = $Config->{ks_home} . "/kscfg/$ks.kscfg";

	#############################################################
	#Set pointer to the OS kickstart pre-configuration file information
	#############################################################
	my $kspre = $Config->{ks_home} . "/kscfg/rhcommon.pre";
	if ( -e $Config->{ks_home} . "/kscfg/$ks.pre" ) {
		$kspre = $Config->{ks_home} . "/kscfg/$ks.pre";
	}

	#############################################################
	#Set pointer to the Custom partition pre-configuration
	#############################################################
	my $cpPre = $Config->{ks_home} . "/kscfg/custompart/rhcommon.pre";
	if ( -e $Config->{ks_home} . "/kscfg/custompart/$ks.pre" ) {
		$cpPre = $Config->{ks_home} . "/kscfg/custompart/$ks.pre";
	}

	#############################################################
	#Set pointer to the OS kickstart post-configuration file information
	#############################################################
	my $kspost = $Config->{ks_home} . "/kscfg/rhcommon.post";
	if ( -e $Config->{ks_home} . "/kscfg/$ks.post" ) {
		$kspost = $Config->{ks_home} . "/kscfg/$ks.post";
	}

	#############################################################
	#Write out the following configuration files:
	#############################################################
	#############################################################
	#kickstart configuration file
	#############################################################
	my @kscfg;
	if ( -e $kscfg ) {
		open IFH, "<$kscfg";
		while (<IFH>) {
			chomp;
			if (/(\@\@KSSERVER\@\@)/) { $_ =~ s/$1/$Config->{'ks_host'}/g; }
			if (/(\@\@KSIPADDR\@\@)/) { $_ =~ s/$1/$Config->{'ks_ipaddr'}/g; }
			if (/(\@\@KSDOMAIN\@\@)/) { $_ =~ s/$1/$Config->{'ks_domain'}/g; }
			push( @kscfg, $_ );
		}
                close IFH;
	}
	else {
		kslog( "err", "[$macaddr]Kickstart config couldn't be found." );
		return 1;
	}

        #############################################################
        #kickstart pre-configuration file
        ############################################################# 
        my $noCustomPart = 1;
        if ( -e $cpPre ) {
                my $cpResource = "http://$Config->{'ks_ipaddr'}/upi/devices/$macaddr/kickstart/partition";
                my $cpXml       = get($cpResource);
                my $cpSimpleXml = new XML::Simple;
                my $cpXmlData   = $cpSimpleXml->XMLin($cpXml);
                if ( $cpXmlData->{get}->{status} =~ /success/i ) {
                        kslog( "info", "[$macaddr]Found a custom partitioning scheme." );
                        push( @kscfg, '%pre --interpreter /bin/sh' );
                        if ($testing) { push( @kscfg, "export testing=1" ); }
                        $noCustomPart = 0;
                        open IFH, "<$cpPre";
                        while (<IFH>) {
                                chomp;
                                if (/(\@\@CUSTOMPART\@\@)/) {
                                        $_ =~ s/$1/$cpXmlData->{get}->{message}/g;
                                }
                                if (/(\@\@KSIPADDR\@\@)/) {
                                        $_ =~ s/$1/$Config->{'ks_ipaddr'}/g;
                                }
                                push( @kscfg, $_ );
                        }
                        close IFH;
                }
        } 
        if ( -e $kspre && $noCustomPart ) {
                kslog( "info", "[$macaddr]Found no custom partitioning scheme. Using default." );
		push( @kscfg, '%pre --interpreter /bin/sh' );
		if ($testing) { push( @kscfg, "export testing=1" ); }
		open IFH, "<$kspre";
		while (<IFH>) {
			chomp;
			if (/(\@\@KSIPADDR\@\@)/) { $_ =~ s/$1/$Config->{'ks_ipaddr'}/g; }
			push( @kscfg, $_ );
		}
		close IFH;
	}

	#############################################################
	# kickstart post-configuration file
	#############################################################
	if ( -e $kspost ) {
		push( @kscfg, '%post --interpreter /bin/sh' );
		if ($testing) { push( @kscfg, "export testing=1" ); }
		open IFH, "<$kspost";
		while (<IFH>) {
			chomp;
			if (/(\@\@KSSERVER\@\@)/) { $_ =~ s/$1/$Config->{'ks_host'}/g; }
			if (/(\@\@KSIPADDR\@\@)/) { $_ =~ s/$1/$Config->{'ks_ipaddr'}/g; }
			if (/(\@\@KSDOMAIN\@\@)/) { $_ =~ s/$1/$Config->{'ks_domain'}/g; }
			push( @kscfg, $_ );
		}
		close IFH;
	}

	#############################################################
	#Log Activity to local file.
	#############################################################
	open LOG, ">/tmp/kscfg-$macaddr.log";
	foreach my $line (@kscfg) {
		print "$line\n";
		print LOG "$line\n";
	}
	close LOG;
	return 0;
}

#$ipaddr = $ENV{'REMOTE_ADDR'};
#if (!defined($ipaddr)) {
#	kslog('err', "I need to be called as a CGI");
#	exit 1;
#}

# ############################################
# Program MAIN
# ############################################

print header;

#Instantiate new CGI Object
my $post     = new CGI;
my $postdata = $post->Vars();

$ipaddr = $ENV{'REMOTE_ADDR'};
if ( !defined($ipaddr) ) {
	kslog( 'err', "I need to be called as a CGI" );
	exit 1;
}

$dbh = ks_dbConnect();

###############################################################################
#
#   # Read in variables that were posted to this cgi.
#   # Red Hat was nice enough to add this HTTP environment variable to the HTTP
#   # headers issued by anaconda.  Saves us a database call if present.
#   if (my $tmpvar = $post->http("HTTP_X_RHN_PROVISIONING_MAC_0")) {
#	    $tmpvar =~ s/^.*\s+//g;
#	    $macaddr = untaint('macaddr', $tmpvar);
#	    kslog("info", "Got MAC ($macaddr) from \$post->http()");
#   }
#   elsif ($post->param("macaddr")) {
#       $macaddr = untaint('macaddr', $post->param("macaddr"));
#	    kslog("info", "Got MAC ($macaddr) from \$post->param()");
#   }
#   else {
#	    $macaddr = get_mac_by_ip($dbh, $ipaddr);
#	    kslog("info", "Got MAC ($macaddr) from database");
#   }
#
###############################################################################

if ( $post->param("macaddr") ) {
	$macaddr = untaint( 'macaddr', $post->param("macaddr") );
	kslog( "info", "Got MAC ($macaddr) from \$post->param()" );
}
else {
	$macaddr = get_mac_from_log($ipaddr);
	if ( !defined($macaddr) ) {
		kslog( 'err', "Unable to find a MAC for $ipaddr" );
		exit 0;
	}
	else {
		kslog( "info", "Got MAC ($macaddr) via IP ($ipaddr) from log" );
	}
}

# Instantiate new MACFun Object.
$macobj = MACFun->new( dbh => $dbh, macaddr => $macaddr );
$status = $macobj->status();
$osload = $macobj->osload();

my $testing  = "";
my $postconf = $macobj->postconf();

if( ! defined( $postconf->{customer_number} ) ) {
        kslog( 'err', "[$macaddr]No postconf information found for IP $ipaddr" );
} elsif ( $postconf->{customer_number} == 4 ) {
        # Check to see if the customer is a development
        # testing account.
        $testing = "1";
}

# Get a list of Kickstart PXE Kicks
my @installs = get_ks_list($dbh);

# Search through the oslad variable to see if it matches one
# in the kickstart configuration list.
if ( grep( /^$osload$/, @installs ) ) {
	if ( $osload =~ /up$/ || $status !~ /kickstarted|online/ ) {
		my $result = get_kscfg( $osload, $testing );
		if ( $result == 0 ) {
			$macobj->status("ksscript");
			$macobj->update();
			kslog( 'info', "$macaddr STATUS -> ksscript ($osload)" );
		}
		else {
			$macobj->status("ksscript_fail");
			$macobj->update();
			kslog( 'err', "$macaddr FAILED -> ksscript ($osload)" );
		}
	}
	else {
		$macobj->pxe("localboot");
		$macobj->update();
		update_pxe( $macaddr, "localboot" );
		kslog( 'err', "$macaddr WARNING ATTEMPTED REKICKSTART" );
	}
}
else {
	get_kscfg( $osload, $testing );
	kslog( 'info', "$macaddr got $osload" );
}

1;


