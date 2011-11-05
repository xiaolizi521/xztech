#!/bin/bash

# File: 20rhn.sh
# Package: p1post
# Install Location: /usr/local/p1post/script.d
# Name: RHN Registration
#
# Supported Platforms: 
# Redhat Enterprise Linux
#
# Description: Perform registration of Redhat Network.
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

# Set initial variables
register="false"

# Determine if this is a RHEL based box or not. Exit gracefully if not.
if [ -s /etc/redhat-release ]; then

    postlog "INFO" "Beginning Redhat Network Registration"

    # Check if YUM is running. Wait for it to finish if it is.
    while [ -e /var/run/yum.pid ]; do
        echo "Another copy of yum is running. Waiting..."
        postlog "INFO" "Another copy of yum is running. Waiting..."
        sleep 60
    done

    # Check if this is a RHEL based product offering. CentOS need not be registered as it is a free product.
    if [ $product == "RHELES" ] ; then
        register="true"
        key="RPM-GPG-KEY"
        startupdate="up2date --nox --update --install"
    elif [ $product == "RHELS" ] ; then
        register="true"
        key="RHNS-CA-CERT"
    fi

    # If either of the two above occurred, then time to begin registration.
    if [ $register == "true" ] ; then

##### BEGIN HEREDOC ######

        cat <<CFG > /etc/sysconfig/rhncheck.cfg
VERBOSE=1
MAILTO=${PUSER}@localhost
CFG

##### END HEREDOC   ######

        name="$customer_number-$server_number"
        proxy="--proxyUser foo --proxyPassword bar"
        /usr/sbin/rhnreg_ks --force $proxy --username $CFG_RHN_USER --password $CFG_RHN_PASS --profilename $name

        rpm --import /usr/share/rhn/$key
        postlog "INFO" "RHN registration complete"

    fi
fi

exit 0
