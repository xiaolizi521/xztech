# $Id: Temperature.pm,v 1.4 2002/03/02 20:40:14 jenni Exp $
package Temperature;

%units = (
	'c' => 'celsius',
	'f' => 'fahrenheit',
	'k' => 'kelvin'
);

sub init {
	&::addhandler("temp", \&temp);
	&::addhandler("temperature", \&temp);
	return 0;
}

sub destruct {
	&::delhandler("temp");
	&::delhandler("temperature");
	return 0;
}

sub temp {
	my ($self, $event, $to, $text) = @_;
	if ($text =~ /^([0-9.\-]+) ([cCkKfF])(.*) (to|into|->|=>) ([cCkKfF])(.*)$/i) {
		$num   = $1;
		$unit1 = lc($2);
		$unit2 = lc($5);
		if (($num < -273.15 && $unit1 eq 'c') || ($num < -459.67 && $unit1 eq 'f') || ($num < 0 && $unit1 eq 'k')) {
			$self->privmsg($to, $event->nick . ": You can't go colder than absolute zero...");
			return;
		}
		if ($unit1 eq 'c' && $unit2 eq 'k') {
			$output = $num + 273.15;
			$unit1 = "degrees " . $units{$unit1};
			$unit2 = $units{$unit2};
		}
		elsif ($unit1 eq 'c' && $unit2 eq 'f') {
			$output = ($num * (9 / 5)) + 32;
			$unit1 = "degrees " . $units{$unit1};
			$unit2 = "degrees " . $units{$unit2};
		}
		elsif ($unit1 eq 'k' && $unit2 eq 'c') {
			$output = $num - 273.15;
			$unit1 = $units{$unit1};
			$unit2 = "degrees " . $units{$unit2};
		}
		elsif ($unit1 eq 'k' && $unit2 eq 'f') {
			$output = (($num - 273.15) * (9 / 5)) + 32;
			$unit1 = $units{$unit1};
			$unit2 = "degrees " . $units{$unit2};
		}
		elsif ($unit1 eq 'f' && $unit2 eq 'c') {
			$output = ($num - 32) * (5 / 9);
			$unit1 = "degrees " . $units{$unit1};
			$unit2 = "degrees " . $units{$unit2};
		}
		elsif ($unit1 eq 'f' && $unit2 eq 'k') {
			$output = (($num - 32) * (5 / 9)) + 273.15;
			$unit1 = "degrees " . $units{$unit1};
			$unit2 = $units{$unit2};
		}
		else {
			return;
		}
		$num    = sprintf("%01.1f", $num);
		$output = sprintf("%01.1f", $output);
		$self->privmsg($to, $event->nick . ": $num $unit1 is $output $unit2");
	}
}

1;
