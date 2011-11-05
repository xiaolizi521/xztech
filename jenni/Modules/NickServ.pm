# $Id: NickServ.pm,v 1.4 2002/03/03 03:00:01 jenni Exp $
package NickServ;

sub connect {
	my ($self) = pop;
	$self->privmsg("NickServ", "IDENTIFY jennibot");
}

sub notice {
	shift @_;
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	my ($mynick) = $self->nick;
	my ($to) = $event->to;

	if ($event->nick eq "NickServ" && $arg =~ /^This nickname is registered/i) {
		$self->privmsg("NickServ", "IDENTIFY yourpassword");
		return 1;
	}
}

1;
