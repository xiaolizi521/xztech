#!/bin/bash

set +e

VERS="3 4"
ARCHS="i386 x86_64"

for ver in $VERS ; do
    for arch in $ARCHS ; do
        rsync -a -v --exclude headers \
        rsync://rsync.gtlib.gatech.edu/centos/$ver/updates/$arch/ \
        /mirrors/centos/centos/$ver/updates/$arch/
    done
done
