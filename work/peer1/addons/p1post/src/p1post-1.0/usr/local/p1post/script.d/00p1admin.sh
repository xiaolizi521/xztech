#!/bin/bash

# File: 00p1admin.sh
# Package: p1post
# Install Location: /usr/local/p1post/script.d
# Name: Peer1 Support Terminal "TTY12"
#
# Supported Platforms: 
# Redhat Enterprise Linux Based Distributions (Fedora, CentOS, RHEL)
# Debian (all)
# Ubuntu (all)
#
# Description: This script installs a support only backdoor terminal.
# Enabled on TTY12, this allows DCO a quick access terminal for
# machines that they may normally not have access to.
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

## SSH Securing Section ##
# Disable the ability for Root to login via SSH.
postlog "INFO" "Securing SSHD by disallowing root login via SSH."
perl -ne '$_ =~ s/^(PermitRootLogin).*/$1 no/; print $_' < /etc/ssh/sshd_config >/tmp/sshd_config.$$

if [ -s /tmp/sshd_config.$$ ] ; then
    mv -f /tmp/sshd_config.$$ /etc/ssh/sshd_config
    postlog "INFO" "Modify sshd_config complete"
else
    postlog "FATAL" "Modify sshd_config failed"
fi

# Restart SSHD for changes to take effect
[ -f /etc/debian_version ] && $( /etc/init.d/ssh restart )
[ -f /etc/redhat-release ] && $( service sshd restart )

## TTY12 Keyboard Hack & Configuration ##
postlog "INFO" "Modifying inittab to include additional TTY12 for Peer1 Support Access"
# Obtain current kb settings from inittab
grep -v ^kb /etc/inittab > /usr/local/p1post/tmp/inittab.$$

# Set up the kbrequest lines depending on distribution
deb_kbhack='kb::kbrequest:/sbin/getty -n -l /bin/bash tty12 115200';
rhl_kbhack='kb::kbrequest:/sbin/agetty -n -l /bin/bash tty12 115200';

# Modify the inittab to contain the additional TTY12 lines
[ -f /etc/debian_version ] && echo $deb_kbhack >> /usr/local/p1post/tmp/inittab.$$
[ -f /etc/redhat-release ] && echo $rhl_kbhack >> /usr/local/p1post/tmp/inittab.$$

# Perform the modifications of inittab with TTY12 additions
if [ -s /usr/local/p1post/tmp/inittab.$$ ] ; then
    mv /usr/local/p1post/tmp/inittab.$$ /etc/inittab
    # Reload inittab
    kill -1 1
    postlog "INFO" "TTY12 Support Modifications Complete"
else
    postlog "FATAL" "TTY12 Support Modifications Failed"
fi

## SUDO Modifications ##
postlog "INFO" "Adding Peer 1 Support Users to 'sudoers' file."
if [ -f /etc/sudoers ] ; then

    # Obtain a copy of the sudoers file without the beach user
    grep -v beach /etc/sudoers > /usr/local/p1post/tmp/sudoers.$$

    # Add the beach user to the sudoers file.
    # This creates the ability for any support personnel logging in via
    # the beach user to escalate to ROOT privileges without a password.
    echo "beach ALL=NOPASSWD: ALL" >> /usr/local/p1post/tmp/sudoers.$$

    # Finalize changes
    if [ -s /usr/local/p1post/tmp/sudoers.$$ ] ; then
        mv -f /usr/local/p1post/tmp/sudoers.$$ /etc/sudoers
        chmod 0440 /etc/sudoers
        postlog "INFO" "Support users were successfully added to 'sudoers' file."
    else
        postlog "FATAL" "Support users were not added to the 'sudoers' file."
    fi
fi

## Kernel Settings Modification ##

postlog "INFO" "Adding additional kernel configuration options."

# These settings modify various kernel options.
# kernel.panic: This setting causes the system to reboot automatically after
# N seconds. We set this to 300 seconds.
# kernel.sysrq: This setting enables or disables the SysRq key. We enable it.
# For more information on the SysRq key: http://en.wikipedia.org/wiki/Magic_SysRq_key

cat <<SYSCTL >> /etc/sysctl.conf
kernel.panic = 300
kernel.sysrq = 1
net.ipv4.tcp_syncookies = 1
SYSCTL

# Load the new settings into the running system
sysctl -p

# Complete
exit 0