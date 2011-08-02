#!/opt/perl/bin/perl -w

BEGIN {
        use lib "/exports/kickstart/lib";
            require 'sbks.pm';
}

use strict;
use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;

my ($self, $post, $postdata, $macaddr);

$self = "$ENV{'REQUEST_URI'}";

print header,
    start_html("Hardware Posting Tool"),
    h1("Hardware Posting Tool");

print "
<form action=\"$self\" method=\"POST\">
<table cellspacing=0 cellpadding=3 border=0>
<tr align=\"center\">
    <td>MAC Address:&nbsp;</td>
</tr>
<tr>
    <td><input name=mac_address type=text length=18></td>
</tr>
</table>
<input type=submit>
</form>
<hr>
";

$post = new CGI;
$postdata = $post->Vars();

$macaddr = untaint('macaddr', $postdata->{'mac_address'});

if ($macaddr) {
    my $dbh = ks_dbConnect();
    my $mobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);

    my $boxinfo = $mobj->hardware();
    $boxinfo->{macaddr} = $mobj->macaddr();
    $boxinfo->{ipaddr} = $mobj->ipaddr();
    $boxinfo->{status} = "ready";

    print "Posting to register_server.php<br>\n";
    foreach my $key (sort(keys(%{$boxinfo}))) {
        print "$key => $boxinfo->{$key}<br>\n";
    }

    my $postres = lwpfetch($Config->{'adm_regnewurl'}, $boxinfo, undef);
    print "Admin server said: ".$postres->[1]."<br>\n";
}
