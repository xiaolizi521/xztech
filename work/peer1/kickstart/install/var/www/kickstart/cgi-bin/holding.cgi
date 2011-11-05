#!/usr/bin/perl -w

BEGIN {
	use lib '/exports/kickstart/lib';
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

my ($adbh, $kdbh);

sub get_holding {
	my @return;
	my ($id, $mac);
	
	my $dbh = $kdbh;
	my $qry1 = "SELECT mac_list_id,mac_address FROM kickstart_map WHERE new_status = ? LIMIT 10";
	my $sth1 = $dbh->prepare($qry1);
	$sth1->execute("holding");
	$sth1->bind_columns(\($id, $mac));
	while ($sth1->fetch()) {
		print "<!-- $id $mac -->\n";
		push(@return, [ $id, $mac ]);
	}
	$sth1->finish();

	return @return;
}

sub set_burnin {
	if (scalar(@_) != 2) { return 1; }
	my ($macid, $macaddr) = @_;
	my $dbh = $kdbh;

	print "Setting $macaddr to burnin .. ";

	my $mobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
	$mobj->osload("burnin");
	$mobj->status("updateks");
	$mobj->update();

	my $errors = $mobj->error();
	if (scalar(@{$errors}) > 0) {
		print "failed: ".join(" ",@{$errors})."<br>\n";
	}
	else {
		print "done.<br>\n";
		kslog('info', "$macaddr STATUS holding -> updateks");
	}
}

sub link_server {
	if (scalar(@_) != 6) { return 1; }
	my ($macid, $macaddr, $custnum, $servnum, $switch, $portnum) = @_;

	print "Linking $macaddr to $custnum-$servnum on $switch-$portnum";
	print " .. (not really).<br>\n";
}

my $self = $ENV{'REQUEST_URI'};

my $post = new CGI;
my $postdata = $post->Vars();

$kdbh = ks_dbConnect();

print header, start_html("Hold queue"),
	h1("Holding");

if ($postdata) {
	my @macids = $post->param('macid');

	foreach my $macid (@macids) {
		next unless ($postdata->{"action_$macid"});
		my $action = $postdata->{"action_$macid"};
		if ($action eq "burn") {
			set_burnin($macid, $postdata->{"macaddr_$macid"});
		}
		elsif ($action eq "link") {
			link_server($macid,
				$postdata->{"macaddr_$macid"},
				$postdata->{"custnum_$macid"},
				$postdata->{"servnum_$macid"},
				$postdata->{"switch_$macid"},
				$postdata->{"portnum_$macid"}
				);
		}
		else { next ; }
	}
}

print hr();

print "<form method=\"POST\" action=\"$self\">

<table border=1>
  <th>MAC</th>
  <th>Burn</th>
  <th>Link</th>";

foreach my $aref (get_holding()) {
	my $id = $aref->[0];
	my $mac = $aref->[1];
	print "
  <tr>
    <td>
      <input type=\"hidden\" name=\"macid\" value=\"$id\">
      <input type=\"hidden\" name=\"macaddr_$id\" value=\"$mac\">
      $mac
    </td>
    <td>
      <input type=\"radio\" name=\"action_$id\" value=\"burn\" checked>Burn
    </td>
    <td>
      <input type=\"radio\" name=\"action_$id\" value=\"link\">Link
      Cust #<input type=\"text\" name=\"custnum_$id\" size=\"4\">
      Serv #<input type=\"text\" name=\"servnum_$id\" size=\"4\">
      Switch<input type=\"text\" name=\"switch_$id\" size=\"4\">
      Port #<input type=\"text\" name=\"portnum_$id\" size=\"4\">
    </td>
  </tr>";
}


print"
</table>
<input type=\"submit\" value=\"Submit\">
</form>
";

print hr();

($adbh) && $adbh->disconnect();
($kdbh) && $kdbh->disconnect();
