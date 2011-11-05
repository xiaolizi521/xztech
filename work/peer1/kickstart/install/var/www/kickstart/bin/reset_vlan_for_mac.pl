#!/usr/bin/perl -w
#=======================================================================
# Company:              Peer1
# Copyright(c):         Peer1 2008
# Project:              Kickstart
# Code Devloper:        Carlos Avila
# Creation Date:        06/18/2008
#
# File Type:            Script
# File Name:            reset_vlan_for_mac.pl
#
# Description:
# Reconfigure the switch port VLAN to 405 for a given MAC address. 
# The MAC must have been previously added to the kickstart system. 
# 
#=======================================================================


BEGIN {
        use lib qw(/exports/kickstart/lib);
        require 'sbks.pm';
}

use strict;

unless ( defined( $ARGV[0] ) ) { die "I need a MAC address" };

my $macaddr = $ARGV[0];

my $switchPortInfo = switchPortInfo($macaddr);
portControl($switchPortInfo, { speed => 100, vlan => 405 });


