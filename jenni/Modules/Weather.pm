# $Id: Weather.pm,v 1.6 2003/05/15 16:27:40 jenni Exp $
package Weather;

use Geo::Weather;

sub init {
	&::addhandler("weather", \&weather);
	return 0;
}

sub destruct {
	&::delhandler("weather");
	return 0;
}

sub weather {
	my ($self, $event, $to, $request) = @_;
	my ($ret, $current, $tmpcit, $tmpst);
	my $weather = new Geo::Weather;
	$weather->{timeout} = 5;
	if ($request =~ /^(.\d*)$/) { 
		$current = $weather->get_weather($1);
	} elsif ($request =~ /^(.*)\,(.*)$/) { 
		$current = $weather->get_weather($1, $2);
		$tmpcit = $1;
		$tmpst = $2;
	} else {
		$ret = "Usage: weather ZIP|City, State|City, Country";
	}

	if (ref $current) { 
		$ret = "Weather: [".(length($current->{city}) > 0 ? $current->{city} : $tmpcit);
		$ret .= ", ".(length($current->{state}) > 0 ? $current->{state} : $tmpst). "] ";
		$ret .= " is currently unavailable ($current->{url}). (Weather.com sucks, try again later)" if ($current->{temp} eq 'N/A');
		$ret .= "Temp: $current->{temp}F/". f2c($current->{temp}) . "C" if ($current->{temp} ne 'N/A');
		$ret .= " (feels like $current->{heat}F/".f2c($current->{heat})."C)" if ($current->{heat} ne '' && $current->{heat} ne 'N/A' && $current->{heat} ne $current->{temp});
		$ret .= ", Humidity: $current->{humi}" if ($current->{humi} ne '' && $current->{humi} ne 'N/A');
		$ret .= ", Wind: $current->{wind}" if ($current->{wind} ne '' && $current->{wind} ne 'N/A');
		$ret .= ", Cond: $current->{cond}" if ($current->{cond} ne '' && $current->{cond} ne 'N/A');
	}

	if ($current->{temp} eq '') {
		$ret = "Couldn\'t get weather for place.";
	}

	if (!$ret) {
		if ($ERROR_NOT_FOUND > 0) {
			$ret = "Weather for \"$request\" could not be found ($ERROR_NOT_FOUND).";
		} elsif ($ERROR_CONNECT || $ERROR_TIMEOUT) {
			$ret = "Weather retrieval timeout. Try again later.";
		} elsif ($ERROR_QUERY || $ERROR_PAGE_INVALID) {
			$ret = "Weather retrieval error. Am I broken?";
		}
	}

	MSG:
	$ret =~ s/&nbsp;/ /;
	$self->privmsg($to, $event->nick . ": $ret");
	undef($ret);
	undef($current);
}

sub f2c {
	my ($data,$bleh) = @_;
	if ($data =~ /^(\d*)$/) {
		$bleh = sprintf("%01.1f",($data - 32) * (5 / 9));
		return($bleh);
	}
	else {
		return('N/A');
	}
}

1;
