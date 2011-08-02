#!/bin/bash

#mirror="rsync://speakeasy.rpmfind.net/linux/redhat"
mirror="rsync://mirrors.kernel.org/mirrors/redhat/redhat/linux"
target="/mirrors/redhat/redhat/linux"
rflags="-av --delete"
exclude="/tmp/updates.exclude"

cat <<EXCL > ${exclude}
.in
.mirror
core$
MIRROR.LOG
.notar
.message
.cache
.zipped
lost+found
Network Trash Folder
*.i586.rpm
*.ia64.rpm
*.s390.rpm
*.src.rpm
EXCL
# rsync -av --delete rsync://mirrors.kernel.org/mirrors/redhat/redhat/linux/7.2/en/os/i386/images/ /mirrors/redhat/redhat/linux/7.2/en/os/i386/images/

for VER in 7.2 7.3 8.0 9 ; do
	rsync ${rflags} "${mirror}/${VER}/en/os/i386/RedHat/" \
		"${target}/${VER}/en/os/i386/RedHat/"
	rsync ${rflags} "${mirror}/${VER}/en/os/i386/images/" \
		"${target}/${VER}/en/os/i386/images/"
	rsync ${rflags} --exclude-from=${exclude} \
		"${mirror}/updates/${VER}/" \
		"${target}/updates/${VER}/"
done

rm -f ${exclude}
