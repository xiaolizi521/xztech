# $Id: Log.pm,v 1.4 2003/05/20 15:28:12 jenni Exp $
package Log;

use POSIX qw(strftime);

sub init {
	&::addhandler("wc",        \&wordcount);
	&::addhandler("wordcount", \&wordcount);
	return 0;
}

sub destruct {
	&::delhandler("wc");
	&::delhandler("wordcount");
	return 0;
}

sub wordcount {
	my ($self, $event, $to, $word) = @_;
	$word = LibUtil::stripforshell($word);
	$count = `cat Logs/* | grep -c "$word"`;
	$count = int($count);
	$self->privmsg($to, $event->nick . ": That word was found in my logs $count times");
}

sub getfh {
	my ($channel) = @_;
	if (fileno($channel) < 1) {
		open($channel, ">>Logs/$channel.log");
		my ($oldh) = select($channel);
		$| = 1;
		select($oldh);
		print $channel "\nSession Start: " . strftime("%a %b %d %T %Y", gmtime) . "\n";
	}
	return $channel;
}

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($args) = $event->args;
	my ($channel) = $event->to;
	$channel = &getfh($channel);
	print $channel "[" . strftime("%T", gmtime) . "] <" . $event->nick . "> $args\n";
	return 0;
}

sub action {
	shift @_;
	my ($self, $event) = @_;
	my (@args) = $event->args;
	shift @args;
	$channel = &getfh($event->to);
	print $channel "[" . strftime("%T", gmtime) . "] * " . $event->nick . " @args\n";
	return 0;
}

sub notice {
	shift @_;
	my ($self, $event) = @_;
	my ($args) = $event->args;
	$channel = &getfh($event->to);
	print $channel "[" . strftime("%T", gmtime) . "] -" . $event->nick . "- $args\n";
	return 0;
}

1;
