#!/usr/bin/perl -w
                  
# =============================================================================
# Company:              ServerBeach, Ltd. (A Peer1 Company)
# Copyright(c):         Server Beach 2006-2008
# Project:              Kickstart Sub-System
# Code Devloper:        SB Product Engineering
#
# File Type:            CGI
# File Name:			AuditFail.cgi	            
#
# Description:
# 	The purpose of this CGI is to report back all servers (according to MAC)
#	that have failed the audit process.  
#
# Dependencies/Known Assumptions:
# 	This script is utilized as a report channel and is called from a within a 
#	task file from within the SBRescue RAM Disk Image.
# =============================================================================

# Program Library usage and pragma defintions
# Include standard ServerBeach Specific Perl Module Libraries
BEGIN {                              
    use lib "/exports/kickstart/lib";
    require 'sbks.pm';           
}                                    

# Include the Perl Standard CGI library
use strict;                          
use CGI ':standard';                 
use CGI ':cgi-lib';                  


# Variable Defitions
my ($post, $postData, $macList);

# Print HTTP Header
print header();

# Establish database connection.
$ksdbh = ks_dbConnect();

# Pull all records from the KS Database where audit has reported "Fail" into an array.
my $macListResult = $ksdbh->selectall_arrayref("SELECT mac_address FROM kickstart_map WHERE new_status = 'audit_fail'");

# Process resultset and push this into an array called $macList
foreach my $row (@$macListResult) { push @$macList, $row->[0]; }

# The following takes a list of mac addresses and then queries the database
# to determine the last error message about the server in the database.
# reference:/exports/kickstart/lib/SB/Common.pm
my $errorMessages = getErrorMessages($macList);

foreach my $row (@$errorMessages) {
    foreach my $key (sort(keys(%$row))) {
        print "$key=$row->{$key}&";
    }
    print "\n";
}

# Release database connection.
$ksdbh->disconnect();

1;
