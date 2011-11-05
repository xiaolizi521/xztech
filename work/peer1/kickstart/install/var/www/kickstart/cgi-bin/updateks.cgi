#!/usr/bin/perl -w

BEGIN {
        use lib "/exports/kickstart/lib";
        require 'sbks.pm';
}
use strict;
use CGI ':standard';
use CGI ':cgi-lib';

my ( $uid, $post, $postdata, $macaddr, $osload, $doRaidSetup );

print header();

open IFH, "</etc/passwd";
while (<IFH>) {
        if (/^(apache|www-data):/) {
                chomp;
                $uid = ( split( /:/, $_ ) )[2];
                last;
        }
        else { next; }
}
close IFH;
if ( ( $< != $uid ) && ( $< != 0 ) ) { exit 1; }

$post     = new CGI;
$postdata = $post->Vars();

$macaddr = untaint( 'macaddr', $postdata->{'macaddr'} );
($macaddr) || kslog( "err", "Invalid or null macaddr" );

$osload = untaint( 'words', $postdata->{'osload'} );
($osload) || kslog( "err", "Invalid or null osload" );

##########################################################
# Initialize helper vars 
########################################################
my $dbh      = ks_dbConnect();
my $macobj   = MACFun->new( dbh => $dbh, macaddr => $macaddr );
my $postconf = $macobj->postconf();
my $status   = $macobj->status();


##########################################################
# Checks if we have a postconf setup for this server
##########################################################
unless ( keys(  %$postconf ) ) {
        print "status=updateks_fail";
        kslog( 'err', "[$macaddr] No postconf found" );
}

##########################################################
# Let's find if this system has automated RAID.
# If it does set the flag. This needs to be done before
# update_ks changes the 'status' of the server
#####################################################
if ( $postconf->{'HW_RAID'} =~ /yes/i  
     && ! ($status =~ /hwraid_setup_done/i) 
     && ! ($osload =~ /localboot|sbrescue/i ) ) {
        $doRaidSetup = 1;
}

kslog( "info", "[$macaddr] Updating KS with $osload" );
my $result = update_ks( $macaddr, $osload );

if ( !$result ) {
        print "status=updateks_fail";
        kslog( "info", "[$macaddr] Told admin server: status=updateks_fail" );
}

if ( $doRaidSetup ) {
        #########################################################
        # Initiate the automated RAID setup.
        # Values like status, osload, pxe will be set back to the 
        # target OS install after the setup is done from the 
        # hw_raidsetup taskfile
        #####################################################
        kslog( 'info', "[$macaddr] This server has automated RAID." );
        $macobj->pxe("sbrescue");
        $macobj->task("hw_raidsetup");
        $macobj->update();
        update_pxe( $macaddr, $macobj->pxe() )
            || printErr("Failed pxed transaction. Is pxed running?");
        kslog( 'info', "[$macaddr] First boot will configure the RAID setup." );
}
else {
        #########################################################
        # Perform the actions that used to be performed by the
        # cronjobs in pre-fusion kickstart
        ######################################################
        kslog( 'info', "[$macaddr] This server has no RAID or has already been configured" );
        #########################################################
        # Perform the actions that used to be performed by the
        # cronjobs in pre-fusion kickstart
        ######################################################
        my $pxefile;
        my $mac_address_id;

        kslog( "INFO", "[$macaddr] serching mac_list ID." );
        $mac_address_id = &getMacListId( $dbh, $macaddr )
            || printErr("[$macaddr] address not found on DB.");

        kslog( "INFO", "[$macaddr] searching pxefile for this system." );
        $pxefile = &getPxeFile( $dbh, $mac_address_id )
            || printErr("[$macaddr] pxefile not found for this system");

        kslog( "INFO", "[$macaddr] setting new target: $pxefile." );
        update_pxe( $macaddr, $pxefile )
            || printErr("Failed pxed transaction. Is pxed running?");

}

#########################################################
# Cleanup
#####################################################
$dbh->disconnect;


#########################################################
# If we reached this point the initial kick should be successful
#####################################################
print "status=updateks";
kslog( "info", "[$macaddr] told admin server: status=updateks" );

1;


sub isValidPxe {
 	my ( $dbh, $pxe ) = @_;
        my $query_pxefile = <<END;
SELECT id 
FROM pxe_list 
WHERE pxefile = ?
END

	my $result = $dbh->selectall_arrayref( $query_pxefile, undef, $pxe );
	if ( $result->[0] ) { return 1; }
	else { return 0; }
}

sub trim {
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}

sub printErr {
	my $message = shift;
	my $reason  = shift;
	$reason = "status=updateks_fail" unless $reason;
	print $reason;
	kslog( "ERR", $message );
}

sub getMacListId() {
	my $row;
	my $rows_;
	my @rows_array;
	my ( $dbh, $mac ) = @_;
        my $query_mac_list = <<END;
SELECT id 
FROM mac_list 
WHERE mac_address = ? 
END

	$rows_ = $dbh->selectall_arrayref( $query_mac_list, undef, $mac );
	$row = $rows_->[0];
	$row || return 0;
	@rows_array = @{$row};
	return $rows_array[0];
}

sub getPxeFile() {
	my $row;
	my $rows_;
	my @rows_array;
	my ( $dbh, $mac_id ) = @_;
        my $query_xref_macid_osload = <<END;
SELECT pxefile 
FROM xref_macid_osload 
        LEFT JOIN pxe_list 
        ON ( xref_macid_osload.pxe_list_id = pxe_list.id ) 
WHERE mac_list_id = ? 
END

	$rows_ =
	  $dbh->selectall_arrayref( $query_xref_macid_osload, undef, $mac_id );
	$row = $rows_->[0];
	$row || return 0;
	@rows_array = @{$row};
	return $rows_array[0];
}

