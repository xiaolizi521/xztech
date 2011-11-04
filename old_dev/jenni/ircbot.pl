#!/usr/bin/perl
# $Id: ircbot.pl,v 1.24 2003/05/15 16:31:17 jenni Exp $

#use lib "/home/jenni/perllibs/lib";

use POSIX qw(strftime);
$mtime = (stat("ircbot.pl"))[9];
$version = "1.0 CVS";
$versiondate = strftime("%a %b %d %H:%M:%S %Y", gmtime($mtime));
$defaultconfig = "Configs/default.conf";

use Getopt::Long;
local %options = ('fork' => 1, 'config' => $defaultconfig);
$getoptval = GetOptions(\%options, 'fork!', 'debug', 'help', 'version', 'config=s');

if ($options{"help"} == 1 || $getoptval != 1) {
	print "Usage: ircbot.pl [options]\n\n";
	print "Options:\n";
	print "  -f,  --fork          Fork to the background after launching (default).\n";
	print "       --nofork        Do not fork to the background.\n";
	print "  -d,  --debug         Enter debug mode, which prints everything received.\n";
	print "  -h,  --help          Displays this help.\n";
	print "  -v,  --version       Displays the version and build date of this bot.\n";
	print "  -c,  --config=file   Uses an alternate config file. The default config file\n";
	print "                       is specified near the top of ircbot.pl\n\n";
	exit(0);
}
elsif ($options{"version"} == 1) {
	print "jenni IRC bot version $version ($versiondate)\n";
	exit(0);
}
elsif (!-e $options{"config"}) {
	print "Error attempting to read config file: $argument\n";
	exit(2);
}

if ($options{"fork"} == 1) {
	print "Forking to the background...\n";
	if (!defined($pid = fork)) {
		print "Unable to fork!\n";
	}
	elsif ($pid) {
		open(PIDFILE, ">pid");
		print PIDFILE $pid;
		close(PIDFILE);
		exit;
	}
}

# open(STDIN,  "<&STDIN");
# open(STDOUT, ">&STDOUT");
# open(STDERR, ">&STDERR");

if ($options{"fork"} == 1) {
	open(STDOUT, ">/dev/null");
	select(STDOUT);
}

$0 = "jenni $version - irc bot";

if ($options{"debug"} == 1) {
	print "=" x 30 . "\n";
	print "Entering debug mode...\n";
	print "=" x 30 . "\n";
	$0 .= " (debug mode)";
}

require $options{"config"};
use	Libraries::Util;
use	Libraries::Database;
use	Net::IRC;
use	UNIVERSAL qw(can);

local $db = LibDB->init(
	$db{host},
	$db{database},
	$db{username},
	$db{password}
);

%handlers = ();
$symbols = "+\\\-!?:=^#\@\$\%";
# Syntax: addhandler($text, $sub, $symbol, $mode, $prefix);
#   $text        Text you want to handle
#   $sub         Reference to a sub to add as handler
#   $symbol      0 = no symbolic prefix allowed (default is 0)
#   $mode        0 = pubmsg only
#                1 = pubmsg or privmsg
#                2 = privmsg only
#                (default is 1)
#   $prefix      0 = don't require nick: prefix
#                1 = require jenni: prefix in pubmsg, don't in privmsg
#                2 = require jenni: prefix in all messages
#                (default is 1)
sub addhandler {
	my ($text, $sub, $symbol, $mode, $prefix) = @_;
	my ($package) = caller;
	return -1 if ref($sub) ne 'CODE';
	$symbol = 0 if $symbol eq "";
	$mode   = 1 if $mode   eq "";
	$prefix = 1 if $prefix eq "";
	my %handler = (
		"package" => $package,
		"sub"     => $sub,
		"symbol"  => $symbol,
		"mode"    => $mode,
		"prefix"  => $prefix
	);
	$handlers{"$text"} = \%handler;
}

