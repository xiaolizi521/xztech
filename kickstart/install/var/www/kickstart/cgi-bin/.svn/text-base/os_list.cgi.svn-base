#!/usr/bin/perl -w

# ccamacho 2010-12-08
# This is created as a part of the Kickstart QA Automation project.
# Gets a list of OSes and related addons.

BEGIN {
	use lib "/exports/kickstart/lib";
	require 'sbks.pm';
}

use CGI ':cgi-lib';
use CGI ':standard';
use XML::Simple;

my $dbh = ks_dbConnect();
my $result = $dbh->selectall_arrayref("SELECT o.id AS os_id, o.osload, a.id AS addon_id, a.display_name FROM os_list AS o JOIN xref_os_addon AS x ON o.id = x.os_list_id JOIN addon_list AS a ON x.addon_list_id = a.id ORDER BY o.osload ASC, a.display_name ASC");

# Build a hash of OSes that is easy for XMLout() to process.
my $prev_os = $result->[0];
my $os_list = {};
my $addons = [];
foreach my $row (@$result) {
	my ($os_id, $osload, $addon_id, $addon_name) = @{$row};
	
	# If os isn't in the list, add it
	if (!exists($os_list->{$osload})) {
		$os_list->{$osload} = {
			'id'=>$os_id,
			'name'=>$osload,
			'addon'=>()
		};
	}
	
	# Add addon to OS' list of addons
	push( @{$os_list->{$osload}->{'addon'}}, {'id' => $addon_id, 'name'=>$addon_name} );
}

# Print XML
print header('application/xml');

my $os_hash = {};
$os_hash->{'os'} = $os_list;
print XMLout($os_hash, ('rootname'=>'oslist'));
