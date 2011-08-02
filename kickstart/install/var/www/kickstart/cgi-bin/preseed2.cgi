#!/usr/bin/perl -w

# Load SBks modules
BEGIN {
        use lib "/exports/kickstart/lib";
        require 'sbks.pm';
}

# Load perl modules
use LWP::Simple;
use XML::Simple;
use CGI ':standard';
use CGI ':cgi-lib';
use POSIX;

# Variable Defitions
my ($ipaddr, $dbh, $macaddr, $macobj, $osload);

# Functions
sub get_deb_based_kscfg {
        my $ks = shift();
        if ( -e $Config->{ks_home}."/kscfg/$ks.cfg")
        {
                $kscfg = $Config->{ks_home}."/kscfg/$ks.cfg";
        }
        else
        {
                kslog('err', "Unable to find /exports/kickstart/kscfg/$osload.cfg");
                return 1;
        }
	#########################################
        #search for custom partition pre-configuration templates
        #########################################
	my $cpCfg = $Config->{ks_home} . "/kscfg/custompart/preseed.cfg";
        if ( -e $Config->{ks_home} . "/kscfg/custompart/$ks.cfg" ) {
                $cpCfg = $Config->{ks_home} . "/kscfg/custompart/$ks.cfg";
        }
	
        my @kscfg;
	#########################################
        #Custom partition pre-configuration file
        #########################################
	my $noCustomPart = 1;
        if ( -e $cpCfg ) {
                my $cpResource  = "http://$Config->{'ks_ipaddr'}/upi/devices/$macaddr/preseed/partition";
                my $cpXml       = get($cpResource);
                my $cpSimpleXml = new XML::Simple;
                my $cpXmlData   = $cpSimpleXml->XMLin($cpXml);
                if ( $cpXmlData->{get}->{status} =~ /success/i ) {
                        kslog( "info", "[$macaddr]Found a custom partitioning scheme." );
                        $noCustomPart = 0;
                        open IFH, "<$cpCfg";
                        while (<IFH>) {
                                chomp;
                                if (/(\@\@CUSTOMPART\@\@)/) {
                                        $_ =~ s/$1/$cpXmlData->{get}->{message}/g;
                                }
                                if (/(\@\@KSSERVER\@\@)/) {
                                        $_ =~ s/$1/$Config->{'ks_host'}/g;
                                }
                                if (/(\@\@KSIPADDR\@\@)/) {
                                        $_ =~ s/$1/$Config->{'ks_ipaddr'}/g;
                                }
                                if (/(\@\@KSDOMAIN\@\@)/) {
                                        $_ =~ s/$1/$Config->{'ks_domain'}/g;
                                }
                                push( @kscfg, $_ );
                        }
                }
        }
        # Parse and generate ks_host, ks_ipaddr, ks_domain
        if (-e $kscfg && noCustomPart )
        {
                kslog( "info", "[$macaddr]Found no custom partitioning scheme. Using default." );
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
        elsif( $noCustomPart) {
                kslog( "err", "[$macaddr]Preseed and custompart couldn't be found." );
                return 1;
        }
	# Print the header info
        print header;

	# Write back the preseed info
        if (defined $macaddr)
        {
                open LOG, ">/tmp/preseed-$macaddr.log";
        }
        else
        {
                open LOG, "/tmp/preseed-$ipaddr.log";
        }
        foreach my $line (@kscfg)
        {
                print "$line\n";
                print LOG "$line\n";
        }
        close LOG;
        return 0;
}

# Main
my $post = new CGI;
my $postdata = $post->Vars();
$ipaddr = $ENV{'REMOTE_ADDR'};
if (! defined $ipaddr) {
		kslog( 'err', "I need to be called as a CGI" );
        exit 1;
}
$dbh = ks_dbConnect();
$macaddr = get_mac_from_log($ipaddr);
$macobj = MACFun->new( dbh => $dbh, macaddr => $macaddr );
$osload = $macobj->osload();

$result = get_deb_based_kscfg($osload);
if ($result == 1)
{
	kslog('err', "Preseed failed check logs");
	exit 1;
}
else
{
	kslog('info', "$osload preseed requested by $ipaddr for $macaddr");
}
1;
