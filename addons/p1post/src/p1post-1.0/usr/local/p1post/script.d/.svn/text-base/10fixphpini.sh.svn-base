#!/bin/bash

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

. /usr/local/p1post/lib/p1ks_lib.sh

[ -f /etc/php.ini ] || exit 0

perl -ne '$_ =~ s/^(short_open_tag|register_globals).*/$1 = On/; print $_;' \
	</etc/php.ini >/tmp/php.ini.$$

if [ -s /tmp/php.ini.$$ ] ; then
	mv -f /tmp/php.ini.$$ /etc/php.ini
	postlog "INFO" "Modify php.ini complete"
else
	postlog "FATAL" "Modify php.ini failed"
fi

if [ -x /sbin/service ] ; then
	service httpd restart
	httpd_res=$?
else
	apachectl restart
	httpd_res=$?
fi

if [ $httpd_res -eq 0 ] ; then
	postlog "INFO" "httpd restart complete"
else
	postlog "FATAL" "httpd restart failed"
fi

exit 0
