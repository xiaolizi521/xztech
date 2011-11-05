#!/bin/bash

case "$1" in

dhcp)
    tail -F /exports/kickstart/logs/daemon.log | /exports/kickstart/bin/trigger_dhcp
    ;;

tftp)
    tail -F /var/log/syslog | /exports/kickstart/bin/trigger_tftp
    ;;

*)
    echo "Usage: start_triggers.sh [tftp|dhcp]"
    ;;
esac
