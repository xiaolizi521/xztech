#!/usr/bin/perl -w

use strict;

sub get_rndcinfo {
	my $read = 0;
	my $return = [];
	open IFH, "</etc/rndc.conf";
	while (<IFH>) {
		chomp;
		if (/^key\ \"(.*)\"/) { $return->[0] = $1; }
		elsif (/algorithm\ (.*);$/) { $return->[1] = $1; }
		elsif (/secret\ \"(.*)\";$/) { $return->[2] = $1; }

		if ($return->[2]) { last; }
	}
	close IFH;
	return $return;
}

sub print_rndckey {
	my $input = shift();
	open RNDCKEY, ">/etc/rndc.key";
	print RNDCKEY "key \"$input->[0]\" \{
\talgorithm $input->[1]\;
\tsecret \"$input->[2]\"\;
\}\;\n";
	close RNDCKEY;
	return 0;
}

sub fix_namedconf {
	my $input = shift();
	my @named;
	open NAMED, "</etc/named.conf";
	while (<NAMED>) {
		chomp;
		s/rndckey/$input->[0]/g;
		push(@named, $_);
	}
	close NAMED;
	open NAMED, ">/etc/named.conf";
	foreach (@named) { print NAMED "$_\n"; }
	close NAMED;
	return 0;
}

my $rndcinfo = get_rndcinfo();

print_rndckey($rndcinfo);
fix_namedconf($rndcinfo);
