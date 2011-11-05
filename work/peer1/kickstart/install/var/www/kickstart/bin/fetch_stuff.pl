#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

exit 1 unless(scalar(@ARGV) == 2);
$ARGV[0] =~ /^((\w{2}:){5}\w{2})$/;
my $macaddr = $1;
$ARGV[1] =~ /^(postconf|licenses)$/;
my $object  = $1;
exit 1 unless(($macaddr) && ($object));

my $dbh = ks_dbConnect();
my $mobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);

if ($object eq "postconf") {
	my $postconf = fetch_postconf($macaddr);
    while (my($param,$value) = each %$postconf) { print "$param = $value\n"; }
	if ($postconf) { $mobj->postconf($postconf); }
}
elsif ($object eq "licenses") {
	my $licenses = fetch_licenses($macaddr);
	if ($licenses) {
        $mobj->licenses($licenses);
        open OFH, ">licenses.tgz";
        print OFH MIME::Base64::decode($licenses);
        close OFH;
    }
}

$mobj->update();
my $error = $mobj->error();
if ($error) {
    print "Errors: ".join(" ", @{$error})."\n";
}
else { print "no error\n"; }

$dbh->disconnect();

1;
