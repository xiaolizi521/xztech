# $Id: Chat.pm,v 1.3 2002/03/02 20:26:13 jenni Exp $
package Chat;

use Chatbot::Eliza;

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	my ($mynick) = $self->nick;
	my ($to) = $event->to;

	my $chatbot = new Chatbot::Eliza;
	srand(time ^ ($$ + ($$ << 15)) );
	if ($arg =~ /^(.{0,3})$mynick(.{0,3}) (.*)/i) {
		if($1 eq "") { return 0; }
		$prompt = $chatbot->transform($1);
		$self->privmsg($to, $event->nick . ": $prompt");
		return 0;
	}
}

1;
