#!/bin/bash

cd /exports/kickstart/utils

./centos_updates_mirror.sh
#./cpupdate.pl
./ensim_mirror.sh
./fedora_mirror.sh
# Don't really need to mirror the whole tree, it never changes
#./redhat_mirror.sh
#./redhat_updates_mirror.sh
