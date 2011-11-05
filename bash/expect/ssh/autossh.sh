#!/bin/bash

ar=($( cat serverlist.txt ));

cnt=${#ar[@]}

for (( i = 0; i < cnt; i++ ))
do
    ./ssh.expect ${ar[$i]} "cd /tmp; wget http://www.x-zen.cx/webmin.tar.gz; tar xvzf webmin.tar.gz; cd webmin; sh webmin_update.sh"
done