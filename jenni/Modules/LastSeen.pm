# $Id: LastSeen.pm,v 1.11 2003/05/14 16:58:32 jenni Exp $
package LastSeen;

use POSIX qw(strftime);

sub init {
	&::addhandler("seen", \&seen, 0, 1, 0);
	&::addhandler("linecount", \&linecount);
	&::addhandler("mostactive", \&mostactive);
	&::addhandler("mostchars", \&mostchars);
	&::addhandler("mostratio", \&mostratio);
	return 0;
}

sub destruct {
	&::delhandler("seen");
	&::delhandler("linecount");
	&::delhandler("mostactive");
	&::delhandler("mostchars");
	&::delhandler("mostratio");
	return 0;
}

sub updateseen {
	my ($event, $lastwords, $charcount) = @_;
	my ($db) = $::db;
	$userid = LibUtil::userid($event);
	$channelid = LibUtil::channelid($event->to);
	$lastwords = LibUtil::stripforsql($lastwords);
	$sth = $db->query("UPDATE lastseen SET lastts = NOW(), channelid = '$channelid', lastwords = '$lastwords', linecount = linecount + 1, charcount = charcount + '$charcount' WHERE userid = '$userid'") || next;
	if ($sth->affectedrows == 0) {
		$db->query("INSERT INTO lastseen (userid, lastts, firstts, channelid, lastwords, linecount, charcount) VALUES ('$userid', NOW(), NOW(), '$channelid', '$lastwords', 1, '$charcount')") || next;
	}
}

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($db) = $::db;
	my ($arg) = $event->args;
	return 0 if $arg =~ /^seen/i;
	&updateseen($event, $arg, length($arg));
	return 0;
}

sub action {
	shift @_;
	my ($self, $event) = @_;
	my (@args) = $event->args;
	shift @args;
	$arg = join(" ", @args);
	$charcount = length($arg);
	$arg = "* " . $event->nick . " " . $arg;
	&updateseen($event, $arg, $charcount);
	return 0;
}

sub seen {
	my ($self, $event, $to, $nick) = @_;
	my ($db) = $::db;
	my ($mynick) = $self->nick;
	if ($nick =~ /^([A-Za-z0-9\[\\\]\^_`\{\|}\-\s]+)$/) {
		$nick = LibUtil::stripforsql($1, 0);
		return if $nick =~ /^$mynick$/;
		$nick =~ s/\s+/\', \'/g;
		if ($sth = $db->query("SELECT u.nick, u.ident, u.host, c.channel, DATE_FORMAT(l.lastts, '%M %e, %Y @ %r'), UNIX_TIMESTAMP(), UNIX_TIMESTAMP(l.lastts), l.lastwords FROM lastseen AS l INNER JOIN users AS u ON l.userid = u.userid LEFT JOIN channels AS c ON (l.channelid = c.channelid) WHERE l.userid = u.userid AND u.nick IN ('$nick') ORDER BY l.lastts DESC LIMIT 1")) {
			if (($seennick, $seenident, $seenhost, $seenchannel, $seendate, $nowtimestamp, $timestamp, $lastwords) = $sth->fetchrow) {
				if (length($seenchannel) < 1) {
					$seenchannel = "a private message";
				}
				$self->privmsg($to, $event->nick . ": I last saw $seennick ($seenident\@$seenhost) on $seendate (" . LibUtil::getdiff($timestamp, $nowtimestamp) . " ago) in $seenchannel. The last thing he/she said was \"$lastwords\"");
			}
			else {
				$self->privmsg($to, $event->nick . ": I have never seen that person before");
			}
		}
		else {
			$self->privmsg($to, $event->nick . ": I have never seen that person before");
		}
	} elsif (($hnick, $hident, $hhost) = ($nick =~ /^([^!]+)!([^\@]+)\@(.*)$/)) {
		$hnick  =~ s/\*/\%/g;
		$hident =~ s/\*/\%/g;
		$hhost  =~ s/\*/\%/g;
		if ($sth = $db->query("SELECT u.nick, u.ident, u.host, DATE_FORMAT(l.lastts, '%M %e, %Y @ %r'), UNIX_TIMESTAMP(), UNIX_TIMESTAMP(l.lastts), l.lastwords FROM lastseen AS l, users AS u WHERE l.userid = u.userid AND u.nick LIKE '$hnick' AND u.ident LIKE '$hident' AND u.host LIKE '$hhost' ORDER BY l.lastts DESC LIMIT 1")) {
			if (($seennick, $seenident, $seenhost, $seendate, $nowtimestamp, $timestamp, $lastwords) = $sth->fetchrow) {
				$self->privmsg($to, $event->nick . ": I last saw $seennick ($seenident\@$seenhost) on $seendate (" . LibUtil::getdiff($timestamp, $nowtimestamp) . " ago). The last thing he/she said was \"$lastwords\"");
			} else {
				$self->privmsg($to, $event->nick . ": I have never seen that person before");
			}
		} else {
			$self->privmsg($to, $event->nick . ": I have never seen that person before");
		}
	}
}

sub linecount {
	my ($self, $event, $to, $nick) = @_;
	my ($db) = $::db;
	$res = $db->query("SELECT sum(linecount), sum(charcount) FROM lastseen AS l, users AS u WHERE l.userid = u.userid AND u.nick = '" . LibUtil::stripforsql($nick) . "'") || next;
	($linecount, $charcount) = $res->fetchrow;
	if ($linecount < 1) {
		$linecount = "no";
		$charcount = "no";
		$cpl = "N/A";
	} else {
		$cpl = sprintf("%01.1f", $charcount / $linecount);
	}
	$self->privmsg($to, $event->nick . ": $nick has said $linecount lines and typed $charcount letters (average letters per line: $cpl).");
}

sub mostactive {
	my ($self, $event, $to, undef) = @_;
	my ($db) = $::db;
	$message = $event->nick . ": Top 10 IRCers: ";
	$res = $db->query("SELECT u.nick, sum(linecount) AS linecount FROM lastseen AS l, users AS u WHERE l.userid = u.userid GROUP BY u.nick ORDER BY linecount DESC LIMIT 10");
	while (($nick, $linecount) = $res->fetchrow) {
		$message .= "$nick ($linecount), ";
	}
	$message =~ s/, $//;
	$self->privmsg($to, $message);
}

sub mostchars {
	my ($self, $event, $to, undef) = @_;
	my ($db) = $::db;
	$message = $event->nick . ": Top 10 IRCers by chars: ";
	$res = $db->query("SELECT u.nick, sum(charcount) AS charcount FROM lastseen AS l, users AS u WHERE l.userid = u.userid GROUP BY u.nick ORDER BY charcount DESC LIMIT 10");
	while (($nick, $charcount) = $res->fetchrow) {
		$message .= "$nick ($charcount), ";
	}
	$message =~ s/, $//;
	$self->privmsg($to, $message);
}

sub mostratio {
	my ($self, $event, $to, undef) = @_;
	my ($db) = $::db;
	$message = $event->nick . ": Top 10 IRCers by ratio: ";
	$res = $db->query("SELECT u.nick, IFNULL(IF(SUM(linecount) > 1000, SUM(charcount), 0) / SUM(linecount), 0) AS ratio FROM lastseen AS l, users AS u WHERE l.userid = u.userid GROUP BY u.nick ORDER BY ratio DESC LIMIT 10");
	while (($nick, $ratio) = $res->fetchrow) {
		$message .= "$nick ($ratio), ";
	}
	$message =~ s/, $//;
	$self->privmsg($to, $message);
}

1;
