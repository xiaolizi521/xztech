# $Id: Calculator.pm,v 1.5 2002/03/03 21:15:13 jenni Exp $
package Calculator;

use Libraries::Util;

sub init {
	return 0;
}

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	my ($db) = $main::db;
	my ($to) = $event->to;
	if ($arg =~ /^==\s*(.*)/) {
		$query = LibUtil::stripforsql($1, 1);
		unless ($sth = $db->query("SELECT $query, $query IS NULL")) {
			$self->privmsg($to, $event->nick . ": That's way too advanced for me");
		}
		elsif (($answer, $null) = $sth->fetchrow) {
			$answer =~ s/(\n|\r).*//g;
			if ($null == 1) {
				$self->privmsg($to, $event->nick . ": undefined");
			} else {
				$self->privmsg($to, $event->nick . ": $answer");
			}
		}
		else {
			$self->privmsg($to, $event->nick . ": undefined");
		}
		return 1;
	}
}

return 1;

