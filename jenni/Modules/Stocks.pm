# $Id: Stocks.pm,v 1.6 2002/03/03 20:21:31 jenni Exp $
package Stocks;

use LWP::UserAgent;

sub init {
	&::addhandler("stock",  \&stocks);
	&::addhandler("stocks", \&stocks);
	return 0;
}

sub destruct {
	&::delhandler("stock");
	&::delhandler("stocks");
	return 0;
}

sub stocks {
	my ($self, $event, $to, $ticker) = @_;
	$ua = new LWP::UserAgent;
	$ua->agent("jenni/" . $::version . " " . $ua->agent);
	if ($ticker =~ / /) {
		@symbols = split(/ /, $ticker);
		$message = $event->nick . ": ";
		foreach $symbol (@symbols) {
			$req = new HTTP::Request GET => "http://quote.yahoo.com/d/quotes.csv?s=$symbol&f=sl1c1&e=.csv";
			$res = $ua->request($req);
			$content = $res->content;
			$content =~ s/(\"|\n|\r)//g;
			($symbol, $price, $change) = split(/,/, $content);
			if ($price eq "0.00") {
				$message .= "$symbol: N/A / ";
			} else {
				$message .= "$symbol: $price ($change) / "
			}
		}
		$message =~ s| / $||;
	} else {
		$req = new HTTP::Request GET => "http://quote.yahoo.com/d/quotes.csv?s=$ticker&f=sl1d1t1c1v&e=.csv";
		$res = $ua->request($req);
		$content = $res->content;
		$content =~ s/(\"|\n|\r)//g;
		($symbol, $price, $lastdate, $lasttime, $change, $volume) = split(/,/, $content);
		if ($price eq "0.00") {
			$message = $event->nick . ": Ticker Symbol $ticker doesn't exist";
		}
		else {
			$message = $event->nick . ": $symbol / Last Trade: $price ($lastdate $lasttime) / Change: $change / Volume: $volume / Details: http://finance.yahoo.com/q?s=$symbol&d=t";
		}
	}
	$self->privmsg($to, $message);
}

1;
