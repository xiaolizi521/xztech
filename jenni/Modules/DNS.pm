# $Id: DNS.pm,v 1.4 2002/03/02 20:25:06 jenni Exp $
package DNS;

sub init {
	&::addhandler("dns", \&dns);
	return 0;
}

sub destruct {
	&::delhandler("dns");
	return 0;
}

sub dns {
	my ($self, $event, $to, $host) = @_;
	if ($host =~ /^([0-9]{1,3}\.){3}[0-9]{1,3}$/) {
		$result = `dnsname $host`;
		$result =~ s/\s+$//g;
		if ($result ne '') {
			$self->privmsg($to, $event->nick . ": $host resolved to $result");
		}
		else {
			$self->privmsg($to, $event->nick . ": Error resolving $host");
		}    
	}
	else {
		$result = `dnsip $host`;
		$result =~ s/\s+$//g;
		if ($result ne '') {
			$self->privmsg($to, $event->nick . ": $host resolved to $result");
		}
		else {
			$self->privmsg($to, $event->nick . ": Error resolving $host");
		}
	}
}

1;
