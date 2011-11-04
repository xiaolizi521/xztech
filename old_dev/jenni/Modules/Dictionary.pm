# $Id: Dictionary.pm,v 1.7 2003/02/15 02:25:41 jenni Exp $
package Dictionary;

use Libraries::Util;

sub init {
	&::addhandler("define", \&define, 0, 1, 0);
	return 0;
}

sub destruct {
	&::delhandler("define");
	return 0;
}

sub define {
	my ($self, $event, $to, $word) = @_;
	my ($db) = $main::db;
	$word = LibUtil::stripall($word);
	$sth = $db->query("SELECT type, definition FROM dictionary WHERE word = '$word'") || next;
	undef($message);
	while (($type, $definition) = $sth->fetchrow) {
		if (length($message . $type . $definition) + 6 <= 425) {
			$message .= "($type) $definition ";
		}
	}
	if (defined($message)) {
		chomp($message);
		$self->privmsg($to, $event->nick . ": [$word] $message");
	}
	else {
		$self->privmsg($to, $event->nick . ": I know of no such word [$word]");
	}
}

1;
