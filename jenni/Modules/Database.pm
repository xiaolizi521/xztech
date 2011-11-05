# $Id: Database.pm,v 1.12 2003/05/15 16:31:02 jenni Exp $
package Database;

use POSIX qw(strftime);

# Get, Add, Delete, and Append triggers
$gettrigger1 = "??";
$gettrigger2 = ">>";
$addtrigger1 = "!!";
$addtrigger2 = "<<";
$deltrigger1 = "--";
$deltrigger2 = "~~";
$apptrigger1 = "++";
$apptrigger2 = "&&";
$inltrigger1 = "??";
$inltrigger2 = "<<";

$inlineexpansionmax = 5;
$definitionlenmax   = 450;

sub init {
	return 0;
}

sub public {
	shift @_;
	my ($self, $event) = @_;
	my ($arg) = $event->args;
	my ($db) = $main::db;
	my ($nick) = $event->nick;
	my ($mynick) = $self->nick;
	my ($to) = $event->to;
	if ($arg =~ /^(.{0,3})$mynick(.{0,3}) random (definition|word)/i) {
		$query = "SELECT word, definition FROM definitions ORDER BY RAND() LIMIT 1";
goto showtheword;
		return 1;
	}
	elsif ($arg =~ /^(\Q$gettrigger1\E|\Q$gettrigger2\E)\s*(.*)/) {
		$word = LibUtil::stripcontrol(LibUtil::stripforsql($2));
		$query = "SELECT word, definition FROM definitions WHERE word = '$word'";
showtheword:
		$sth = $db->query($query) || next;
		if ($sth) {
			if (($word, $definition) = $sth->fetchrow) {
				$db->query("UPDATE definitions SET numhits = numhits + 1 WHERE word = '" . LibUtil::stripforsql($word) . "'") || next;
				$expansions = 0;
				while ($definition =~ /(\Q$gettrigger1\E|\Q$gettrigger2\E)\s*(.*?)(\Q$inltrigger1\E|\Q$inltrigger2\E)/ && $expansions < $inlineexpansionmax) {
					$expansions++;
					$sth = $db->query("SELECT definition FROM definitions WHERE word = '" . LibUtil::stripforsql($2) . "'") || next;
					$inlinedef = $sth->fetchrow;
					$definition =~ s/(\Q$gettrigger1\E|\Q$gettrigger2\E)\s*(.*?)(\Q$inltrigger1\E|\Q$inltrigger2\E)/$inlinedef/;
				}
				$self->privmsg($to, "<$word> $definition");
			}
			else {
				$self->privmsg($to, $event->nick . ": <$2> hasn't been added yet...");
			}
		}
		return 1;
	}
	elsif ($arg =~ /^\Q$addtrigger1\E\s*(.*?)\s*\Q$addtrigger1\E\s*(.*)/ ||
			$arg =~ /^\Q$addtrigger2\E\s*(.*?)\s*\Q$addtrigger2\E\s*(.*)/) {
		if (!$2) {
			$self->privmsg($to, $event->nick . ": word must have a definition!");
		}
		else {
			($ident, $hostname) = split(/\@/, $event->userhost);
			$word = LibUtil::stripcontrol(LibUtil::stripforsql($1));
			$definition = LibUtil::stripcontrol(LibUtil::stripforsql($2));
			$sth = $db->query("SELECT u.nick, u.ident, u.host FROM definitions AS d, users AS u WHERE d.userid = u.userid AND d.word = '$word'") || next;
			if (($addnick, $addident, $addhost) = $sth->fetchrow) {
				if ($addnick eq $event->nick && LibUtil::mask($addhost) eq LibUtil::mask($hostname)) {
					$sth = $db->query("UPDATE definitions SET word = '$word', definition = '$definition', modts = NOW() WHERE word = '$word'") || next;
					if ($sth) {
						$self->privmsg($to, "Thanks, <$1> overwritten.");
					}
				}
				else {
					$self->privmsg($to, "Sorry, but <$1> already exists in the database.");
				}
			}
			else {
				$sth = $db->query("INSERT INTO definitions (word, definition, modts, addts, userid) VALUES ('$word', '$definition', NOW(), NOW(), '" . LibUtil::userid($event) . "')") || next;
				if ($sth) {
					$self->privmsg($to, "Thanks, <$1> added.");
				}
			}
		}
		return 1;
	}
	elsif ($arg =~ /^\Q$apptrigger1\E\s*(.*?)\s*\Q$apptrigger1\E(.*)/ ||
			$arg =~ /^\Q$apptrigger2\E\s*(.*?)\s*\Q$apptrigger2\E(.*)/) {
		if (!$2) {
			$self->privmsg($to, $event->nick . ": word must have a definition!");
		}
		else {
			($ident, $hostname) = split(/\@/, $event->userhost);
			$word = LibUtil::stripcontrol(LibUtil::stripforsql($1));
			$definition = LibUtil::stripcontrol(LibUtil::stripforsql($2));
			$sth = $db->query("SELECT u.nick, u.ident, u.host, length(d.definition) FROM definitions AS d, users AS u WHERE d.userid = u.userid AND d.word = '$word'") || next;
			if (($addnick, $addident, $addhost, $deflength) = $sth->fetchrow) {
				if (($addnick eq $event->nick && LibUtil::mask($addhost) eq LibUtil::mask($hostname)) || LibUtil::is_admin($event->userhost)) {
					if ($deflength + length($definition) + length($word) + 3 > $definitionlenmax) {
						$self->privmsg($to, $event->nick . ": Sorry, but that would make <$1> too long.");
					}
					else {
						$sth = $db->query("UPDATE definitions SET definition = concat(definition, '$definition'), modts = NOW() WHERE word = '$word'") || next;
						if ($sth) {
							$self->privmsg($to, "Thanks, <$1> appended.");
						}
					}
				}
				else {
					$self->privmsg($to, "Sorry, but you do not have permission to append to <$1>.");
				}
			}
			else {
				$sth = $db->query("INSERT INTO definitions (word, definition, modts, addts, userid) VALUES ('$word', '$definition', NOW(), NOW(), '" . LibUtil::userid($event) . "')") || next;
				if ($sth) {
					$self->privmsg($to, "Thanks, <$1> added.");
				}
			}
		}
	}
	elsif ($arg =~ /^(\Q$deltrigger1\E|\Q$deltrigger2\E)\s*(.*)$/) {
		if ($word =~ /ping statistics/i) {
			return 1;
		}
		$word = LibUtil::stripcontrol(LibUtil::stripforsql($2));
		$sth = $db->query("SELECT u.nick, u.ident, u.host FROM definitions AS d, users AS u WHERE d.userid = u.userid AND d.word = '$word'") || next;
		if (($addnick, $addident, $addhost) = $sth->fetchrow) {
			($ident, $hostname) = split(/\@/, $event->userhost);
			if ((LibUtil::mask($addhost) eq LibUtil::mask($hostname)) || LibUtil::is_admin($event->userhost)) {
				$sth = $db->query("DELETE FROM definitions WHERE word = '$word'") || next;
				$self->privmsg($to, "<$2> deleted");
			}
			else {
				$self->privmsg($to, "Access denied: <$2> was added by $addnick ($addident\@$addhost)");
			}
		}
		else {
			$self->privmsg($to, "$nick: <$2> hasn't been added yet...");
		}
		return 1;
	}
	elsif ($arg =~ /^(.{0,3})$mynick(.{0,3}) (.*)/i) {
		if ($3 =~ /who added (.*)/i) {
			$origword = $1;
			$origword =~ s/\?//;
			$word = LibUtil::stripcontrol(LibUtil::stripforsql($origword));
			$sth = $db->query("SELECT u.nick, u.ident, u.host, d.word FROM definitions AS d, users AS u WHERE d.userid = u.userid AND d.word = '$word'") || next;
			if (($addnick, $addident, $addhost, $addword) = $sth->fetchrow) {
				$self->privmsg($to, "<$addword> was added by $addnick ($addident\@$addhost)");
			}
			else {
				$self->privmsg($to, "$nick: <$origword> hasn't been added yet...");
			}
			return 1;
		}
		elsif ($3 =~ /when was (.*) added/i) {
			$origword = $1;
			$origword =~ s/\?//;
			$word = LibUtil::stripcontrol(LibUtil::stripforsql($origword));
			$sth = $db->query("SELECT DATE_FORMAT(addts, '%M %e, %Y @ %r'), UNIX_TIMESTAMP(addts), word FROM definitions WHERE word = '$word'") || next;
			if (($addedon, $timestamp, $addword) = $sth->fetchrow) {
				$self->privmsg($to, "<$addword> was added on $addedon (" . LibUtil::getdiff($timestamp, time()) . " ago)");
			}
			else {
				$self->privmsg($to, "$nick: <$origword> hasn't been added yet...");
			}
			return 1;
		}
		elsif ($3 =~ /how many (words|definitions) (have|has) (I|.*) added/i) {
			$query = "SELECT count(*) FROM definitions AS d, users AS u WHERE d.userid = u.userid AND u.nick = ";
			if ($3 eq 'I' || $3 eq 'i') {
				$query .= "'$nick'";
			} else {
				$query .= "'$3'";
			}
			$sth = $db->query($query) || next;
			$mywords = $sth->fetchrow;
			$sth = $db->query("SELECT count(*) FROM definitions") || next;
			$totalwords = $sth->fetchrow;
			$percentage = sprintf("%01.1f", ($mywords / $totalwords) * 100);
			if ($3 eq 'I' || $3 eq 'i') {
				$self->privmsg($to, "$nick: You have added $mywords $1 (or " . $percentage . "%)");
			}
			else {
				$self->privmsg($to, "$nick: $3 has added $mywords $1 (or " . $percentage . "%)");
			}
			return 1;
		}
		elsif ($3 =~ /(how much do you know|how many (words|definitions) do you (have|know)|how big is your database)/i) {
			$sth = $db->query("SELECT count(*) FROM definitions") || next;
			$defcount = $sth->fetchrow;
			$sth = $db->query("SELECT count(*) FROM dictionary") || next;
			$dictcount = $sth->fetchrow;
			$self->privmsg($to, "$nick: I know $defcount words (and $dictcount are in my dictionary)");
			return 1;
		}
		elsif ($3 =~ /how many times (has|was) (.*?) (been )*(gotten|retrieved|requested)/i) {
			$word = LibUtil::stripcontrol(LibUtil::stripforsql($2));
			$sth = $db->query("SELECT numhits, word FROM definitions WHERE word = '$word'") || next;
			if (($numhits, $addword) = $sth->fetchrow) {
				$self->privmsg($to, "<$addword> $1 $3$4 $numhits times");
			}
			else {
				$self->privmsg($to, "$nick: <$2> hasn't been added yet...");
			}
			return 1;
		}
	}
	else {
		return 0;
	}
}

1;
