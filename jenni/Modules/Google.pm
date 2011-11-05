# $Id: Google.pm,v 1.5 2003/05/15 16:30:38 jenni Exp $
package Google;

use LWP::UserAgent;

sub init {
	&::addhandler("google", \&google, 0);
	return 0;
}

sub destruct {
	&::delhandler("google");
	return 0;
}

sub google {
	my ($self, $event, $to, $args) = @_;
	my ($ua, $req, $res, $page, $content);

	if (!length($args)) {
		$self->privmsg($to, $event->nick . ": You must supply a search term.");
		return;
	}

	if (!defined($pid = fork)) {
		$self->privmsg($to, $event->nick . ": Unable to fork!");
		return;
	} elsif ($pid) {
		return;
	}

	$url  = "http://www.google.com/search?q=";
	$url .= LibUtil::urlencode($args);
	$url .= "&btnI=";
	$url .= LibUtil::urlencode("I'm Feeling Lucky");

	$ua = LWP::UserAgent->new();
	$ua->agent("jenni/" . $::version . " " . $ua->agent);
	$ua->timeout(5);
	$res = $ua->send_request(new HTTP::Request GET => $url);
	if ($res->is_success) {
		$self->privmsg($to, $event->nick . ": No web pages found.");
	} elsif ($res->is_redirect) {
		($url) = $res->as_string =~ /^Location: (.*)$/m;
		$self->privmsg($to, $event->nick . ": $url");
	} elsif ($res->is_error) {
		$self->privmsg($to, $event->nick . ": An error occurred while contacting Google.");
	}
	exit;
}

1;
