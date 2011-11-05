# $Id: DAoC.pm,v 1.1 2003/05/14 16:58:52 jenni Exp $
package DAoC;

use LWP::UserAgent;

sub init {
	&::addhandler("daoc", \&daoc, 0);
	return 0;
}

sub destruct {
	&::delhandler("daoc");
	return 0;
}

sub daoc {
	my ($self, $event, $to, $args) = @_;
	my ($ua, $req, $res, $page, $content);

	if (length($args) < 1) {
		$self->privmsg($to, $event->nick . ": You forgot to include a nickname");
		return;
	}
	($nick, $server) = split(/ /, $args);
	if (length($server) < 1) {
		$server = "Kay";
	}

	$url  = "http://www.camelotherald.com/guilds/guild-search.php?guildname=&charactername=";
	$url .= LibUtil::urlencode($nick);
	$url .= "&s=";
	$url .= LibUtil::urlencode($server);

	$ua = LWP::UserAgent->new(
		timeout => 4,
	);
	$ua->agent("jenni/" . $version . " " . $ua->agent);
	$req = new HTTP::Request GET => $url;
	$res = $ua->request($req);
	if ($res->is_success) {
		$page = $res->content;
		($nickname)   = $page =~ />The Camelot Herald: ([^<]+)</;
		($rp)         = $page =~ /Realm points:\s+([0-9]+)/;
		($rpweek)     = $page =~ /Realm points earned this week:\s+([0-9]+)/;
		($deaths)     = $page =~ /Total deaths in RvR combat[^:]+:\s+([0-9]+)/;
		($deathsweek) = $page =~ /Deaths in RvR combat this week:\s+([0-9]+)/;
		($irs)        = $page =~ /.I Remain Standing. score:\s+([0-9\.]+)/;
		$message = $event->nick . ": ";
		if ($page =~ /did not return any active guilds/) {
			$message .= "Character not found.";
		} elsif ($page =~ /Search returned multiple characters/) {
			$message .= "Multiple characters found";
		}
		$message .= "$nickname" if $nickname;
		$message .= " / RP (Week): $rp ($rpweek)" if $rp && $rpweek;
		$message .= " / Deaths (Week): $deaths ($deathsweek)" if $deaths && $deathsweek;
		$message .= " / IRS: $irs" if $irs;
		if (!($page =~ /did not return any active guilds/)) {
			$message .= " / URL: " . $res->request->url->as_string;
		}
		$self->privmsg($to, $message);
	} else {
		$self->privmsg($to, $event->nick . ": Doh");
	}
}

1;
