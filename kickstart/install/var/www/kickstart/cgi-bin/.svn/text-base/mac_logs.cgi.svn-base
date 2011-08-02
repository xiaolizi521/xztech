#!/usr/bin/perl

# Author: Sean Davis <sdavis@serverbeach.com>
# Date: 2007-04-17
# Purpose: extract relevant log information for a MAC address

# Which logfiles to read
my @logfiles = ("script.log","daemon.log");
#my @logfiles = ("script.log");
# Where they are stored
my $logdir = "/exports/kickstart/logs";

# Pull in everything we need
use strict;
BEGIN {
	use CGI ':standard';
	use CGI ':cgi-lib';
}
use Time::ParseDate;
use POSIX qw(strftime);

my $post;

# In order to properly sort this data, we have tacked the epoch time value to the desired output line.
# This is a customer comparison function that will sort the data based solely on this number.  We don't want
# to sort using any other data as it will cause the information to possibly get out of order
sub sort_by_date
{   
    my ($compare1, @extra) = split(/ /,$a);
    my ($compare2, @extra) = split(/ /,$b);
    return -1 if ($compare1 > $compare2);
    return 1 if ($compare1 < $compare2);
    return 0 if ($compare1 == $compare2);
}

sub validate_mac {
        my ($mac_in,$mac_out);
	$mac_in = $_[0];
        if ($mac_in =~ /^[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}$/) {
		$mac_out = lc($mac_in);
	} else {
		$mac_out = "INVALID";
	}
	return $mac_out;
}

$post = new CGI;

my $posted_mac = $post->param("macaddr");

if (!$posted_mac) {
	print "NEED INPUT!<br>\r\n";
	exit;
}

my $mac = validate_mac($posted_mac);

if ($mac eq "INVALID") {
	print "Invalid input.<br>\r\n";
	exit;
}


# The following is used to go back one logfile if the current one if less than x number of lines
# Currently, x number of lines is 1000
my @morelogs;
foreach my $logfile (@logfiles) 
{
    my $fpath = $logdir . "/" . $logfile;
    my $num_lines = `wc -l $fpath | awk '{print $1}'`;
    push @morelogs, "$logfile.0" if ($num_lines < 1000);
}
push @logfiles, @morelogs;

# This is the actual part of the script that does the work
my @temp;
foreach my $logfile (@logfiles) {
    my $fpath = $logdir . "/" . $logfile;
    next if (! -e $fpath);
	open(FOO,"tail -2000 $fpath |");
	while(my $line = <FOO>) {
		chomp($line);
		next unless ($line =~ /$mac/);
		my ($when,$datestamp,$log_entry,$service);
		if ($line =~ /^(.*) ks1 ([a-zA-Z0-9\._]*)\[[0-9]*\]: (.*)$/ ||
			$line =~ /^(.*) ks1 ([a-zA-Z0-9\._]*): (.*)$/) {
			$when = parsedate($1);
			$service = $2;
			$log_entry = $3;
		}
        # The parsedate() above basically tacks the current year onto the date since the files do not have
        # it stored.  If log entries span a year (i.e. Dec 31 => Jan 1) this will cause the dates to be off.
        # Below is a quick check to see if the data is in the future.  If so, subtract the seconds in a year from
        # the date.  Also, attempt to compensate for leap years
        if ($when > time())
        {
            my ($Sec, $Min, $Hr, $Day, $Month, $Year, $WeekDay, $DayOfYear, $IsDST) = localtime(time);
            $Year += 1900;
            if ($Year % 4)
            {   
                # not a leap year
                $when = $when - (60 * 60 * 24 * 365);
            }
            else
            {   
                # leap year
                $when = $when - (60 * 60 * 24 * 366);
            }
        }
		my $datestamp = strftime "%Y/%m/%d %H:%M:%S",
			localtime($when);

                $log_entry =~ s/"/&#63/g;
                next if ($service eq "" || $log_entry eq "");
        # Instead of printing, push all of the data into an array that we can sort
       	push @temp, "$when <entry recorded=\"$datestamp\" logfile=\"$logfile\" service=\"$service\" text=\"$log_entry\"/>";
	}
	close(FOO);
}

# Print header information
print "Content-Type: text/xml\n\n";
print "<?xml version=\"1.0\" encoding=\"US-ASCII\"?>\n";
print "<?xml-stylesheet type=\"text/xsl\" href=\"/logs.xsl\"?>\n";
print "<log>\n";

# sort the array using a custom comparison function
@temp = sort sort_by_date reverse(@temp);
foreach my $line (@temp)
{
    # We tacked a little extra info onto the entry so that we coud sort it.  We don't want to print that data
    my ($junk, @data) = split(/ /, $line);
    print "@data\n";
}

# End the xml data
print "</log>\n\n";
