# $Id: Currency.pm,v 1.9 2002/03/03 21:15:13 jenni Exp $
package Currency;

sub init {
	&::addhandler("currency", \&currency);
	&::addhandler("currencysearch", \&currencysearch);
	return 0;
}

sub destruct {
	&::delhandler("currency");
	&::delhandler("currencysearch");
	return 0;
}

sub currency {
	my ($self, $event, $to, $arg) = @_;
	my ($db) = $main::db;
	if (($amount, $unitfrom, undef, undef, $unitto) = $arg =~ /^\$?([0-9.]+) ([A-Za-z]{3}) ((-|=)>|to) ([A-Za-z]{3})$/i) { 
		$ua = new LWP::UserAgent;
		$ua->agent("jenni/" . $::version . " " . $ua->agent);
		$url = "http://finance.yahoo.com/d/quotes.csv?s=$unitfrom$unitto=X&f=l1&e=.csv";
		$req = new HTTP::Request GET => $url;
		$res = $ua->request($req);
		if ($res->is_success) {
			$rate = $res->content;
			$rate = sprintf("%1.04f", $rate);
			if ($rate == 0) {
				$self->privmsg($to, $event->nick . ": There was an error retrieving the exchange rate for those currencies.");
				return;
			}
			$before = sprintf("%01.2f", $amount);
			$after = sprintf("%01.2f", ($before * $rate));
			$sth = $db->query("SELECT symbol, currency, plural FROM currency WHERE symbol = '$unitfrom' OR symbol = '$unitto'") || next;
			while (($symbol, $currency, $plural) = $sth->fetchrow) {
				if ($symbol =~ /^$unitto$/i) {
					$after .= " $currency";
					$after .= "s" if $plural eq "Y";
				} elsif ($symbol =~ /^$unitfrom$/i) {
					$before .= " $currency";
					$before .= "s" if $plural eq "Y";
				}
			}
			$self->privmsg($to, $event->nick . ": $before is $after (using rate $rate)");
		} else {
			$self->privmsg($to, $event->nick . ": There was an error retrieving the currency data.");
		}      
	}
}

sub	currencysearch {
	my ($self, $event, $to, $country) = @_;
	my ($db) = $main::db;
	my ($line) = "";
	$sth = $db->query("SELECT symbol, currency FROM currency WHERE currency LIKE '%" . LibUtil::stripforsql($country) . "%' LIMIT 10") || next;
	if ($sth->affectedrows == 0) { 
		$self->privmsg($to, $event->nick . ": No currency found, try using the country name.");
	} else {
		while (($symbol, $name) = $sth->fetchrow) {
			$line .= "$symbol $name, ";
		}
		$line =~ s/, $//;
		$self->privmsg($to, $event->nick . ": Possible currency: $line");
	}
}

1;
