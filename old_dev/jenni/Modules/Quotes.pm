# $Id: Quotes.pm,v 1.10 2003/02/15 02:29:05 jenni Exp $
package Quotes;

use Libraries::Util;

sub init {
	&::addhandler("quote", \&quote, 1);
	&::addhandler("addquote", \&addquote);
	&::addhandler("quotecount", \&quotecount);
	&::addhandler("lastquote", \&lastquote);
	return 0;
}

sub destruct {
	&::delhandler("quote");
	&::delhandler("addquote");
	&::delhandler("quotecount");
	&::delhandler("lastquote");
	return 0;
}

sub addquote {
	my ($self, $event, $to, $quote) = @_;
	&quote($self, $event, $to, $quote, '+');
}

sub quote {
	my ($self, $event, $to, $quote, $plusminus) = @_;
	my ($db) = $::db;
	$quote = LibUtil::stripforsql($quote);
	if ($plusminus eq '+') {
		$sth = $db->query("INSERT INTO quotes (userid, quote) VALUES ('" . LibUtil::userid($event) . "', '$quote')") || next;
		$sth = $db->query("SELECT LAST_INSERT_ID()") || next;
		$lastid = $sth->fetchrow;
		$self->privmsg($to, $event->nick . ": Quote $lastid added");
	}
	elsif ($plusminus eq '-') {
		if (LibUtil::is_admin($event->userhost)) {
			$sth = $db->query("DELETE FROM quotes WHERE quoteid = '$quote'") || next;
			if (!$sth->affectedrows) {
				$self->privmsg($to, $event->nick . ": Error deleting quote <$quote>");
			} else {
				$self->privmsg($to, $event->nick . ": Quote <$quote> deleted");
			}
		}
	}
	else {
		if ($quote =~ /^[0-9]+$/) {
			$quoteid = $quote;
			$sth = $db->query("SELECT quote FROM quotes WHERE quoteid = '$quoteid'") || next;
			$quote = $sth->fetchrow;
		} elsif (length($quote) > 0) {
			$sth = $db->query("SELECT quoteid, quote FROM quotes WHERE quote LIKE '%$quote%' ORDER BY RAND() LIMIT 1") || next;
			($quoteid, $quote) = $sth->fetchrow;
		} else {
			$sth = $db->query("SELECT count(*) FROM quotes") || next;
			$quotecount = $sth->fetchrow;
			$tries = 5;
			while ($tries--) {
				if ($tries > 1) {
					$quoteid = sprintf("%1.f", (rand() * ($quotecount - 1)) + 1);
					$sth = $db->query("SELECT quoteid, quote FROM quotes WHERE quoteid = '$quoteid'") || next;
				} else {
					$sth = $db->query("SELECT quoteid, quote FROM quotes ORDER BY RAND() LIMIT 1") || next;
				}
				($quoteid, $quote) = $sth->fetchrow;
				break if defined($quote);
			}
		}
		if (!$quote) {
			$self->privmsg($to, $event->nick . ": No such quote exists");
		} else {
			$self->privmsg($to, $event->nick . ": Quote $quoteid: $quote");
		}
	}
}

sub quotecount {
	my ($self, $event, $to, $quote) = @_;
	my ($db) = $::db;
	if (length($quote) > 0) {
		$sth = $db->query("SELECT count(*) FROM quotes WHERE quote LIKE '%" . LibUtil::stripforsql($quote) . "%'") || next;
	} else {
		$sth = $db->query("SELECT count(*) FROM quotes") || next;
	}
	$count = $sth->fetchrow;
	if (length($quote) > 0) {
		$self->privmsg($to, $event->nick . ": $count quotes contain <$quote>");
	} else {
		$self->privmsg($to, $event->nick . ": There are $count quotes");
	}
}

sub lastquote {
	my ($self, $event, $to, $quote) = @_;
	my ($db) = $::db;
	$sth = $db->query("SELECT max(quoteid) FROM quotes") || next;
	$lastquote = $sth->fetchrow;
	$self->privmsg($to, $event->nick . ": $lastquote");
}

1;
