# $Id: Conversation.pm,v 1.3 2002/03/02 20:28:44 jenni Exp $
package Conversation;

sub init {
	return 0;
}

@moods = (
	"crappy",
	"shitty",
	"good",
	"okay",
	"great",
	"fine",
	"alright",
	"bored",
);

@moods2	= (
	", why?", # crappy
	", why the hell do YOU care?", # shitty
	"...", # good
	".", #okay
	"!", #great
	".", #fine
	".", #alright
	"...", # bored
);

@greetings = (
	"hi ",
	"howdy, ",
	"hello, ",
	"what's up, ",
	"greetings, ",
	"hey there, ",
);

@greetings2	= (
	"!", # hi
	"!", #howdy
	".", #hello
	"?", #what's up
	"!", #greetings
	"...", #hey there
);

@doyou = (
	"Why do you care if I ",
	"What business is it of yours if I ",
);

@doyou2	= (
	"?", #why do you care if i
	"?", #what business is it of yours if i"
);

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	my ($mynick) = $self->nick;
	my ($to) = $event->to;
	
	$nick = $event->nick;

	if ($arg =~ /hi $mynick/) {
		$num = rand @greetings;
		$response = $greetings[$num] . $nick . $greetings2[$num];
		$self->privmsg($to, $response);
		return 0;
	} elsif ($arg =~ /(.{0,3})$mynick(.{0,3}) (.*)/) {
		$line = $3;
		if ($line =~ /do you (.*)\?+/) {
			$num = rand @doyou;
			$response = $nick . ": " . $doyou[$num] . $1 . $doyou2[$num];
			$self->privmsg($to, $response);
			return 0;
		} elsif ($line =~ /how are you\?+/) {
			$num = rand @moods;
			$response = $moods[$num] . $moods2[$num];
			$self->privmsg($to, "$nick: $response");
			return 0;
		}
	}
	return 0;
}

1;
