#!/bin/bash

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

sbpost="/usr/local/sbpost"
tmpdir="${sbpost}/cpanel"
. /boot/.serverbeach
. ${sbpost}/postconf.info
. "${sbpost}/lib/sbks_lib.sh"

if [[ -e /etc/redhat-release ]] ; then
    postlog "INFO" "Removing bind-chroot package for redhat as well as mod_auth_mysql"
    rpm -ev bind-chroot
    rpm -ev mod_auth_mysql
fi

cd ${tmpdir}

cp /etc/resolv.conf /etc/resolv.conf.cpanel

touch /etc/melangedisable

[ -s installd ] && rm -rf installd
postlog "INFO" "Getting latest version of CPanel"
fetch "http://httpupdate.cpanel.net/latest" "" "${tmpdir}/latest"
if [ ! -e "${tmpdir}/latest" ] ; then
    wget -O "${tmpdir}/latest" "http://httpupdate.cpanel.net/latest"
fi
chmod +x latest

./latest

# Check that cPanel is installed.
#
# This is a fix for HYBFUS-1795
#
# This fix will provide an appropriate exit status to allow
# SBPost to complete, while verifying the installation of
# cPanel.
#
# Adam Hubscher - 5/25/2010 [ahubscher "AT" peer1 "DOT" com] 

# Check that all required ports for cPanel operation are up and running

# 2082, 2086 - Non-SSL cPanel & WHM Ports
# 2083, 2087 - SSL cPanel & WHM Ports
# 80, 25, 21, 53, 3306 - HTTPD, SMTP, FTP, DNS,MySQL respectively, installed by cPanel

PORTS=(2082 2083 2086 2087 80 25 21 53 3306)

for i in ${PORTS[@]}; do

    nc -z -w 1 localhost $i
    ptest=$(echo $?)

    if [ $ptest -ne 0 ]; then
	postlog "INFO" "Port ${i} failed to be checked properly."
    fi
	postlog "INFO" "Port ${i} passed checks."
done

postlog "INFO" "Finished processing PORTS with status of $(ptest)"

postlog "INFO" "Beginning verify of PID."

# Verify that the cpsrvd PID is running

PID_FILE="/var/run/cpsrvd.pid"

if [ -f "$PID_FILE" ]; then
    CPPID=$( head -n 1 "$PID_FILE" )
    RUNNING=$( ps -p ${CPPID} | grep ${CPPID} )

    if [ -z "$RUNNING" ]; then
        postlog "FATAL" "PID File exists but ${CPPID} is not running..."
        exit 1
    else
        postlog "INFO" "cPanel is running. Installation successful."
    fi
else
    postlog "FATAL" "${PID_FILE} does not exist. cPanel install failed."
    exit 1
fi

postlog "INFO" "Finised installing CPanel with exit status $cpexit"

# END HYBFUS-1795 FIX - AHUBSCHER "AT" PEER1 "DOT" COM - 5/25/2010

mv -f /etc/resolv.conf.cpanel /etc/resolv.conf
chmod 4111 /usr/bin/sudo
chmod 0440 /etc/sudoers


/usr/sbin/rndc-confgen -a

postlog "INFO" "Running fixrndc"

/scripts/fixrndc -fv

postlog "INFO" "Checking for update script"
[ -d /usr/local/cpanel ] || exit 1


postlog "INFO" "Setting SSL to always on"
grep -v "alwaysredirecttossl" /var/cpanel/cpanel.config > /tmp/cpanel.config.$$
echo "alwaysredirecttossl=1" >> /tmp/cpanel.config.$$
mv /tmp/cpanel.config.$$ /var/cpanel/cpanel.config


# echo a newline to the end of the network file 
echo >> /etc/sysconfig/network

postlog "INFO" "Restarting CPanel"
/etc/init.d/cpanel restart

/etc/init.d/sbadm restart

exit 0
