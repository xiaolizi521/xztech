#!/usr/bin/perl
open(FILE, "<xmlrpcd.log");
my %tmpdbid;
my %tmpeventid;

sub parse {
	print "parsing...\n";
	$x = 1;
	foreach $curr_line (<FILE>) {
		
		chomp($curr_line);
		#print $curr_line . "\n";
		if ($curr_line =~ m/dbid\s[0-9]+\s/) {
						
			$curr_line =~ m/dbid\s([0-9]+)\s/;
			
			$tmpdbid{$1} = time();
			
		}
		
		elsif ($curr_line =~ m/eventid\s+[0-9].*/) {
		
			$curr_line =~ m/eventid\s+([0-9]+)/; 
			
			$tmpeventid{$1} = time();
		}
	
	#print "done with line $x\n";
	$x++;
	#sleep 10;
	}
	print "done parsing... $x lines parsed\n";
	
}
	
# Initial filesize.
$filestats = stat("xmlrpcd.log");

$filesize = $filestats[7];

$first = 1;

while () {
			
		if ($first) {
		
			&parse();
		}
		else {
			
			$stats = stat("xmlrpcd.log");
			
		}
		
	#	print $tmpdbid;
	#	print $tmpeventid;
		while (($dbid, $time) = each(%tmpeventid)) {
		
			#print "eventID: $dbid @ Time: $time \n";
			print keys(%tmpdbid) . "\n";
			print keys(%tmpeventid) . "\n";
			
			if (exists($tmpdbid{$dbid})) {
			
				delete($tmpdbid{$dbid});
				delete($tmpeventid{$dbid});
			#	print "eventID & dbID $dbid found. Deleting off queue. Continuing parsing, No errors at this time.\n";
			}
			
			else {		
			
				while (($dbid,$time) = each(%tmpdbid)) {
					
					
					if (time() - $time >= 60) {
			#			print "dbID: $dbid @ Time: $time \n";
			#			print "Error found. No eventid after 1 minute of time. Rebooting xmlrpcd.";
					}
					else {
			#			print "dbID: $dbid @ Time: $time \n";
			#			print "No error found. Still " . time() - $time . "seconds left.\n";
					}
				}
			}
			
		}	
	print ".\n";			
	sleep 1;
	
	
}

