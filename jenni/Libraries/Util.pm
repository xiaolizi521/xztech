# $Id: Util.pm,v 1.10 2003/05/14 16:57:13 jenni Exp $
package LibUtil;

use POSIX;

sub mask {
	@mask = split(/\./, $_[0]);
	$mask = pop(@mask);
	$mask = '*.' . pop(@mask) . ".$mask";
	return($mask);
}

sub channelid {
	my ($channel) = @_;
	my ($dbh) = $::db;
	if (substr($channel, 0, 1) ne '#') {
		return 0;
	}
	$channel = LibUtil::stripforsql($channel);
	$sth = $dbh->query("SELECT channelid FROM channels WHERE channel = '$channel'") || next;
	$channelid = $sth->fetchrow;
	if ($channelid > 0) {
		return $channelid;
	} else {
		$sth = $dbh->query("INSERT INTO channels (channel) VALUES ('$channel')") || next;
		$sth = $dbh->query("SELECT LAST_INSERT_ID()") || next;
		$channelid = $sth->fetchrow;
		return $channelid;
	}
}

sub userid {
	my ($event) = @_;
	my ($dbh) = $::db;
	my ($nick) = $event->nick;
	my ($ident, $host) = split(/\@/, $event->userhost);
	$sth = $dbh->query("SELECT userid FROM users WHERE nick = '$nick' AND ident = '$ident' AND host = '$host'") || next;
	$userid = $sth->fetchrow;
	if ($userid > 0) {
		return $userid;
	} else {
		$sth = $dbh->query("INSERT INTO users (nick, ident, host) VALUES ('$nick', '$ident', '$host')") || next;
		$sth = $dbh->query("SELECT LAST_INSERT_ID()") || next;
		$userid = $sth->fetchrow;
		return $userid;
	}
}

sub getdiff {
	my ($starttime, $endtime) = @_;
	my ($diffstr);
	$difference = $endtime - $starttime;
	$days    = floor($difference / 86400);   $difference -= ($days    * 86400);
	$hours   = floor($difference / 3600);    $difference -= ($hours   * 3600);
	$minutes = floor($difference / 60);      $difference -= ($minutes * 60);
	$seconds = ($difference % 60);
	$diffstr .= $days    . "d " if $days    > 0;
	$diffstr .= $hours   . "h " if $hours   > 0;
	$diffstr .= $minutes . "m " if $minutes > 0;
	$diffstr .= $seconds . "s " if $seconds > 0;
	chop $diffstr;
	if (length($diffstr) == 0) {
		$diffstr = "0s";
	}
	return $diffstr;
}

sub is_admin {
	my $dbh = $main::db;
	$query = "SELECT * FROM admins WHERE '" . $_[0] . "' LIKE mask LIMIT 1";
	$sth = $dbh->query($query);
	@result = $sth->fetchrow;
	if (!$result[1]) {
		return 0;
	}
	else {
		return 1;
	}
}

sub stripall {
	return stripforsql(stripforshell(stripcontrol($_[0]), 0));
}

sub stripforshell {
	$str = $_[0];
	$str =~ s/(\'|\"|\||\>|\<|\`|\;|\\)//g;
	return $str;
}

sub stripforsql {
	$str = $_[0];
	if ($_[1] eq "1") {
		$str =~ s/(select|insert|update|replace|delete|create|drop|count|sum|grant|where|like|benchmark|;)//gi;
	}
	$str =~ s/\\/\\\\/gi;
	$str =~ s/\'/\\\'/gi;
	return $str;
}

sub stripcontrol {
	$str = $_[0];
	$str =~ s/(\003|\003[0-9]+|\003[0-9]+\,[0-9]+)//g;      # Color
	$str =~ s/\002//g;                                      # Bold
	$str =~ s/\031//g;                                      # Underline
	$str =~ s/\022//g;                                      # Reverse
	return $str;
}

sub urlencode {
	$url = $_[0];
	$url =~ s/(\W)/sprintf("%%%x", ord($1))/eg;
	$url =~ s/\%20/+/g;
	return $url;
}

sub is_loaded_module {
	undef %is_module;
	for (@main::modules) { $is_module{$_} = 1 }
	if ($is_module{$_[0]}) {
		return 1;
	}
	else {
		return 0;
	}
}

sub moduleindex {
	for ($i = 0; $i < @main::modules; $i++) {
		if ($main::modules[$i] eq $_[0]) {
			return $i;
		}
	}
	return -1;
}

1;
