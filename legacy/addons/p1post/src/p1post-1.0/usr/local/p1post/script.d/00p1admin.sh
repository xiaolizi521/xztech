#!/bin/bash

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

. /usr/local/p1post/postconf.info
. /usr/local/p1post/lib/p1ks_lib.sh

# Fix sshd 
perl -ne '$_ =~ s/^(PermitRootLogin).*/$1 no/; print $_' \
	</etc/ssh/sshd_config >/tmp/sshd_config.$$
if [ -s /tmp/sshd_config.$$ ] ; then
	mv -f /tmp/sshd_config.$$ /etc/ssh/sshd_config
	postlog "INFO" "Modify sshd_config complete"
else
	postlog "FATAL" "Modify sshd_config failed"
fi

# Keyboard hack
grep -v ^kb /etc/inittab > /usr/local/p1post/tmp/inittab.$$
deb_kbhack='kb::kbrequest:/sbin/getty -n -l /bin/bash tty12 115200';
rhl_kbhack='kb::kbrequest:/sbin/agetty -n -l /bin/bash tty12 115200';
[ -f /etc/debian_version ] && echo $deb_kbhack >> /usr/local/p1post/tmp/inittab.$$
[ -f /etc/redhat-release ] && echo $rhl_kbhack >> /usr/local/p1post/tmp/inittab.$$

if [ -s /usr/local/p1post/tmp/inittab.$$ ] ; then
	mv /usr/local/p1post/tmp/inittab.$$ /etc/inittab
	kill -1 1
	postlog "INFO" "Modify inittab complete"
else
	postlog "FATAL" "Modify inittab failed"
fi

# Add beach to sudoers
if [ -f /etc/sudoers ] ; then
	grep -v beach /etc/sudoers > /usr/local/p1post/tmp/sudoers.$$
	echo "beach ALL=NOPASSWD: ALL" >> /usr/local/p1post/tmp/sudoers.$$
	if [ -s /usr/local/p1post/tmp/sudoers.$$ ] ; then
		mv -f /usr/local/p1post/tmp/sudoers.$$ /etc/sudoers
		chmod 0440 /etc/sudoers
		postlog "INFO" "Modify sudoers complete"
	else
		postlog "FATAL" "Modify sudoers failed"
	fi
fi

cat <<SYSCTL >> /etc/sysctl.conf
kernel.panic = 300
kernel.sysrq = 1
net.ipv4.tcp_syncookies = 1
SYSCTL

exit 0
