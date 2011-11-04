# $Id: Reminder.pm,v 1.3 2002/03/02 20:39:00 jenni Exp $
package Reminder;

sub init {
	return 0;
}

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	my ($nick) = $event->nick;
	my ($mynick) = $self->nick;
	my ($to) = $event->to;

	if ($arg =~ /^(.{0,3})$mynick(.{0,3}) (.*)/i) {
		if ($3 =~ /remind me in (.*?)(|s|m|h|d): (.*)/i) {
			$unit = $2 eq "" ? "s" : "$2";
			if ($unit eq 'm') {
				$multiplier = 60;
			} elsif ($unit eq 'h') {
				$multiplier = 3600;
			} elsif ($unit eq 'd') {
				$multiplier = 86400;
			} else {
				$multiplier = 1;
			}
			$interval = $1 * $multiplier;
			# FUCK YOU TONY
			if ($interval > 604800 && !LibUtil::is_admin($event->userhost)) {
				$self->privmsg($to, "$nick: No reminders over 1 week.");
				return 0;
			}
			$self->schedule($interval, \&Net::IRC::Connection::privmsg, $nick, $3);
			$self->notice($event->nick, "Reminder added (will remind in $1$unit)");
			return 1;
		}
		return 0;
	}
	else {
		return 0;
	}
}
return 1;
