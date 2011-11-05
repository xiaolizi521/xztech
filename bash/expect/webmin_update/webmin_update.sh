#!/bin/bash

ar=($( cat serverlist.txt ));

cnt=${#ar[@]}

for (( i = 0; i < cnt; i++ ))
do
    ./webmin_update.expect
done

rm -rf /tmp/webmin