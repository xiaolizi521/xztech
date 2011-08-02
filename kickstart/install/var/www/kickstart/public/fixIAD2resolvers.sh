#!/bin/bash

resolvconf=/etc/resolv.conf

egrep '69.44.56.(86|102)' $resolvconf >/dev/null

if [ $? -ne 0 ] ; then echo SUCCESS ; exit 0 ; fi

sed -e "s/69.44.56.86/64.34.160.76/g; s/69.44.56.102/64.34.160.92/g" < $resolvconf > $resolvconf.new

if [ -s $resolvconf.new ] ; then
        mv $resolvconf.new $resolvconf
        echo SUCCESS
        exit 0
fi

echo FAILURE
exit 1

