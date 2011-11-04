# $Id: Administration.pm,v 1.21 2003/05/14 16:59:26 jenni Exp $
package Administration;

use Libraries::Util;

sub init {
	&::addhandler("chan",    \&channel, 1);
	&::addhandler("channel", \&channel, 1);
	&::addhandler("join",    \&join);
	&::addhandler("part",    \&part);
	&::addhandler("leave",   \&part);
	&::addhandler("admin",   \&admin, 1);
	&::addhandler("uptime",  \&uptime, 0);
	if ($inittime < 1) {
		$inittime = time();
	}
	return 0;
}

sub destruct {
	&::delhandler("chan");
	&::delhandler("channel");
	&::delhandler("join");
	&::delhandler("part");
	&::delhandler("leave");
	&::delhandler("admin");
	&::delhandler("uptime");
	return 0;
}

sub channel {
	my ($self, $event, $to, $chan, $plusminus) = @_;
	my ($db) = $::db;
	if ($plusminus eq '+') {
		$sth = $db->query("INSERT INTO channels (channel, autojoin) VALUES ('" . LibUtil::stripforsql($chan) . "', '1')") || next;
		if ($sth->affectedrows == 0) {
			$db->query("UPDATE channels SET autojoin = '1' WHERE channel = '" . LibUtil::stripforsql($chan) . "'");
		}
		&join($self, $event, $to, $chan);
	}
	elsif ($plusminus eq '-') {
		$sth = $db->query("UPDATE channels SET autojoin = '0' WHERE channel = '" . LibUtil::stripforsql($chan) . "'") || next;
		&part($self, $event, $to, $chan);
	}
	elsif (lc($chan) eq 'list') {
		undef($message);
		$sth = $db->query("SELECT channel FROM channels WHERE autojoin = '1'") || next;
		$message .= $chan . " " while $chan = $sth->fetchrow;
		chop($message);
		$self->privmsg($to, $event->nick . ": $message");
	}
}

sub join {
	my ($self, $event, $to, $chan) = @_;
	if (LibUtil::is_admin($event->userhost)) {
		$self->join($chan);
		$self->privmsg($to, $event->nick . ": Joined $chan");
	}
}

sub part {
	my ($self, $event, $to, $chan) = @_;
	if (LibUtil::is_admin($event->userhost)) {
		$self->part($chan);
		$self->privmsg($to, $event->nick . ": Left $chan");
	}
}

sub admin {
	my ($self, $event, $to, $mask, $plusminus) = @_;
	return if !LibUtil::is_admin($event->userhost);
	my ($db) = $::db;
	$mask =~ s/\*/%/g;
	$mask =~ s/\?/_/g;
	if ($plusminus eq '+') {
		$sth = $db->query("INSERT INTO admins (mask, userid) VALUES ('" . LibUtil::stripforsql($mask) . "', '" . LibUtil::userid($event) . "')") || next;
		$self->privmsg($to, $event->nick . ": Administrator from $mask added");
	}
	elsif ($plusminus eq '-') {
		$sth = $db->query("DELETE FROM admins WHERE mask = '" . LibUtil::stripforsql($mask) . "'") || next;
		if ($sth->affectedrows > 0) {
			$self->privmsg($to, $event->nick . ": Administrator from $mask removed");
		} else {
			$self->privmsg($to, $event->nick . ": No such administrator found");
		}
	}
	elsif (lc($mask) eq 'list') {
		undef($message);
		$sth = $db->query("SELECT mask FROM admins") || next;
		$message .= $mask . " " while $mask = $sth->fetchrow;
		chop($message);
		$self->privmsg($to, $event->nick . ": $message");
	}
}

sub uptime {
	my ($self, $event, $to) = @_;
	$response = $event->nick . ": I have been up for " . LibUtil::getdiff($inittime, time());
	if (abs($inittime - $conntime) > 10) {
		$response .= " and connected for " . LibUtil::getdiff($conntime, time()) . ".";
	} else {
		$response .= ".";
	}
	$self->privmsg($to, $response);
}

sub connect {
	$conntime = time();
	my ($self) = pop;
	my ($db) = $::db;
	$sth = $db->query("SELECT channel FROM channels") || next;
	while ($channel = $sth->fetchrow) {
		$self->join($channel);
	}
}

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	my ($mynick) = $self->nick;
	my ($to) = $event->to;
	if ($arg =~ /^(.{0,3})$mynick(.{0,3}) (.*)/) {
		if ($3 eq "fixname" || $3 eq "fixnick") {
			$self->nick($mynick);
			return 1;
		}
		elsif ($3 eq "version") {
			$self->privmsg($to, $event->nick . ": jenni/" . $::version . " (" . $::versiondate . ") on " . `uname -mnrs`);
			return 1;
		}
		elsif ($3 =~ /^die/i) {
			if (LibUtil::is_admin($event->userhost)) {
				$self->quit($event->nick . " killed me");
				exit;
			}
			else {
				$self->privmsg($to, $event->nick . ": Access denied.");
			}
			return 1;
		}
		elsif ($3 =~ /^(rehash|restart)/i) {
			if (LibUtil::is_admin($event->userhost)) {        
				$self->quit("Restarting...");
				system("./ircbot.pl -f -c " . $::options{"config"});
				exit(0);
			}
			else {
				$self->privmsg($to, $event->nick . ": Access denied.");
			}
			return 1;
		}
		else {
			return 0;
		}
	}
}

1;
