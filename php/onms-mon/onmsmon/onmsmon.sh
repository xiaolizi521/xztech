#!/bin/bash
#
# chkconfig: 345 99 01
# description: Starts and Stops the ONMSMONITOR daemon.
#

# This file is simply to start and stop the monitoring daemon.
# The monitoring daemon will then run in the background and make sure
# That the onmsmon.php file is always running.
# In the event onmsmon.php is not running, it will attempt to restart it automatically.

# Import the included init functions for SysV Init

. /etc/rc.d/init.d/functions

# If the log file doesn't exist, create it.
if [ -e /var/log/opennms/onmsmon.log ]

        then touch /var/log/opennms/onmsmon.log
fi

# Perform the start/stop/restart based on switched argument.

case "$1" in
        start)
                echo ""
                echo -n "Starting XMLRPC Monitoring Daemon.: "
                /opt/opennms/monitor >> /var/log/opennms/onmsmon.log &2>1
                echo_success
                echo ""
                ;;
        stop)
                echo ""
                echo -n "Stopping XMLRPC Monitorign Daemon.: "
                killproc /opt/opennms/monitor -SIGTERM
                echo_success
                echo ""
                ;;
        restart)
                echo ""
                echo -n "Stopping XMLRPC Monitoring Daemon.: "
                killproc /opt/opennms/monitor
                echo_success
                echo ""
                echo -n "Starting XMLRPC Monitoring Daemon.: "
                /opt/opennms/monitor >> /var/log/opennms/onmsmon.log &2>1
                echo_success
                echo ""
                ;;
esac