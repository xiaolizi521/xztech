#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      Common.pm
#
# Overview:
#   Provides functions for basic functions shared amongst various scripts and libraries
#   that do not otherwise fit into any other category
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::Common;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    @EXPORT = qw();
    %EXPORT_TAGS = ( 'all' => [ qw(
        lwpfetch
        is_running
        untaint
        register
        getErrorMessages
        daemonize
    ) ]);
    @EXPORT_OK = ( @{ $EXPORT_TAGS{'all'} } );

}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use SB::Database;
use LWP::UserAgent;
use Compress::Zlib;
use POSIX "setsid";

#############################
#    Serverbeach modules    #
#############################
use SB::Config;
use SB::MACFun;
use SB::Logger;

$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

# ===============================================================================================
# Package scoped variable definitions
# ===============================================================================================


# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# lwpfetch()
#
#   parameters
#       1st arg: URL, http or https, user/pass optional
#       2nd arg: hash ref, array ref, or scalar with info to post
#       3rd arg: file name to dump data to (optional)
#
#   return value
#       Returns: [ result, content ]
#
#   Overview
#       Generic function call to post info to a web script and return the results
#
#------------------------------------------------------------------------------------

sub lwpfetch 
{
    my ($url, $data, $file, $debug) = @_;
    my ($auth, $method, $content, $ua, $request, $result);

    # Sanity check to ensure that we were given proper info
    if ($url !~ /^(http|https):\/\//) 
    {
        logsys ("ERR","lwpfetch(): No URL supplied");
        return [ 0, 'Missing information: URL' ];
    }

    if ($url =~ /\@/) {
        $url =~ s/(http|https):\/\/(.*:.*)\@/$1:\/\//;
        $auth = $2;
    }

    # Check to see what type of data we were given as we need to format it correctly
    # for posting
    if (ref($data) eq "HASH") 
    {
        my %tmphash = %{$data};
        my @tmpary = ();
        while (my($n,$v) = each(%tmphash)) 
        {
            push(@tmpary, "$n=$v");
        }
        $method = "POST";
        $content = join("&", @tmpary);
    }
    elsif (ref($data) eq "ARRAY") 
    {
        my @tmpary = @{$data};
        $method = "POST";
        $content = join("&", @tmpary);
    }
    elsif (ref($data) eq "SCALAR") 
    {
        $method = "POST";
        $content = $$data;
    }
    elsif ($data && ($data ne "")) 
    {
        $method = "POST";
        $content = $data;
    }
    else 
    {
        $method = "GET";
        $content = "";
    }
    if ($debug ) 
    {
            kssys("INFO", "URL = $url, Content = " . $content || 0 .", File = " . $file || 0 );
    }

    $ua = LWP::UserAgent->new();

    # Allow the user agent to re-post if a redirect is returned
    push @{ $ua->requests_redirectable }, 'POST'; 
    $request = HTTP::Request->new($method => $url);

    # Check if we have auth information and use it if we do
    if ($auth && ($auth ne "")) 
    {
        $request->authorization_basic(split(/:/, $auth));
    }
    # Check if there is content to post
    if ($content && ($content ne "")) 
    {
        $request->content($content);
        $request->content_type('application/x-www-form-urlencoded');
    }
    # Check if we are supposed to download the results to a file
    if ($file && ($file ne "")) 
    {
        # This allows us to fetch large files without consuming memory
        $result = $ua->request($request, $file);
    }
    else 
    {
        $result = $ua->request($request);
    }

    if ($result->is_success()) 
    {
        # If we were fetching a file, return the name of the file
        if ($file && ($file ne "")) 
        {
            return [ 1, $file ];
        }
        # Else just return the content that was fetched
        else 
        {
            return [ 1, $result->content() ];
        }
    }
    else 
    {
        return [ 0, $result->status_line() ];
    }
}


#------------------------------------------------------------------------------------
# is_running()
#
#   parameters
#       none
#
#   return value
#       undef : Success
#       1     : Process already running
#
#   Overview
#       Check to see if a process is already running
#
#------------------------------------------------------------------------------------


sub is_running 
{
    my $return = undef;

    # Get the name of the current program and whack off any leading path info
    (my $prog = $0) =~ s/.*\///g;

    # Check Config to find out where we should write state info
    my $statefile = $Config->{'ks_state'}."/$prog";

    # if the file exists 
    if (-e "$statefile") 
    {
        # get the pid of the process 
        my $oldpid = `cat $statefile`; chomp($oldpid);

        # and see if it is still running
        if (-e "/proc/$oldpid") 
        {
            logks('NOTICE', "$prog already running ($oldpid)");
            $return = 1;
        }
        # if not write pid of this instance to the state file
        else 
        {
            open OFH, ">$statefile";
            print OFH $$;
            close OFH;
            $return = undef;
        }
    }
    # if the file doesn't exist, create it with the pid of the current process
    else 
    {
        open OFH, ">$statefile";
        print OFH $$;
        close OFH;
        $return = undef;
    }
    return $return;
}



#------------------------------------------------------------------------------------
# untaint()
#
#   parameters
#       $type       :   type of data we are looking for
#       $unclean    :   search string to check for type
#
#   return value
#       undef : Success
#       1     : Process already running
#
#   Overview
#       Retrieve specified search pattern from a string
#
#------------------------------------------------------------------------------------

sub untaint 
{
    my ($type, $unclean) = @_;
    my $clean = undef;
    return undef unless (($type) && ($unclean));

    if ($type eq "macaddr") 
    {
        $unclean =~ /^((\w{2}:){5}\w{2})$/;
                $clean = lc($1); 
    }
    elsif ($type eq "ipaddr") 
    {
        $unclean =~ /^((\d{1,3}\.){3}\d{1,3})$/;
                $clean = $1; 
    }
    elsif ($type eq "cidr")
    {
        $unclean =~ /^((\d{1,3}\.){3}\d{1,3}\/\d{1,2})$/;
                $clean = $1;
    }
    elsif ($type eq "rhks") 
    {
        $unclean =~ /^(fc[12]ks|rhel3ks|rh\d{1,2}ks|rh72esm)$/;
        $clean = $1; 
    }
    elsif ($type eq "yorn") 
    {
        $unclean =~ /^(yes|no)$/;
        $clean = $1; 
    }
    elsif ($type eq "digits") 
    {
        $unclean =~ /^(\d+)$/;
        $clean = $1; 
    }
    elsif ($type eq "words") 
    {
        $unclean =~ /^(\w+)$/;
        $clean = $1; }
    elsif ($type eq "any") 
    {
        $unclean =~ /^(.*)$/;
        $clean = $1; 
    }

    return $clean;

}

#------------------------------------------------------------------------------------
# register()
#
#   parameters
#       $mobj       :   MACFun object to register
#
#   return value
#       hash reference to to keys
#           SUCCESS :   1 (success), 0 (failed)
#           MESSAGE :   String containing error from function
#
#   Overview
#       This function is designed to take a MACFun object and post it to the admin
#       server.  Though provisioning is the most prominent process that uses this 
#       function, it is being included in this package instead of the provisioning
#       package as this function is also used to register rapid reboot, audits, etc.
#
#------------------------------------------------------------------------------------
sub register {

    my $mobj = shift();
    my $action = shift();
    my $return = { success => 0 , message => "" };
    my $posturl; 
    my $postdata = {};
    my $expect;

    # Basic check to see if we got all of our parameters
    if (!$mobj)
    {
        logks("ERR","register(): Recieved empty parameter list");
        $return = { success => 1 , message => "No parameters" };
    }
    elsif (!$action)
    {
        logks("ERR","register(): Required parameter (action) missing");
        $return = { success => 1 , message => "Missing parameter" };
    }

    if ($return->{success})
    {
        return $return;
    }

    # For each case, set up the url that we are going to post to.  Also, sets the "expected"
    # return from the post call
    if ($action eq "new") 
    {
        $posturl = $Config->{pit_baseurl}."/register_server.php";
        $postdata = $mobj->hardware();
        $postdata->{macaddr} = $mobj->macaddr();
        $postdata->{ipaddr} = $mobj->ipaddr();
        $postdata->{status} = "ready";
        $expect = "It worked";
    }
    elsif ($action eq "failed") 
    {
        $posturl = $Config->{pit_baseurl}."/register_failed_server.php";
        $postdata->{macaddr} = $mobj->macaddr();
    }
    elsif ($action eq "kickstart") 
    {
        $posturl = $Config->{pit_baseurl}."/register_kickstart.php";
        $postdata->{macaddr} = $mobj->macaddr();
        $postdata->{ipaddr} = $mobj->ipaddr();
        $postdata->{status} = "kickstarted";
        $expect = "It worked";
    }
    elsif ($action eq "rescue") 
    {
        $posturl = $Config->{pit_baseurl}."/register_rescue.php";
        $postdata->{macaddr} = $mobj->macaddr();
        $postdata->{ipaddr} = $mobj->ipaddr();
    }
    elsif ($action eq "unknown") 
    {
        $posturl = $Config->{pit_baseurl}."/register_unknown_server.php";
        $postdata->{macaddr} = $mobj->macaddr();
    }

    # Make the post call
    my $postres = lwpfetch($posturl, $postdata, undef, undef);
    
    # Did we get a successful result?
    if ($postres->[0]) 
    {
        # Did we set an expected message?
        if ($expect) 
        {
            # Does the returned message match what we expected?
            if ($postres->[1] =~ /$expect/) 
            {
                # If it matches, the result was a success
                $return = { success => 1, message => "Success" }
            }
            else 
            {
                # If is doesn't match, it must have failed
                logks("WARNING", $mobj->macaddr()." register_$action failed: $postres->[1]");
                $return = { success => 0, message => $postres->[1] }
            }
        }
        else 
        {
            # If we didn't set an expected message and lwpfetch returned success
            $return = { success => 1, message => "Success" }
        }
    }
    else 
    {
        # We don't know if we got a result back from lwpfetch (an undefined $postres will get us
        # here) so we need to ensure we got a message back before we try and return the error
        if ($postres->[1])
        {
            $return = { success => 0, message => $postres->[1] }
        }
        else
        {
            $return = { success => 0, message => "Failed" }
        }
    }

    return $return;
}


#------------------------------------------------------------------------------------
# getErrorMessages()
#
#   parameters
#       $maclist    :   list of mac address to get error message for
#
#   return value
#       array of hash references
#           mac_address     :   mac of server
#           date_added      :   date server (maybe error message) was added
#           error_message   :   last error message saved for server
#
#   Overview
#       This function takes a list of mac addresses and then queries the database
#       to determine the last error message about the server in the database
#
#------------------------------------------------------------------------------------
sub getErrorMessages 
{
    my $maclist = shift();
    my $return = [];
    my $ksdbh = ks_dbConnect();

    if (!$ksdbh)
    {
        return $return;
    }

    my $select = $ksdbh->prepare("SELECT mac_address, error_message, t2.date_added FROM mac_list t1, macid_error_history t2 WHERE t1.mac_address = ? AND t2.mac_list_id = t1.id ORDER BY date_added DESC LIMIT 1");
        
    foreach my $macAddress (@$maclist) 
    {
        my $row = $ksdbh->selectrow_hashref($select, undef, $macAddress);
        if (DBI->err)
        {
            logks("ERR","getErrorMessages(): Database connection error");
            last;
        }

        ($row->{mac_address}) || ($row->{mac_address} = $macAddress);
        ($row->{date_added}) || ($row->{date_added} = "2001-01-01 00:00:00+00");
        if ($row->{error_message}) 
        {
            $row->{error_message} =~ s/<.*?>//g;
            $row->{error_message} =~ s/\r|\n//g;
        }
        else 
        {
            $row->{error_message} = "None logged";
        }
        push @$return, $row;
    }

    $ksdbh->disconnect();

    return $return;
}


#------------------------------------------------------------------------------------
# daemonize
#
#   parameters
#       none
#
#   return value
#       success :   1
#       failure :   undef
#       
#
#   Overview
#       Forks off a child process and exits.  Used to create daemon processes
#
#------------------------------------------------------------------------------------

sub daemonize
{
    my $pid;

    # Need to check if $pid is defined as fork returns undef on error
    if (defined($pid = fork))
    {
        # If $pid != 0 then we are the parent and we want to exit
        exit(0) if $pid;
    }
    else
    {
        # We get here if fork failed
        logks("ERR","daemonize(): Unable to fork");
        return undef;
    }

    # Proper procedure for creating daemon process
    chdir "/";

    # Create a new session   
    if (!setsid())
    {
        logks("ERR", "Unable to detach from terminal");
        return undef;
    }

    close(STDIN);
    close(STDOUT);
    close(STDERR);

    open(STDIN,  "+>/dev/null");
    open(STDOUT, "+>/dev/null");
    open(STDERR, "+>/dev/null");

    return 1;
    
}




1
