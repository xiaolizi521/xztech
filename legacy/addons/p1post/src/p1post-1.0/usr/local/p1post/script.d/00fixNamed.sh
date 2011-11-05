#!/bin/bash

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin

. /usr/local/p1post/postconf.info
. /usr/local/p1post/lib/p1ks_lib.sh

[ -f /etc/redhat-release ] || exit 0;

grep "unset ROOTDIR" /etc/sysconfig/named >/dev/null && exit 0

postlog "INFO" "Fixing named ROOTDIR"

cat <<FOO >>/etc/sysconfig/named
# ROOTDIR breaks 'named' by default, especially with cPanel
unset ROOTDIR
FOO

/etc/init.d/named restart && exit 0

postlog "INFO" "Fixed ROOTDIR, named restart OK"

exit 0
