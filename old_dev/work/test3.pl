#!/usr/bin/perl
open(FILE, "<xmlrpcd.log");

@lines = <FILE>;

	for ($x = scalar(@lines); $x > (scalar(@lines) - 20); $x--) {
		
		if ($lines[$x] =~ m/dbid\s[0-9].*/ig) {
			
			
			
			$lines[$x] =~ m/dbid\s([0-9]+)/;
			
			print $1 . "\n";
			
		}

}


