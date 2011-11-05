# $Id: Help.pm,v 1.1 2002/03/03 18:20:25 jenni Exp $
package Help;

sub init {
	&::addhandler("help", \&help);
	return 0;
}

sub destruct {
	&::delhandler("help");
	return 0;
}

sub help {
	my ($self, $event, $to, $whocares) = @_;
	return if $whocares;
	$message = "Valid commands: ";
	while (($text, $details) = each %::handlers) {
		$message .= "$text, ";
	}
	$message =~ s/, $//;
	$self->privmsg($event->nick, $message);
}

1;
