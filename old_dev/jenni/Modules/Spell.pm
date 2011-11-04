# $Id: Spell.pm,v 1.7 2003/02/15 02:30:34 jenni Exp $
package Spell;

sub init {
	&::addhandler("spell", \&spell, 0, 1, 0);
	return 0;
}

sub destruct {
	&::delhandler("spell");
	return 0;
}

sub spell {
	my ($self, $event, $to, $word) = @_;
	$word = LibUtil::stripforshell($word);
	@response = split(/\n/, `echo $word | ispell -a`);
	$response = $response[1];
	if (($alt) = $response =~ /&.*\: (.*)/) {
		$self->privmsg($to, $event->nick . ": Possible alternatives to $word: $alt");
	}
	elsif ($response =~ /^(\*|\+)/) {
		$self->privmsg($to, $event->nick . ": $word seems to be correct");
	}
	else {
		$self->privmsg($to, $event->nick . ": I don't know that word yet.");
	}
}

1;
