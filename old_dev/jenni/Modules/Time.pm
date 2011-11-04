# $Id: Time.pm,v 1.11 2003/05/14 17:24:44 jenni Exp $
package Time;

use POSIX qw(strftime);

$timeformat = "%l:%M:%S %p on %a, %b %e, %Y";

sub init {
	&::addhandler("time", \&time, 1);
	return 0;
}

sub destruct {
	&::delhandler("time");
	return 0;
}

sub time {
	my ($self, $event, $to, $zone, $plusminus) = @_;
	my ($db) = $::db;
	my ($offset);
	if ($plusminus eq '+') {
		if ($zone =~ /(.*?) (\+|-)([0-9\.]+) ?([Nn]|[Ss]|-)?/) {
			$plainzone = $1;
			$sqlzone = $1;
			$offset = $2 . $3;
			$hemisphere = uc($4);
			if ($hemisphere ne 'N' &&
				$hemisphere ne 'S' &&
				$hemisphere ne '-') {
				$hemisphere = 'N';
			}
			$sqlzone = LibUtil::stripforsql($sqlzone);
			$sth = $db->query("INSERT INTO timezones (name, offset, dst) VALUES ('$sqlzone', '$offset', '$hemisphere')") || next;
			if ($sth->affectedrows == 0) {
				$self->privmsg($to, $event->nick . ": Error adding timezone '$plainzone'.");
			} else {
				$self->privmsg($to, $event->nick . ": Timezone '$plainzone' added.");
			}
		}
	}
	elsif ($plusminus eq '-') {
		$sqlzone = LibUtil::stripforsql($zone);
		$sth = $db->query("DELETE FROM timezones WHERE name = '$sqlzone'") || next;
		if ($sth->affectedrows == 0) {
			$self->privmsg($to, $event->nick . ": Timezone '$zone' not found.");
		} else {
			$self->privmsg($to, $event->nick . ": Timezone '$zone' deleted.");
		}
	}
	else {
		if (($sign, $number) = ($zone =~ /^GMT (\+|\-)([0-9:.]+)$/i)) {
			if ($number =~ /([0-9]+):([0-9]{2})/) {
				$offsetn = $1;
				$offsetn += ($2 / 60);
				$offset = $number;
			} else {
				$offsetn = $sign . $number;
				$offset = $offsetn;
			}
		} else {
			if ($zone eq '') {
				$zone = $event->nick;
				$fallback = 1;
			} else {
				$fallback = 0;
			}
			$zone = LibUtil::stripforsql($zone);
			$sth = $db->query("SELECT offset, dst FROM timezones WHERE name = '$zone' LIMIT 1") || next;
			if ($sth) {
				if (($offsetn, $dst) = $sth->fetchrow) {
					$isdst = (localtime(time))[8];
					if ($isdst == 1) {
						if ($dst eq 'N') {
							$offsetn++;
							$offsetr = $offsetn - 1;
						} else {
							$offsetr = $offsetn;
						}
					} else {
						if ($dst eq 'S') {
							$offsetn++;
							$offsetr = $offsetn - 1;
						} else {
							$offsetr = $offsetn;
						}
					}
				} else {
					if ($fallback == 1) {
						$offsetn = $offsetr = 0;
						$offset = "GMT";
					} else {
						$self->privmsg($to, $event->nick . ": I don't know what time it is there.");
						return;
					}
				}
			}
		}
		if ($offsetr < 0) {
			$offset = "GMT $offsetr";
		} elsif ($offsetr > 0) {
			$offset = "GMT +$offsetr";
		} else {
			$offset = "GMT";
		}
		if ((localtime(time))[8] == 1) {
			if ($dst eq 'N') { $offset .= ", DST"; }
		} else {
			if ($dst eq 'S') { $offset .= ", DST"; }
		}
		$offset =~ s/\.0//;
		$timenew = strftime($timeformat, gmtime(time() + ($offsetn * 3600)));
		$timenew =~ s/ +/ /g;
		$timenew =~ s/^ //;
		$self->privmsg($to, $event->nick . ": It is currently $timenew ($offset)");
	}
}

1;