sub delhandler {
	my ($text) = @_;
	undef($handlers{"$text"});
}

if (@modules) {
	print "=" x 30 . "\n";
	print "Loading Modules...\n";
	print "=" x 30 . "\n";
	foreach $module (@modules) {
		if ($module ne $modules[0]) {
			print "\n";
		}
		print "-> $module";
		eval("use Modules::$module;");
		die $@ if $@;
		#if (can($module, "init")) {
		#	$module->init();
		#}
	}
	print "\n" . "=" x 30 . "\n";
}

foreach $module (@modules) {
	if (can($module, "init")) {
		$module->init($conn);
		print "$module->init called\n" if $options{"debug"} == 1;
	}
}

print "Connecting to $config{server}:$config{port}\n";
$irc = new Net::IRC;
$conn = $irc->newconn(
	Server    => "$config{server}",
	Port      =>  $config{port},
	Nick      => "$config{name}",
	Ircname   => "$config{ircname}",
	Username  => "$config{ident}",
	LocalAddr => "$config{vhost}"
) or die "$config{nick}: Can't connect to IRC server.\n";
$conn->maxlinelen(450);

sub on_connect {
	my $self = shift;
	foreach $module (@modules) {
		if (can($module, "connect")) {
			$module->connect($self);
			print "$module->connect called\n" if $options{"debug"} == 1;
		}
	}
}

sub on_part {
	my ($self, $event) = @_;
	my ($channel) = ($event->to)[0];
}

sub on_join {
	my ($self, $event) = @_;
	my ($channel) = ($event->to)[0];
}

sub on_public {
	my ($self, $event) = @_;
	my ($mynick) = $self->nick;
	my ($arg) = $event->args;
	print "<" . $event->nick . ":" . ($event->to)[0] . "> $arg\n" if $options{"debug"} == 1;
	foreach $module (@modules) {
		if (can($module, "public")) {
			if ($module->public($self, $event) > 0) {
				if ($options{"debug"} == 1) {
					print "Module $module handled message by <" . $event->nick . ":" . ($event->to)[0] . "> with public()\n";
				}
				return;
			}
		}
	}
	($pre, $symbol, $word, $rest) = $arg =~ /(.{0,3}$mynick[^\s]{0,3})?\s?([$symbols])?([^\s]+)\s*(.*)/;
	if ($::handlers{"$word"}) {
		$okay = 1;
		$okay = 0 if $::handlers{"$word"}{"prefix"} > 0 && !$pre;
		$okay = 0 if $::handlers{"$word"}{"mode"} == 2;
		$okay = 0 if $::handlers{"$word"}{"symbol"} == 0 && $symbol;
		if ($okay == 1) {
			print "Module " . $::handlers{"$word"}{"package"} . " handled message by <" . $event->nick . ":" . ($event->to)[0] . "> with handler\n" if $options{"debug"} == 1;
			$handler = $::handlers{"$word"}{"sub"};
			&$handler($self, $event, ($event->to)[0], $rest, $symbol);
		}
	}
}

