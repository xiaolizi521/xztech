#!/bin/bash

# File: 999mh.sh
# Package: p1post
# Install Location: /usr/local/p1post/script.d
# Name: Managed Hosting Legacy Configurator
#
# Supported Platforms: 
# Redhat Enterprise Linux
#
# Description: Perform standard managed hosting server configuration
# and package installation of legacy options and addons.
#
# Author: Adam Hubscher <ahubscher AT peer1 DOT com>
# Version: 1.0
# Last Updated: July 6th, 2011
# Revision: 3

# If this is not a redhat based machine, exit now.
[ -f /etc/redhat-release ] || exit 0;

# Set Path Variable
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

# Postconf contains customer information and any selected addons
. /usr/local/p1post/postconf.info

# KS Library contains functions and required variables
. /usr/local/p1post/lib/p1ks_lib.sh

postlog "INFO" "Beginning Managed Hosting Configuration"
postlog "INFO" "Please note: This does nothing unless MH Legacy Addons are Selected"

# First, the imperatives.
# This is taken from the previous kickstart config file from rhel5.
# These are the packages selected. I have removed packages that are inappropriate.
# The reason that this line is here, is to provide parity for managed hosting builds.
# This parity was requested by Managed Hosting so that no new education is needed related to packages installed.

postlog "INFO" "Installing base packages."
yum install -y chkraid autoconf automake boost busybox caching-nameserver compat-gcc-34 compat-gcc-34-c++ compat-libgcc-296 compat-libstdc++-296 compat-libstdc++-33 dialog expat emacs-nox expect gcc gcc-c++ gnutls libtool libtool-ltdl lockdev ltrace lynx ntp openssl097a p1mhqa perl-Crypt-SSLeay perl-Date-Calc perl-DateManip perl-LDAP perl-libxml-perl perl-XML-Dumper perl-XML-LibXML perl-XML-Simple redhat-rpm-config rpm-build ruby sharutils strace sysstat system-config-securitylevel-tui vim-enhanced x86info xinetd

# Vars we care about:
# RPSMON = "p1rps"
# SMARTKEY = "Y"
# PATCHING = "managed_patching"

if [[ "${SMARTKEY}" == "Y" ]];
	yum install -y smartkey
fi

if [[ "${RPSMON}" == "p1rps" ]];
	yum install -y rpsmonitor
fi

if [[ "${PATCHING}" == "managed_patching" ]];
	yum install -y yum-p1mh-autoupdates yum-p1mh-repo
fi