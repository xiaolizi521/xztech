#!/bin/bash
#
# Script to Register RHEL machines with RHN
#
# Fix for HYBFUS-1935
#
# 

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

. /usr/local/sbpost/postconf.info
. /usr/local/sbpost/lib/config.sh
. /usr/local/sbpost/lib/sbks_lib.sh

# Determine if this is a RHEL based box or not. Exit gracefully if not.
if [ -s /etc/redhat-release ]; then

        postlog "INFO" "Beginning Redhat Network Registration"

        # Check if YUM is running. Wait for it to finish if it is.
        while [ -e /var/run/yum.pid ]; do
                echo "Another copy of yum is running. Waiting..."
                postlog "INFO" "Another copy of yum is running. Waiting..."
                sleep 60
        done

        register=false

        # Check if this is a RHEL based product offering. CentOS need not be registered as it is a free product.
        if [ $product == "RHELES" ] ; then
                register=true
                key="RPM-GPG-KEY"
                startupdate="up2date --nox --update --install"
        elif [ $product == "RHELS" ] ; then
                register=true
                key="RHNS-CA-CERT"
        fi

        # If either of the two above occurred, then time to begin registration.
        if [ $register == "true" ] ; then
                cat <<CFG > /etc/sysconfig/rhncheck.cfg
VERBOSE=1
MAILTO=${PUSER}@localhost
CFG

                name="$customer_number-$server_number"
                proxy="--proxyUser foo --proxyPassword bar"
                /usr/sbin/rhnreg_ks --force $proxy --username $CFG_RHN_USER --password $CFG_RHN_PASS --profilename $name

                rpm --import /usr/share/rhn/$key
                postlog "INFO" "RHN registration complete"

        fi
fi

exit 0
