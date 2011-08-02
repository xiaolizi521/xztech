#!/bin/bash

# File: 11firewall.sh
# Package: p1post
# Install Location: /usr/local/p1post/script.d
# Name: Firewall Configuration
#
# Supported Platforms: 
# Redhat Enterprise Linux Based Distributions (Fedora, CentOS, RHEL)
#
# Description: This script installs the default firewall rules for
# Peer1 hosting. This script acts as a method to avoid having the
# installed iptables rule file be deleted when the rpm is erased
# following completion of the post installation process.
#
# Author: Adam Hubscher <ahubscher AT peer1 DOT com>
# Version: 1.0
# Last Updated: July 6th, 2011
# Revision: 3

# Set Path Variable
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

# Postconf contains customer information and any selected addons
. /usr/local/p1post/postconf.info

# KS Library contains functions and required variables
. /usr/local/p1post/lib/p1ks_lib.sh

# Remove the current iptables rules, whatever they are
if [ -f "/etc/sysconfig/iptables" ];
    rm -f "/etc/sysconfig/iptables"
fi

# Install the iptables file from the distribution
install -m 0755 /usr/local/p1post/files/iptables /etc/sysconfig/iptables

# Restart the iptables service to accept the rules
service iptables restart

exit 0