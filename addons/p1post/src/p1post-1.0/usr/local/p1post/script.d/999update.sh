#!/bin/bash

# File: 999update.sh
# Package: p1post
# Install Location: /usr/local/p1post/script.d
# Name: OS Updater
#
# Supported Platforms: 
# Redhat Enterprise Linux Based Distributions (Fedora, CentOS, RHEL)
# Debian (all)
# Ubuntu (all)
#
# Description: Perform OS updates following all other installation
# requirements.
#
# Author: Adam Hubscher <ahubscher AT peer1 DOT com>
# Version: 1.0
# Last Updated: N/A
# Revision: 1

# Set Path Variable
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

# Postconf contains customer information and any selected addons
. /usr/local/p1post/postconf.info

# KS Library contains functions and required variables
. /usr/local/p1post/lib/p1ks_lib.sh

postlog "INFO" "Performing Operating System Updates"

if [ -s /etc/redhat-release ]; then

    # Redhat Based Distributions
    while [ -e /var/run/yum.pid ]; do
        echo "Another copy of yum is running. Waiting..."
        postlog "INFO" "Another copy of yum is running. Waiting..."
        sleep 60
    done
        
    yum -y update --exclude kernel* 

elif [ -s /etc/debian_version ]; then

    # Debian Based Distribtions
    export DEBIAN_FRONTEND=noninteractive
    export DEBIAN_PRIORITY=critical
    export DEBCONF_NOWARNINGS=yes

    apt-get update
    apt-get -y upgrade

fi

postlog "INFO" "Operating System Updates Complete"