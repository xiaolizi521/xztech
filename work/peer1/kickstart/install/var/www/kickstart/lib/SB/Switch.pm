#!/usr/bin/perl -w

# ===============================================================================================
# Company           :      Server Beach
# Copyright(c)      :      Server Beach 2007
# Project           :      Kickstart Sub-System
# Code Maintainer   :      SB Product Engineering
#
# File Type         :      Perl Module 
# File Name         :      Switch.pm
#
# Overview:
#   Perl module to be included in future kickstart perl scripts that need access to various
#   networking information (switches, ports, vlans, etc)
#
# Change Log:
#   2007-07-05 : Kevin Schwerdtfeger
#       Created
# ===============================================================================================

package SB::Switch;

BEGIN 
{

    use lib qw(/exports/kickstart/lib);
    use Exporter();
    our ($VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS);

    $VERSION = 1.00;
    @ISA = qw(Exporter);
    %EXPORT_TAGS = ( 'all' => [ qw(
        switchPortInfo
        portvlan
        portControl
        portstate
    ) ]);
    @EXPORT_OK = ( @{ $EXPORT_TAGS{'all'} } );
    @EXPORT = qw();

}

#############################
#   Standard perl modules   #
#############################
use strict;
use warnings;
use XML::Simple;

#############################
#    Serverbeach modules    #
#############################
use SB::Config;
use SB::Logger;
use SB::Common "lwpfetch";


$ENV{'PATH'} = "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/sbin:/usr/local/bin";
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

# ===============================================================================================
# Begin funciton definitions
# ===============================================================================================


#------------------------------------------------------------------------------------
# switchPortInfo()
#
#   parameters
#       $macAddress = mac address of the server
#
#   return value
#       success : returned values from switchPortInfo.php
#       failure : undef
#       
#   Overview
#       Frontend call to switchPortInfo.php
#
#------------------------------------------------------------------------------------

sub switchPortInfo {
    my $macAddress = shift();
    if (!$macAddress)
    {
        logks("ERR","switchPortInfo(): No MAC Address supplied to function");
        return undef;
    }
    my $return = {};
    my $postResult = lwpfetch($Config->{pit_baseurl}."/switchPortInfo.php","macAddress=$macAddress");
    if (($postResult->[0]) && (length($postResult->[0]) > 0)) 
    {
        my @lines = split(/\n/, $postResult->[1]);
        foreach my $line (@lines) 
        {
            my ($key, $value) = split(/=/, $line, 2);
            $return->{$key} = $value;
        }
    }
    else 
    {
        logks("ERR","$macAddress switchPortInfo(): switchPortInfo.php returned no information");
        $return = undef;
    }

    return $return;
}


#------------------------------------------------------------------------------------
# portvlan()
#
#   parameters
#       $switchPortInfo : hash such as the one returned from getSwitchPortInfo
#       $new_vlan       : id of new vlan to change port access to
#
#   return value
#       success : new vlan id
#       failure : 0
#
#   Overview
#       Used to update the vlan access of a particular port.  Front end call to
#       the vlan control cgi 
#
#------------------------------------------------------------------------------------

sub portvlan {

        my ($switchPortInfo, $new_vlan) = @_;
        my $return = 0;

        # create the port name in the form of DC:SWITCH:PORT
        my $portname = join(":",
            $switchPortInfo->{dc_abbr},
            $switchPortInfo->{switch},
            $switchPortInfo->{switch_port}
        );

        # Create a post string to post to the vlan control cgi
        my $postfmt = "dc_abbr=%s&switch_name=%s&change_ports=%s";
        my $postinfo = sprintf($postfmt,
            $switchPortInfo->{dc_abbr},
            $switchPortInfo->{switch},
            join(":", $switchPortInfo->{switch_port}, $new_vlan)
        );

        my $postres = lwpfetch($Config->{vlanctl_cgi}, $postinfo, undef, undef);

        if ($postres->[0] and $postres->[1] =~ /port(\d+)=vlan(\d+)/) 
        {
            my $real_vlan = $2;
            logks('info',"Port $portname VLAN -> $real_vlan (OK)");
            $return = $real_vlan;
        }
        else 
        {
            logks("INFO", "portvlan() got :".$postres->[1]);
            logks("ERR", "Port $portname VLAN -> $new_vlan (NO)");
            $return = 0;
        }

        return $return;
}



#------------------------------------------------------------------------------------
# portControl()
#
#   parameters
#
#   return value
#
#   Overview
#
#------------------------------------------------------------------------------------

sub portControl {
    my ($switchPortInfo, $changeInfo) = @_;

    my $switchName = $switchPortInfo->{switch};
    my $switchPort = $switchPortInfo->{switch_port};
    $changeInfo->{port} = $switchPort;

    my $switchControl =
    {
        auth => { username => 'sb_user', password => 'n3tw0rk' },
        switch => {
            name => $switchName,
            ports => [ $changeInfo ]
            },
        output => { xml => 1 }
    };

    my $portname = join(":", $switchPortInfo->{dc_abbr}, $switchPortInfo->{switch}, $switchPortInfo->{switch_port});
    my $postinfo = XMLout($switchControl);
    my $postres = lwpfetch($Config->{switchctl_cgi}, $postinfo, undef, undef);

    if ($postres->[0] && $postres->[1] ne "") 
    {
        my $xmlResult = XMLin($postres->[1]);
        my $info = $xmlResult->{switch}->{ports};
        if (defined($changeInfo->{speed})) 
        {
            #if ($changeInfo->{speed} != $info->{speed}) { return 0; }
            #logks("info", "$portname SPEED -> $info->{speed} (OK)");
        }
        #print Dumper($xmlResult);
    }
    else 
    { 
        return 0; 
    }
    
    return 1;
}



#------------------------------------------------------------------------------------
# portstate()
#
#   parameters
#
#   return value
#
#   Overview
#
#------------------------------------------------------------------------------------

sub portstate {
        my ($switchPortInfo, $new_state) = @_;
        my $return = 1;
        (($switchPortInfo) && ($new_state)) || return $return;

        my $portname = join(":", $switchPortInfo->{dc_abbr}, $switchPortInfo->{switch}, $switchPortInfo->{switch_port});

        my $postfmt = "dc_abbr=%s&switch=%s&port=%s&status=%s";
        my $postinfo = sprintf($postfmt, split(/:/, $portname), $new_state);
        my $postres = lwpfetch($Config->{'portctl_cgi'}, $postinfo, undef, undef);

        if ($postres->[0]) {
                logks('info',"portstate() $portname STATE -> $new_state (OK)");
                $return = 0;
        }
        else {
                logks('WARNING',"portstate() $portname STATE -> $new_state (NO)");
        }

        return $return;
}

1
