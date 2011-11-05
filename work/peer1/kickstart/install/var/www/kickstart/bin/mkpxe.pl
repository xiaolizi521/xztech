#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;
use File::Copy;

# ndurr@2009-03-13: This has not been updated in 3 or 4 years and should not 
# be used.  
print "This is very broken do not use.\n";
exit;

my @bootmenu;
my @default;
my @images;
my $header = "SERIAL 0 115200 0x303
PROMPT 0
LABEL linux
";
my $append = " console=ttyS0,115200 console=tty0"; # Make sure first character is a space
#my $append = "";

my $ordfile = $Config->{'ks_home'}."/pxeimages/order.txt";
open IFH, "<$ordfile";
while (<IFH>) {
	chomp;
	my @line = split(/\s+/, $_, 2);
	my $num;
	if (length($.) == 1) { $num = " $."; } else { $num = $. };
	my $padnum = (8 - length($line[0])) + 8;
	my $pad = " "x$padnum;
	#push (@bootmenu, "$num) $line[0]".$pad."$line[1]");
	push (@bootmenu, "$num) $line[0]".$pad);
	push (@images, "$line[0]");
}
close IFH;

open OFH, ">/tftpboot/pxe/bootmenu.txt";
foreach my $ct (0..12) {
	if ($bootmenu[$ct+13]) {
		print OFH $bootmenu[$ct].$bootmenu[$ct+13]."\n";
	}
	else {
		print OFH $bootmenu[$ct]."\n";
	}
}
#foreach (@bootmenu) { print OFH "$_\n"; }
close OFH;

open DEF, ">$Config->{'ks_pxeconf'}/localboot";
print DEF qq{SERIAL 0 115200 0x303
PROMPT 1
TIMEOUT 50
DEFAULT 1

DISPLAY bootmenu.txt

};

foreach my $ct (0..$#images) {
	my $image = $images[$ct];
	my $count = ($ct + 1);
	my @foo;
	my $template = $Config->{'ks_home'}."/pxeimages/$image";
	open IFH, "<$template" || next;
	while (<IFH>) {
		chomp;
		$_ =~ s/MYIPADDR/$Config->{'ks_ipaddr'}/g;
		if (/^APPEND/) { $_ .= $append; }
		push @foo, "$_\n";
	}
	close IFH;

	#print DEF "LABEL $image\n"; print DEF @foo;
	print DEF "# $image\n";
	print DEF "LABEL $count\n"; print DEF @foo;
	print DEF "\n";
	
	my $imgfile = $Config->{'ks_pxeconf'}."/$image";
	print "Creating $imgfile\n";
	open OFH, ">$imgfile";
	print OFH $header; print OFH @foo;
	close OFH;
}

#unlink($Config->{'ks_pxeconf'}."/localboot");
system("cp",$Config->{'ks_pxeconf'}."/localboot", $Config->{'ks_pxeconf'}."/default");

1;
