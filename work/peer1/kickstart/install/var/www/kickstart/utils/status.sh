#!/bin/bash

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin:/exports/kickstart/bin

notify() {
	service=$1
	cat <<NOTE | /usr/sbin/sendmail -t
From: status@ks01.iad01.kslan.serverbeach.com
To: page.hpugsley@serverbeach.com
Subject: DOWN - $service

$service is down
NOTE

}

#dpid=`pidof dhcpd`
#[ -z $dpid ] && /sbin/service dhcpd start > /dev/null

smbd_pid=`pidof -s smbd`
nmbd_pid=`pidof -s nmbd`
if [ -z $smbd_pid ] || [ -z $nmbd_pid ] ; then
	/etc/init.d/samba stop
	/etc/init.d/samba start
	if [ $? -ne 0 ] ; then notify samba ; fi
fi

exit 0

HTTPD=`/sbin/service httpd status`
if [ "x$HTTPD" == "xhttpd dead but pid file exists" ] || [ "x$HTTPD" == "xhttpd is stopped" ] ; then
	/sbin/service httpd stop
	/sbin/service httpd start
	if [ $? -ne 0 ] ; then notify httpd ; fi
fi

PSQL=`/sbin/service postgresql status`
if [ "x$PSQL" == "postmaster dead but pid file exists" ] || [ "x$PSQL" == "xpostmaster is stopped" ] ; then
	/sbin/service postgresql stop
	/sbin/service postgresql start
	if [ $? -ne 0 ] ; then notify postgresql ; fi
fi

/usr/sbin/rndc status > /dev/null
if [ $? -ne 0 ] ; then
	/sbin/service named stop
	/sbin/service named start
	if [ $? -ne 0 ] ; then notify named ; fi
fi