sub on_msg {
	my ($self, $event) = @_;
	my ($mynick) = $self->nick;
	my ($nick) = $event->nick;
	my ($arg) = $event->args;
	my @to = $event->to;
	if ($options{"debug"} == 1) {
		print "<" . $event->nick . "> $arg\n";
	}
	foreach $module (@modules) {
		if (can($module, "private")) {
			if ($module->private($self, $event) > 0) {
				if ($options{"debug"} == 1) {
					print "Module $module handled privmsg by <" . $event->nick . "> with private()\n";
				}
				return;
			}
		}
		elsif (can($module, "public")) {
			@to = $event->to($nick);
			if ($module->public($self, $event) > 0) {
				if ($options{"debug"} == 1) {
					print "Module $module handled privmsg by <" . $event->nick . "> with public()\n";
				}
				return;
			}
			$event->to(@to);
		}
	}
	($pre, $symbol, $word, $rest) = $arg =~ /([^\s]{0,3}$mynick[^\s]{0,3})?\s?([$symbols])?([^\s]+)\s*(.*)/;
	if ($::handlers{"$word"}) {
		$okay = 1;
		$okay = 0 if $::handlers{"$word"}{"prefix"} == 2 && !$pre;
		$okay = 0 if $::handlers{"$word"}{"mode"} == 0;
		$okay = 0 if $::handlers{"$word"}{"symbol"} == 0 && $symbol;
		if ($okay == 1) {
			print "Module " . $::handlers{"$word"}{"package"} . " handled privmsg by <" . $event->nick . "> with handler\n" if $options{"debug"} == 1;
			$handler = $::handlers{"$word"}{"sub"};
			&$handler($self, $event, ($event->to)[0], $rest, $symbol);
		}
	}
}

sub on_notice {
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	if ($options{"debug"} == 1) {
		print "-" . $event->nick . "- $arg\n";
	}
	foreach $module (@modules) {
		if (can($module, "notice")) {
			if ($module->notice($self, $event) > 0) {
				if ($options{"debug"} == 1) {
					print "Module $module handled notice by <" . $event->nick . "> with notice()\n";
				}
				last;
			}
		}
	}
}

sub on_topic {

}

sub on_names {
	my ($self, $event) = @_;
	my (@list, $channel) = ($event->args);
	($channel, @list) = splice @list, 2;
}

sub on_dcc {
	my ($self, $event) = @_;
	my $type = ($event->args)[1];
}

sub on_ping {
	my ($self, $event) = @_;
	my $nick = $event->nick;
	$self->ctcp_reply($nick, join (' ', ($event->args)));
}

sub on_ping_reply {
	my ($self, $event) = @_;
	my ($args) = ($event->args)[1];
	my ($nick) = $event->nick;
	$args = time - $args;
}

sub on_nick_taken {
	my ($self) = shift;
	$self->nick($self->nick . "-");
}

sub on_kick {
	my ($self, $event) = @_;
	if (($event->to)[0] eq "jenni") {
		$self->join(($event->args)[0]);
	}
}

sub on_action {
	my ($self, $event) = @_;  
	my ($nick, @args) = ($event->nick, $event->args);
	shift @args;
	if ($options{"debug"} == 1) {
		print "* " . $event->nick . ":" . ($event->to)[0] . " @args\n";
	}
	foreach $module (@modules) {
		if (can($module, "action")) {
			if ($module->action($self, $event) > 0) {
				if ($options{"debug"} == 1) {
					print "Module $module handled action by <" . $event->nick . "> with action()\n";
				}
				last;
			}
		}
	}
}

sub on_disconnect {
	my ($self, $event) = @_;
	$self->connect();
}

$conn->add_handler('cping',   \&on_ping);
$conn->add_handler('crping',  \&on_ping_reply);
$conn->add_handler('msg',     \&on_msg);
$conn->add_handler('notice',  \&on_notice);
$conn->add_handler('public',  \&on_public);
$conn->add_handler('caction', \&on_action);
$conn->add_handler('join',    \&on_join);
$conn->add_handler('part',    \&on_part);
$conn->add_handler('cdcc',    \&on_dcc);
$conn->add_handler('topic',   \&on_topic);
$conn->add_handler('notopic', \&on_topic);
$conn->add_handler('kick',    \&on_kick);

# nick
# quit
# mode
# ping
# invite
# kill
# leaving
# umode
# error

# $conn->add_global_handler([ 251,252,253,254,302,255 ], \&on_init);
$conn->add_global_handler('disconnect',	\&on_disconnect);
$conn->add_global_handler([ 376,422 ], \&on_connect); 
# $conn->add_global_handler(433, \&on_nick_taken);
$conn->add_global_handler(353, \&on_names);

$irc->start;
