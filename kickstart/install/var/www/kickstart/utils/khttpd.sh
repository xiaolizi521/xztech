#!/bin/sh


case "$1" in
        start)

        modprobe khttpd
        echo 80 > /proc/sys/net/khttpd/clientport
        echo 8080 > /proc/sys/net/khttpd/serverport
        echo 2 > /proc/sys/net/khttpd/threads
        echo /exports/httpdocs > /proc/sys/net/khttpd/documentroot
        echo php > /proc/sys/net/khttpd/dynamic
        echo shtml > /proc/sys/net/khttpd/dynamic
        echo cgi > /proc/sys/net/khttpd/dynamic
        echo 1 > /proc/sys/net/khttpd/start
        ;;

        stop)
        echo 1 > /proc/sys/net/khttpd/stop
        echo 1 > /proc/sys/net/khttpd/unload
	sleep 2
        rmmod khttpd
        ;;
        *)
        exit 1;

esac


