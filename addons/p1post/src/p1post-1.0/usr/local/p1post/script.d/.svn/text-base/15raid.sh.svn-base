#!/bin/bash

# Name: 3ware Raid Performance Configuration
# File: 15raid.sh
# Package: p1post
# Install Location: /usr/local/p1post/script.d
#
#
# Description: 
# This script attempts to see if a 3ware RAID card is being used.
# If one is found, it will add a config line to the startup scripts that will increase
# the disk read performance. 
# For reference see: http://www.3ware.com/kb/article.aspx?id=11050

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

# Use lspci to check to see if a 3ware card is installed (id for 3ware is 13c1)
postlog "INFO" "Scanning for installed 3ware RAID controller"

lspci -n | grep 13c1

if [ $? -eq 0 ] ; then
    postlog "INFO" "Adjusting read ahead value to improve 3ware RAID performance"
    blockdev --setra 16384 /dev/sda

    if [ -f /etc/redhat-release ] ; then
        postlog "INFO" "Adding changes to rc.local"
        echo "blockdev --setra 16384 /dev/sda" >> /etc/rc.d/rc.local
    elif [ -f /etc/debian_version ] ; then
        postlog "INFO" "Creating init script for changes"

##### BEGIN HEREDOC ######

cat << DONE > "/etc/init.d/3wareRAID.sh"

lspci -n | grep 13c1
if [ $? -eq 0 ] ; then
    blockdev --setra 16384 /dev/sda
fi

DONE

##### END HEREDOC    ######

        update-rc.d 3wareRAID.sh start 95 S .
        chmod +x /etc/init.d/3wareRAID.sh
    fi

    postlog "INFO" "3ware performance tuning complete"
    
else
    postlog "INFO" "No 3ware card installed"
fi

exit 0