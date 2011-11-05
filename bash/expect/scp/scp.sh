#!/bin/bash

ar=($( cat serverlist.txt ));

cnt=${#ar[@]}

for (( i = 0; i < cnt; i++ ))
do
    ./scp.expect web1.sh pe-local@${ar[$i]} /tmp
done