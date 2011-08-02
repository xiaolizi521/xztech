#!/bin/bash

/exports/kickstart/bin/vconf.pl
/exports/kickstart/bin/dhcpconf.pl -f
/etc/init.d/dhcp3-server restart
