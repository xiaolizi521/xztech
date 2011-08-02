#!/bin/bash

# File: 00fixNamed.sh
# Package: p1post
# Install Location: /usr/local/p1post/script.d
# Name: BIND9 Bug Fix(es)
#
# Supported Platforms: 
# Redhat Enterprise Linux
#
# Description: Perform any known resolutions for issues
# with BIND9/named.
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

# If this is not a redhat based machine, exit now.
[ -f /etc/redhat-release ] || exit 0;

# There is an issue if using the ROOTDIR flag.
postlog "INFO" "Fixing named ROOTDIR"

# Disable the ROOTDIR flag.
cat <<FOO >>/etc/sysconfig/named
# ROOTDIR breaks 'named' by default, especially with cPanel
unset ROOTDIR
FOO

# Restart named
/etc/init.d/named restart && exit 0

postlog "INFO" "Fixed ROOTDIR, named restart OK"

exit 0