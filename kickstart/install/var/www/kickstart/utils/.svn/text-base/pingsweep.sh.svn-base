#!/bin/bash

tmpfile=`mktemp /tmp/pingXXXXXX`

psql -U hpugsley sbks -t -c "SELECT private_network FROM vlan_map ORDER BY id" > $tmpfile

for block in `cat $tmpfile` ; do
	echo "Sweeping $block"
	fping -a -c1 -q -g $block 2>/dev/null
	sleep 5;
done

rm -f $tmpfile
