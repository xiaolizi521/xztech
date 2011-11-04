# $Id: Countries.pm,v 1.5 2002/03/03 21:15:13 jenni Exp $
package Countries;

sub init {
	&::addhandler("country", \&country);
	return 0;
}

sub destruct {
	&::delhandler("country");
	return 0;
}

sub country {
	my ($self, $event, $to, $request) = @_;
	my ($db) = $::db;
	$request = LibUtil::stripforsql($request);
	$sth = $db->query("SELECT code, country FROM countries WHERE code = '$request' OR country = '$request'") || next;
	if (($code, $country) = $sth->fetchrow) {
		if ($code =~ /$request/i) {
			$self->privmsg($to, $event->nick . ": $code is the country code for $country");
		} else {
			$self->privmsg($to, $event->nick . ": The country code for $country is $code");
		}
	} else {
		$self->privmsg($to, $event->nick . ": Sorry, I know of no such country");
	}
}

1;
