#!/usr/bin/perl -w

BEGIN {
	use lib qw(/exports/kickstart/lib);
	require 'sbks.pm';
}

use strict;

my ($dbh, $pipe);

if (is_running()) { exit 0; }

# dm@09-04-2006: can't tell if this thing is running or not, so print
# a simple banner
print "$0 Starting\n";

kslog("info", "Starting");
    
chdir("/");

$dbh = ks_dbConnect();
$pipe = $Config->{'ks_status'}."/status.log";

if ( ! -e $pipe ){
        kslog( 'info', $pipe . " doesn't exist!. Do we need it?" );
        print "Error, $pipe doesn't exist!. Do we need it?";
}

while (1) {
	if (!$dbh->ping()) {
		print "dbConnect()\n";
		$dbh = ks_dbConnect();
		$dbh->errstr() && die "dbh: ".$dbh->errstr();
	}
	open IFH, "<$pipe";
	while (<IFH>) {
		chomp;
		print "INPUT: $_\n";
		do_stuff($_);
	}
	close IFH;
        sleep 1;
}

sub do_stuff {
	my $input = shift;
	my($macaddr, $status) = split(/\s+/, $input);
	$macaddr =~ s/-/:/g;
    $macaddr = untaint('macaddr', $macaddr) || return 0;

	my @validstats = ("prep", "part", "partdone", "copy", "copydone");
	return unless grep(/^$status$/, @validstats);
	my $newstatus = "win2k_".$status;

	my $macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
	my $oldstatus = $macobj->status();
	if ($newstatus eq $oldstatus) { $dbh->disconnect; return 0; }

	print "$macaddr $oldstatus -> $newstatus\n";
	kslog('info', "$macaddr STATUS -> $newstatus");

	$macobj->status("$newstatus");

	my $osload = $macobj->osload();
	my $last3 = $macobj->halfmac();
	$last3 =~ s/:/-/g;
	$last3 = uc($last3);

	(my $realos = $osload) =~ s/(.*)-(i|r)tl/$1/;

	if ($newstatus =~ /partdone$/) {
		$macobj->pxe("sbrescue");
		if (new_update_pxe($macaddr, "sbrescue")) {
			unlink($Config->{ks_status}."/partdone/$last3.TXT");
			my $task = $realos."-copy";
			$macobj->task($task);
		}
	}

	$macobj->update();
}	

$dbh->disconnect();
1;
