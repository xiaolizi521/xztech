#!/usr/bin/perl -w

#-------------------------------------------------------------------------------
# Company:              Server Beach
# Copyright(c):         Server Beach 2006
# Project:              Kickstart Sub-System
# Code Devloper:        SB Product Engineering
# Creation Date:        2007-04-11
#
# File Type:            CGI
# File Name:            ubuntu.cgi
#
# Description:
#    This script is used to generate the ubuntu.cfg file used for future Debian
#    based installs (Debian, Ubuntu, etc)
# 
#-------------------------------------------------------------------------------

BEGIN {
        use lib "/exports/kickstart/lib";
        require 'sbks.pm';
}

use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;

# Variable Defitions
my ($dbh, $ipaddr, $macaddr, $macobj, $status, $osload);

#-------------------------------------------------------------------------------
# FUNCTION DEFFITIONS
#-------------------------------------------------------------------------------
sub get_kscfg 
{
    my $ks = shift();

    #Set pointer to the OS kickstart configuration file information
    my $kscfg = $Config->{ks_home}."/kscfg/$ks.cfg";

    my @kscfg;

    # Parse the existing preseed file to generate custom install script for server
    if (-e $kscfg) 
    {
        open IFH, "<$kscfg";
        while (<IFH>) 
        {
                chomp;
                if (/(\@\@KSSERVER\@\@)/) { $_ =~ s/$1/$Config->{'ks_host'}/g; }
                if (/(\@\@KSIPADDR\@\@)/) { $_ =~ s/$1/$Config->{'ks_ipaddr'}/g; }
                if (/(\@\@KSDOMAIN\@\@)/) { $_ =~ s/$1/$Config->{'ks_domain'}/g; }
                push(@kscfg, $_);
        }
   }
   else 
   { 
        return 1; 
   }

    # Print the header information
    print header;

    # Write back the preseed configuration information and also
    # write the same information to a local file.
    if (defined $macaddr)
    {
        open LOG, ">/tmp/$ks-$macaddr.log";
    }
    else
    {
        open LOG, ">/tmp/$ks-$ipaddr.log";
    }
    foreach my $line (@kscfg) 
    {
            print "$line\n";
            print LOG "$line\n";
    }
    close LOG;
    return 0;
}

sub validate_mac {
        my $mac_in = $_[0];
        my $mac_out;
        if ($mac_in =~ /[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}/) {
                $mac_out = lc($mac_in);
        } else {
                $mac_out = "INVALID";
        }
        return $mac_out;
}


#-------------------------------------------------------------------------------
# Program MAIN
#-------------------------------------------------------------------------------

#Instantiate new CGI Object
my $post = new CGI;
my $postdata = $post->Vars();

$ipaddr = $ENV{'REMOTE_ADDR'};
if (!defined($ipaddr)) {
        kslog('err', "I need to be called as a CGI");
        exit 1;
}

$dbh = ks_dbConnect();

# Read in variables that were posted to this cgi.
# Currently the macaddr post parameter is not used and a default preseed
# file is sent to all servers, however, this is being left in the file in case
# this is supported in future iterations of the Debian installer
if ($post->param("macaddr")) 
{
    $macaddr = untaint('macaddr', $post->param("macaddr"));
        kslog("info", "Got MAC ($macaddr) from \$post->param()");
}
else 
{       
        kslog('info',"Edgy preseed file requested by $ipaddr");
        $osload = 'edgy';
        my $result = get_kscfg($osload);
        exit 0;
}


# Instantiate new MACFun Object.
$macobj = MACFun->new(dbh => $dbh, macaddr => $macaddr);
$status = $macobj->status();
$osload = $macobj->osload();

my $postconf = $macobj->postconf();

# Get a list of Kickstart PXE Kicks
my @installs = get_ks_list($dbh);

# Search through the oslad variable to see if it matches one 
# in the kickstart configuration list.
if (grep(/^$osload$/, @installs)) 
{
    if ($osload =~ /up$/ || $status !~ /kickstarted|online/) 
    {
        my $result = get_kscfg($osload);
        if ($result == 0) 
        {
                $macobj->status("ksscript");
                $macobj->update();
                kslog('info', "$macaddr STATUS -> ksscript ($osload)");
        }
        else 
        {
                $macobj->status("ksscript_fail");
                $macobj->update();
                kslog('err', "$macaddr FAILED -> ksscript ($osload)");
        }
    }
    else 
    {
        $macobj->pxe("localboot");
        $macobj->update();
        update_pxe($macaddr, "localboot");
        kslog('err', "$macaddr WARNING ATTEMPTED REKICKSTART");
    }
}
else 
{
        get_kscfg($osload);
        kslog('info', "$macaddr got $osload");
}

1;

