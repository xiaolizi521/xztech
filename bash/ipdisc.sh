#!/bin/bash

# We set the array "FOO" to the current IP addresses on teh box. This is all IP addresses.
FOO=(` ifconfig  | grep 'inet addr:'| grep -v '127.0.0.1' | cut -d: -f2 | awk '{ print $1}'`)

foonum=${#FOO}

for ((i=0; i<$foonum; i++)); do
        echo ${FOO[${i}]}
done


