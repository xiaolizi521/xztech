#!/bin/bash

exclude="/tmp/rsync$$.exclude"

cat <<EXCL > $exclude
/apt/ensim/LWP/3.5.2/
/apt/ensim/LWP/3.5.10/
/apt/ensim/LWP/3.5.15/
/apt/ensim/LWP/3.5.16/
/apt/ensim/LWP/3.5.18/
+/apt/ensim/LWP/3.5.20/
/apt/ensim/OSI/7.3/
/apt/ensim/OSI/3.5.18/
+/apt/ensim/OSI/3.5.20/
/download/sxc/
/download/webppliance/
EXCL

rsync -e ssh -a -v --delete --delete-excluded --exclude-from=$exclude \
	"root@69.44.56.85:/mirrors/ftp2.ensim.com/" \
	"/exports/mirrors/ftp2.ensim.com/"

rm -f $exclude
