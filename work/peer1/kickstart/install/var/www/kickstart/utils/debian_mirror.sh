#!/bin/bash

debian_mirror="/mirrors/debian"
nonUS_mirror="/mirrors/debian-non-US"
security_mirror="/mirrors/debian-security"

#debmirror --nosource --passive --host=mirrors.kernel.org --root=:debian --method=rsync --dist=woody --arch=i386 --progress $debian_mirror
#debmirror --nosource --passive --host=non-us.debian.org --root=:debian-non-US --method=rsync --dist=woody/non-US --arch=i386 --progress $nonUS_mirror
#debmirror --nosource --passive --host=non-us.debian.org --root=:debian-security --method=rsync --dist=woody/updates --arch=i386 --progress $security_mirror

debmirror --nosource --passive --host=mirrors.kernel.org --root=debian --method=ftp --dist=woody --arch=i386 --progress $debian_mirror
debmirror --nosource --passive --host=non-us.debian.org --root=debian-non-US --method=ftp --dist=woody/non-US --arch=i386 --progress $nonUS_mirror
debmirror --nosource --passive --host=non-us.debian.org --root=debian-security --method=ftp --dist=woody/updates --arch=i386 --progress $security_mirror
