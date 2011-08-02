#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      Database.pm
#
# Overview:
#   Perl modules to be included in future kickstart perl scripts that require connections to one
#   of the databases at Server Beach.
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::Database;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw(ks_dbConnect adm_dbConnect);
    %EXPORT_TAGS = ();
    @EXPORT_OK = qw();

}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use DBI;
use Net::Ping ();

#############################
#    Serverbeach modules    #
#############################
use SB::Config;
use SB::Logger;


$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};


# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# ks_dbConnect()
#
#   parameters
#       none
#
#   return value
#       success : handle to the open database connection
#       failure : undef
#
#   Overview
#       used to instanciate a connection to the kickstart database
#
#------------------------------------------------------------------------------------
sub ks_dbConnect {
        my($dbname,$dbhost,$dbuser,$dbpass,$ldbh);
        $dbhost = $Config->{ks_db_host};
        $dbname = $Config->{ks_db_name};
        $dbuser = $Config->{ks_db_user};
        $dbpass = $Config->{ks_db_pass};

        # kschwerd @ 2007-07-02
        # Changed RaiseError => 1 to RaiseError => 0 as this causes the script to exit
        # if there is a database error without ever giving us the cahnce to log the error.  
        # The better method is to return the error and handle it accordingly.
        $ldbh = DBI->connect("dbi:Pg:dbname=$dbname;host=$dbhost",
                "$dbuser", "$dbpass", { AutoCommit => 1, RaiseError => 0} );
        if (($ldbh) && ($ldbh->ping())) { return $ldbh; }
        else 
        { 
                # Logging the message with level ERR.  This will cause the script to
                # exit.  This was added to ensure the same behaviour as before the 
                # changes, though it should be changed to a warning message once all
                # dependencies have been corrected
                logsys("WARNING","ks_dbConnect(): Unable to connect to database $dbname at $dbhost");
                logsys("WARNING","ks_dbConnect(): " . DBI->errstr);
                return undef; 
        }
}

#------------------------------------------------------------------------------------
# adm_dbConnect()
#
#   parameters
#       none
#
#   return value
#       success : handle to the open database connection
#       failure : undef
#
#   Overview
#       used to instanciate a connection to the admin database
#
#------------------------------------------------------------------------------------
sub adm_dbConnect {
        my($dbname,$dbhost,$dbuser,$dbpass,$ldbh);
        $dbhost = $Config->{adm_db_host};
        $dbname = $Config->{adm_db_name};
        $dbuser = $Config->{adm_db_user};
        $dbpass = $Config->{adm_db_pass};

        $ldbh = DBI->connect("dbi:Pg:dbname=$dbname;host=$dbhost",
                "$dbuser", "$dbpass", { AutoCommit => 1, RaiseError => 0} );
        if (($ldbh) && ($ldbh->ping())) { return $ldbh; }
        else 
        { 
                logsys("WARNING","adm_dbConnect(): Unable to connect to database $dbname at $dbhost");
                logsys("WARNING","adm_dbConnect(): " . DBI->errstr);
                return undef; 
        }
}

1;
