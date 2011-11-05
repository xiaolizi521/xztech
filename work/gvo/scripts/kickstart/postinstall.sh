#!/bin/bash

# Be sure we have updated fully
yum -t -y update

# Lets grab the cpanel install and put it in the appropriate location
wget http://httpupdate.cpanel.net/initinstall.static
chmod 700 initinstall.static
mv initinstall.static /usr/sbin/install-cpanel

# And now the cpanel install init script

wget http://12.204.164.247/install-cpanel
mv install-cpanel /etc/init.d/install-cpanel
chmod 700 /etc/init.d/install-cpanel

# Now lets be sure that its set to run on startup
/sbin/chkconfig --add install-cpanel
/sbin/chkconfig --level 2345 install-cpanel on
