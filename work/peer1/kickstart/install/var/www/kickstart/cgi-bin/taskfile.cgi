#!/usr/bin/perl -w

BEGIN {
	use lib '/exports/kickstart/lib';
	require 'sbks.pm';
}

use strict;
use CGI ':cgi-lib';
use CGI ':standard';

my ($post, $postdata, $macaddr, $task, $tfiledir, $taskfile, $dbh, $macobj);

$post = new CGI;
$postdata = $post->Vars();

print header;

$macaddr = untaint('macaddr', $postdata->{'macaddr'});
($macaddr) || kslog("err", "Invalid or null macaddr");
$task = untaint('words', $postdata->{'task'});

if (($postdata->{version}) && ($postdata->{version} eq "new")) {
    $tfiledir = "/exports/kickstart/taskfiles.new";
}
else {
    $tfiledir = "/exports/kickstart/taskfiles";
}

$dbh = ks_dbConnect();
$macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
($task) || ($task = $macobj->task());

$taskfile = "$tfiledir/default.txt";

if (grep(/^$macaddr$/, @{$Config->{bootServerMacs}})) {
    $taskfile = "$tfiledir/bootserver.txt";
}
elsif ($task) { $taskfile = "$tfiledir/$task.txt"; }
elsif ($macobj->status() eq "new") { $taskfile = "$tfiledir/default.txt"; }

if (-f $taskfile) {
        kslog("info", "$macaddr has requested $taskfile");
	open IFH, "<$taskfile";
	while (<IFH>) { print; }
	close IFH;
}

$dbh->disconnect();
exit 0;
