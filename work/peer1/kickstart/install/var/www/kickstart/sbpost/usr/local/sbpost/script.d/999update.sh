#!/bin/bash
# Script to update the system packages
# jbair@2009-3-20 - Added support for Debian.
#       
# Adam Hubscher - 6/7/2010, removed RHN registration and placed at earlier portion of SBPOST.
#       
# Fix for HYBFUS-1935
        
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin
        
. /usr/local/sbpost/postconf.info
. /usr/local/sbpost/lib/config.sh
. /usr/local/sbpost/lib/sbks_lib.sh

# Red Hat
if [ -s /etc/redhat-release ]; then

        postlog "INFO" "Updating packages for Red Hat/CentOS"

        while [ -e /var/run/yum.pid ]; do
                echo "Another copy of yum is running. Waiting..."
                postlog "INFO" "Another copy of yum is running. Waiting..."
                sleep 60
        done
                        
        startupdate="yum update --exclude kernel* -y"
        
        if [ $product == "RHELES" ] ; then
                key="RPM-GPG-KEY"
                startupdate="up2date --nox --update --install"
        elif [ $product == "RHELS" ] ; then
                key="RHNS-CA-CERT"
        fi
                
        $startupdate
        
        postlog "INFO" "Package update for Red Hat/CentOS completed."

        exit 0
# Debian 
elif [ -s /etc/debian_version ]; then
        postlog "INFO" "Updating packages for Debian."
        export DEBIAN_FRONTEND=noninteractive
        export DEBIAN_PRIORITY=critical
        export DEBCONF_NOWARNINGS=yes
        apt-get update
        apt-get upgrade -y
        postlog "INFO" "Package updates for Debian completed."
        exit 0
fi
