#!/bin/bash

# This is the script that will remove any index.php files that are owned by ROOT:ROOT.

if [ `stat -c %U "$1"` == root ];
then
    echo "$1";
    rm -f "$1";
fi
