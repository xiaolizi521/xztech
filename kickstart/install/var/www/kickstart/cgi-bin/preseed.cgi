#!/usr/bin/perl -w
# vim:tabstop=4:shiftwidth=4

# Load SBks modules
BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

# Load perl modules
use LWP::Simple;
use XML::Simple;
use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;

# Variable Defitions
my ($ipaddr, $dbh, $macaddr, $macobj, $osload);

# Functions
sub get_deb_based_kscfg {
	my $ks 		= shift();
	my $testing	= shift();

	if ( -e $Config->{ks_home}."/kscfg/$ks.cfg") {
		$kscfg = $Config->{ks_home}."/kscfg/$ks.cfg";
	}
	else {
		kslog('err', "Unable to find /exports/kickstart/kscfg/$ks.cfg");
		return 1;
	}

	#############################################################
	# Read preseed file, substitute placeholders
	#############################################################
	my @kscfg;
	if (-e $kscfg) {
		open IFH, "<$kscfg";
		while (<IFH>)
		{
			chomp;
			if (/(\@\@KSSERVER\@\@)/) { $_ =~ s/$1/$Config->{'ks_host'}/g; }
			if (/(\@\@KSIPADDR\@\@)/) { $_ =~ s/$1/$Config->{'ks_ipaddr'}/g; }
			if (/(\@\@KSDOMAIN\@\@)/) { $_ =~ s/$1/$Config->{'ks_domain'}/g; }
			push(@kscfg, $_);
		}
	}
	else {
		kslog( "err", "[$macaddr] No preseed file found for $ks" );
		return 1;
	}

	#############################################################
	#  Load partition information from partition_master.cgi
	#############################################################
	kslog("info", "[$macaddr] Getting partition recipe from partition_master.cgi.");
	my $ua = LWP::UserAgent->new;
	my $response = $ua->get("http://localhost/cgi-bin/partition_master.cgi?macaddr=$macaddr");
	my $partition_recipe = "";
	if( $response->is_success ) {
		$partition_recipe = $response->decoded_content;
	}
	kslog("info", "[$macaddr] Found parition recipe:\n $partition_recipe");

	#############################################################
	# Pre-config filename, default or per-os
	#############################################################
	my $ksPre;
	if ( -e $Config->{ks_home} . "/kscfg/$ks.pre" ) {
		$ksPre = $Config->{ks_home} . "/kscfg/$ks.pre";
	}
	else {
		$ksPre = $Config->{ks_home} . "/kscfg/preseed.pre";
	}

	if( -e $ksPre ) {
		kslog( "info", "[$macaddr] Using $ksPre." );
		open IFH, "<$ksPre";
		while (<IFH>) {
			chomp;
			if (/(\@\@KSIPADDR\@\@)/) { $_ =~ s/$1/$Config->{'ks_ipaddr'}/g; }
			if (/(\@\@PARTITIONRECIPE\@\@)/) { $_ =~ s/$1/$partition_recipe/g; }
			push( @kscfg, $_ );
		}
		close IFH;
	}




# Print the header info
	print header;

# Write back the preseed info
	if (defined $macaddr) {
		open LOG, ">/tmp/preseed-$macaddr.log";
	}
	else {
		open LOG, "/tmp/preseed-$ipaddr.log";
	}
	foreach my $line (@kscfg) {
		print "$line\n";
		print LOG "$line\n";
	}
	close LOG;
	return 0;
}

# Main
my $testing  = "";

my $post = new CGI;
my $postdata = $post->Vars();
$ipaddr = $ENV{'REMOTE_ADDR'};
if (! defined $ipaddr) {
	kslog( 'err', "I need to be called as a CGI" );
	exit 1;
}

$dbh = ks_dbConnect();
$macaddr = get_mac_from_log($ipaddr);
$macobj = MACFun->new( dbh => $dbh, macaddr => $macaddr );
$osload = $macobj->osload();

$result = get_deb_based_kscfg($osload, $testing);
if ($result == 1) {
	kslog('err', "Preseed failed check logs");
	exit 1;
}
else {
	kslog('info', "$osload preseed requested by $ipaddr for $macaddr");
}
1;
