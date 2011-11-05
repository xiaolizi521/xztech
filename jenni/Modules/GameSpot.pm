# $Id: GameSpot.pm,v 1.1 2003/05/14 16:59:00 jenni Exp $
package GameSpot;

use LWP::UserAgent;

sub init {
	&::addhandler("gamespot", \&gamespot, 0);
	return 0;
}

sub destruct {
	&::delhandler("gamespot");
	return 0;
}

sub gamespot {
	my ($self, $event, $to, $args) = @_;
	my ($ua, $req, $res, $page, $content);

	if (length($args) < 1) {
		$self->privmsg($to, $event->nick . ": You forgot to include a game");
		return;
	}

	$url  = "http://www.gamespot.com/finder/findgames.html?q=";
	$url .= LibUtil::urlencode($args);

	$ua = LWP::UserAgent->new(
		timeout => 4,
	);
	$ua->agent("jenni/" . $version . " " . $ua->agent);
	$req = new HTTP::Request GET => $url;
	$res = $ua->request($req);
	if ($res->is_success) {
		$page = $res->content;
		if ($page =~ /No matching game titles found/) {
			$self->privmsg($to, $event->nick . ": No matching game titles found.");
			return;
		}
		$sl = length($page);
		if ($page =~ m|<!-- embedded results table -->(.*?)<!-- end embedded table -->|s) {
			$page = $1;
		} else {
			$self->privmsg($to, $event->nick . ": Couldn't grab results, maybe GameSpot changed their layout?");
			return;
		}
		$ml1 = length($page);
		$page =~ s/\r//g;
		$ml2 = length($page);
		$page =~ s/ class=([^ ]+)//g;
		$ml3 = length($page);
		@results = split(/\n\n\n\n/, $page);
		$rc = 0;
		foreach $result (@results) {
			$result =~ s/\n//g;
			($url, $title, $platform, $scoregs, $scorereader, $releasedate) =
				$result =~ m|
				<td\swidth=250>\&nbsp;<a\shref="([^"]+)">([^<]+)</a></td>
				<td>(?:.*?)?</td> # Downloads
				<td>(?:.*?)?</td> # Media
				<td>(?:.*?)?</td> # Hints
				<td>(?:.*?)?</td> # News
				<td\salign=center><b>([^<]+)</b></td>
				<td\salign=center><b>([^<]+)</b></td>
				<td\salign=center><b>([^<]+)(?:.*?)</td>
				<td\salign=center>([^<]+)</td>
				|x;
			if ($title =~ /$args/i) {
				return if $rc >= 3;
				$line = $event->nick . ": $title ($platform)";
				if ($scoregs ne '--') {
					$line .= " - GS Score: $scoregs";
				} if ($scorereader ne '--') {
					$line .= " - Reader Score: $scorereader";
				} if ($releasedate ne 'n/a') {
					$line .= " - Release Date: $releasedate";
				}
				$line .= " - http://www.gamespot.com$url";
				$line =~ s/index\.html$//;
				$self->privmsg($to, $line);
				$rc++;
			}
		}
		if ($rc < 1) {
			$self->privmsg($to, $event->nick . ": No games found.");
		}
	} else {
		$self->privmsg($to, $event->nick . ": GameSpot is down");
	}
}

1;
