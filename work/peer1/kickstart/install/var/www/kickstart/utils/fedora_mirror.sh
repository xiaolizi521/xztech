#!/bin/bash

# Mirror List:
#  http://fedora.redhat.com/download/mirrors/fedora-core-3
#  http://fedora.redhat.com/download/mirrors/updates-released-fc3
#  http://fedora.redhat.com/download/mirrors/updates-testing-fc3

# Sync updates online
rsync -av \
    --exclude 'testing' \
    --exclude 'SRPMS' \
    --exclude '*.hdr' \
    --exclude 'repodata' \
    --delete --delete-excluded \
    --progress \
    rsync://mirror.hiwaay.net/fedora-linux-core-updates/ \
    /mirrors/fedora/fedora-linux-core-updates/

