#!/usr/bin/perl
use strict;
use IO::Socket::INET;
use POSIX qw(strftime :sys_wait_h);
use File::Basename;
use vars qw($logfile $sock $listen_port $ksipaddr405 $scriptname);

$logfile	= "/var/log/rapidreboot.log";
$listen_port	= 9090;
$scriptname	= basename($0);
# Hack to dynamically find our IP for VLAN 405 since hardcoding is bad and there are no libs to do this yet in perl
$ksipaddr405	= `/sbin/ifconfig vlan405 | /usr/bin/awk '( \$2 ~ /addr:10./ ) { print \$2 }' | /usr/bin/cut -d : -f 2`;
chomp($ksipaddr405);

sub log_it {
	my $line = shift;
	chomp($line);
	unless(open(LOGFILE,">> $logfile")) {
		return -1;
	}
	my $ts = strftime "%Y%m%d-%H:%M:%S (%Z)",localtime;
	print LOGFILE "${ts}: $line\n";
	close(LOGFILE);
}

# setup our SIGCHLD handler, so we don't accumulate zombies.
sub REAPER {
	my %child_status;
	my $child;
	while (($child = waitpid(-1,WNOHANG)) > 0) {
		$child_status{$child} = $?;
	}
	$SIG{CHLD} = \&REAPER;			# reinstall signal handler
}

$SIG{CHLD} = \&REAPER;

# handy function for forking off children
sub spawn {
	my $cr = shift;
	unless (@_ == 0 && $cr && ref($cr) eq 'CODE') {
		return;
	}
	my $p = fork;
	if ($p) {
		return;
	}
	open(STDIN,"< /dev/null");
	open(STDOUT,"> /dev/null");
	open(STDERR,"> /dev/null");
	my $z = &$cr();
	exit $z;
}

### MAIN ###

# Ensure we have an IP address to bind to
if (!defined ($ksipaddr405) || ($ksipaddr405 eq "")) {
	print "$scriptname was unable to find the IP for the 405 VLAN for Kickstart. Exiting.\n";
	exit 1;
}

$sock = IO::Socket::INET->new(
	LocalAddr	=> $ksipaddr405,
	LocalPort	=> $listen_port,
	Listen		=> 100,
	Proto		=> 'tcp');

# Make sure our $listen_port isn't already taken.
unless(defined($sock)) {
	print "$scriptname cannot listen on $ksipaddr405:$listen_port\nReason: $!\n";
	exit 1;
}

# Print out that we've successfully started our daemon.
print "$scriptname has successfully bound to $ksipaddr405:$listen_port!\n";

my $pid = fork();
exit if ($pid);

open(STDIN,"< /dev/null");
open(STDOUT,"> /dev/null");
open(STDERR,"> /dev/null");

while (1) {
	while (my $client = $sock->accept()) {
		spawn sub {
			my $line = <$client>;
			chomp($line);
			log_it($client->peerhost() . ": " . $line);
			$client->shutdown(2);
			close($client);
			exit;
		};
	}
}

