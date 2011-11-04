# $Id: IMDb.pm,v 1.15 2003/05/14 17:03:13 jenni Exp $
package IMDb;

use LWP::UserAgent;

sub init {
	&::addhandler("imdb", \&imdb, 0, 1, 0);
	return 0;
}

sub destruct {
	&::delhandler("imdb");
	return 0;
}

sub getinfo {
	my ($page) = @_;
	$page =~ s/\n//g;
	undef $title;
	undef $plot;
	undef $rating;
	undef $cast;
	if ($page =~ m|<title>(.*?)</title>|i) {
		$title = $1;
		$title =~ s/\&\#([0-9]+)\;/pack("C", $1)/eg;
	}
	if ($page =~ m|<b class=\"ch\">Plot (Outline\|Summary):</b> ([^<]+)|i) {
		$plot = $2;
		if (length($plot) > 100) {
			$plot = substr($plot, 0, 97) . "...";
		} elsif ($3) {
			$plot .= "...";
		}
	}
	if ($page =~ m|<b>([0-9.]{3})/10</b> \(([0-9,]+) votes\)|i) {
		$rating = "$1 ($2 votes)";
		if ($page =~ m|top 250: #([0-9]+)|i) {
			$rating .= ", Top 250 #$1";
		}
	}
	else {
		$rating = "N/A";
	}
	if ($page =~ m|cast[^:]*: </b></td></tr> <tr><td valign="top"><a href="/Name\?.*?">([^<]+)</a></td><td valign="top" nowrap="1"> \.\.\.\. </td><td valign="top">[^<]+</td></tr><tr><td valign="top"><a href="/Name\?.*?">(.*?)</a></td>|i) {
		$cast = "$1, $2";
		$cast =~ s/\&\#([0-9]+)\;/pack("C", $1)/eg;
	}
	return ($title, $plot, $rating, $cast);
}

sub saymovieinfo {
	my ($self, $event, $to, $url, $page) = @_;
	my ($title, $plot, $rating, $cast) = &getinfo($page);
	my ($message);
	$message = $event->nick . ": $title";
	$message .= " - Rating: $rating" if $rating ne 'N/A';
	$message .= " - Plot: $plot" if $plot ne '';
	$message .= " - Starring: $cast" if $cast ne '';
	$message .= " - URL: $url";
	$self->privmsg($to, $message);
}

sub saynameinfo {
  my ($self, $event, $to, $url, $page) = @_;
  $self->privmsg($to, $event->nick . ": Sorry, actor/actress info is not yet available.");
}

sub imdb {
  my ($self, $event, $to, $args) = @_;
  my ($ua, $req, $res, $page, $content);

  $origquery = $args;
  $query = LibUtil::urlencode($args);

  $ua = new LWP::UserAgent;
  $ua->agent("JenniIMDb 0.1 " . $ua->agent);
  $req = new HTTP::Request GET => "http://www.imdb.com/Title?title=$query";
  $res = $ua->request($req);
  if ($res->is_success) {
    $page = $res->content;
    if ($page =~ m|<BASE HREF="http://us.imdb.com/Title\?([0-9]+)">|i) {
      $url = "http://us.imdb.com/Title?$1";
      &saymovieinfo($self, $event, $to, $url, $res->content);
    }
    else {
      $req = new HTTP::Request GET => "http://www.imdb.com/Find?select=All&for=$query";
      $res = $ua->request($req);
      $page = $res->content;
      if ($res->request->url->as_string =~ /\/Find\?/) {
        if ($page =~ m|<H1>We're Sorry</H1>|) {
          $self->privmsg($to, $event->nick . ": IMDb found no matches for <$origquery>");
        }
        elsif ($page =~ m|Most popular (.*?) searches:</p><ol><li><a href=\"(.*?)\">(.*?)</a></li>\n</ol>|is) {
          $url = "http://us.imdb.com$2";
          $req = new HTTP::Request GET => $url;
          $res = $ua->request($req);
          $page = $res->content;
          &saymovieinfo($self, $event, $to, $url, $page);
        }
        else {
          $self->privmsg($to, $event->nick . ": IMDb found more than one match, view them here: " . $res->request->url->as_string);
        }
      }
      else {
        $url = $res->request->url->as_string;
        &saymovieinfo($self, $event, $to, $url, $page);
      }
    }
  }
  else {
    $self->privmsg($to, $event->nick . ": An error occurred while attempting to contact IMDb.");
  }
}

1;
