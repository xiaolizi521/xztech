#!/usr/local/bin/bash

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

. /usr/local/sbpost/postconf.info
. /usr/local/sbpost/lib/sbks_lib.sh

# Add beach to sudoers
if [ -f /usr/local/etc/sudoers ] ; then
	grep -v beach /usr/local/etc/sudoers > ${sbpost}/tmp/sudoers.$$
	echo "beach ALL=NOPASSWD: ALL" >> ${sbpost}/tmp/sudoers.$$
	echo "$PUSER ALL=(ALL) ALL" >> ${sbpost}/tmp/sudoers.$$
	if [ -s ${sbpost}/tmp/sudoers.$$ ] ; then
		mv -f ${sbpost}/tmp/sudoers.$$ /usr/local/etc/sudoers
		chmod 0440 /usr/local/etc/sudoers
		echo "INFO" "Modify sudoers complete"
	else
		echo "FATAL" "Modify sudoers failed"
	fi
fi

exit 0
