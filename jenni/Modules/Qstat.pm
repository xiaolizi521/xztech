# $Id: Qstat.pm,v 1.1 2003/02/15 02:55:41 jenni Exp $
package Qstat;

sub init {
	&::addhandler("q3stat", \&q3stat);
	return 0;
}

sub destruct {
	&::delhandler("q3stat");
	return 0;
}

sub parseparam {
	my ($param, $defport) = @_;
	if ($param =~ /^(([0-9]{1,3}\.){3}[0-9]{1,3})( .+)?$/) {
		$ip = $1;
		$port = $defport;
		$nick = $3;
	} elsif ($param =~ /^(([0-9]{1,3}\.){3}[0-9]{1,3}):([0-9]{5})( .+)?$/) {
		$ip = $1;
		$port = $3;
		$nick = $4;
	}
	$nick =~ s/^\s+//;
	return ($ip, $port, $nick);
}

sub q3stat {
	my ($self, $event, $to, $rawhost) = @_;
	my $topnick, $topfrags;
	($ip, $port, $player) = &parseparam($rawhost, 27960);
	@rv = `/home/jenni/qstat -P -raw ":" -q3s $ip:$port`;
	$i = 0;
	$topfrags = -1;
	%frags = ();
	foreach $line (@rv) {
		$i++;
		if ($i == 1) {
			(undef, undef, undef, $name, $map, $maxplayers, $curplayers, $ping, undef) = split(/:/, $line);
			$name =~ s/^\s+//;
			$name =~ s/\s+$//;
		} else {
			if ($line =~ /^(.*?):([0-9]+):[0-9]+$/) {
				$frags{$1} = $2;
				if ($2 > $topfrags) {
					$topnick = $1;
					$topfrags = $2;
				}
			}
		}
	}
	if ($name eq 'DOWN') {
		$self->privmsg($to, $event->nick . ": Server unavailable.");
		return 0;
	}
	@byfrags = sort {$frags{$b} <=> $frags{$a}} keys %frags;
	%ranks = ();
	@byrank = ();
	$i = 0;
	$j = 0;
	$last = 0;
	$playertext = "";
	foreach $nick (@byfrags) {
		$i++;
		if ($frags{$nick} != $last) {
			$j = $i;
		}
		$last = $frags{$nick};
		$ranks{$nick} = $j;
		$byrank[$j]++;
	}
	if ($player) {
		foreach $nick (@byfrags) {
			if ($nick eq $player) {
				$playertext = $nick;
				if ($byrank[$ranks{$nick}] == 2) {
					$playertext .= " is tied for ";
				} elsif ($byrank[$ranks{$nick}] > 2) {
					$playertext .= " is tied (" . $byrank[$ranks{$nick}] . " way) for ";
				} else {
					$playertext .= " is in ";
				}
				if ($ranks{$nick} == 1) {
					$playertext .= "1st place";
				} elsif ($ranks{$nick} == 2) {
					$playertext .= "2nd place";
				} elsif ($ranks{$nick} == 3) {
					$playertext .= "3rd place";
				} else {
					$playertext .= $ranks{$nick} . "th place";
				}
				$playertext .= " with " . $frags{$nick} . " frags";
			}
		}
		if ($playertext eq '') {
			$playertext = "$player is not on the server";
		}
	} else {
		$playertext = "$topnick is in the lead with $topfrags frags";
	}
	$self->privmsg($to, $event->nick . ": $name - $map - $curplayers/$maxplayers ($playertext)");
}

1;
