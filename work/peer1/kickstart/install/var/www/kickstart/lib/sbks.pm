#!/usr/bin/perl -w

use lib qw(/exports/kickstart/lib);
use strict;

use SB::Config;
use SB::MACFun;
use SB::Logger;
use SB::Database;
use SB::Provisioning ":all";
use SB::Common ":all";
use SB::Switch ":all";
use SB::RapidReboot qw(rebootByMac RapidReboot);
use SB::Linkserver ":all";
use SB::SBAdmin "sbadmWrapper";
use SB::Bootinfo ":all";

$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

#############################
#       Bootinfo.pm         #
#############################
sub dhcp_refresh ;
sub new_update_pxe ;
sub update_pxe ;
sub clean_mac ;

#############################
#       Common.pm           #
#############################
sub is_running ;
sub lwpfetch ;
sub untainat ;
sub register ;
sub getErrorMessages ;

#############################
#       Database.pm         #
#############################
sub ks_dbConnect ;
sub adm_dbConnect ;

#############################
#       Linkserver.pm       #
#############################
sub linkServer ;
sub macFinder ;
sub getCustomerProductIp ;

#############################
#       Logger.pm           #
#############################
sub logks ;
sub logsys ;

#############################
#       Provisioning.pm     #
#############################
sub provcheck ;
sub fetch_licenses ;
sub fetch_postconf ;
sub get_ks_list ;
sub hwcheck ;
sub get_mac_by_ip ;
sub new_get_mac_by_ip ;
sub isValidOS ;
sub isValidPanel ;
sub update_ks ;
sub adm_online ;

#############################
#       RapidReboot.pm      #
#############################
sub rebootByMac ;

#############################
#       SBAdmin.pm          #
#############################
sub sbadmWrapper ;

#############################
#       Switch.pm           #
#############################
sub switchPortInfo ;
sub portvlan ;
sub portControl ;
sub portstate ;


#############################
#       Local functions     #
#############################

# This is a frontend for logks the ensures that all of the 
# current scripts calling kslog will maintain the same functionality
# as before, namely exiting upon logging an error

sub kslog {
    logks(@_);
	my ($loglvl, $logmsg) = @_;
	if ($loglvl =~ /err/i) {
		exit 1;
	}
	return 0;
}


1;
