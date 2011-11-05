#!/bin/csh
# fetch http://kickstart/kickstart/kscfg/freebsd_zfs.csh
# csh freebsd_zfs.csh

set KSIPADDR=`netstat -rnf inet | grep default | awk '{print $2}'`
set FREEBSD_RELEASE="8.2-RELEASE-amd64.tar.xz"

set PING_KICKSTART=`ping -o -c 10 ${KSIPADDR} < /dev/null >& /dev/null ; echo $?`
if ( $PING_KICKSTART != 0 ) then
   echo "Cannot ping the kickstart server. Make sure this server is on the provisioning VLAN (405) and has a DHCP ip"
   exit 1;
endif

pkg_add -r curl > /dev/null
rehash > /dev/null

# Find the device ID for the ethernet interface
# This can be a number of different /dev names on FreeBSD
set MSG=`dmesg | awk ' /Ethernet address/ { print $1 }' | sed 's/.\{1\}$//'`

foreach i ( $MSG )
    if ( `ifconfig $i | grep -c 'status: active'` == 1 ) then
       set IFACE=$i
       break
    endif
end

# Check to see if an IP is configured on the ethernet interface.
ifconfig $IFACE | grep "inet" | tail -1 > /dev/null
if ( $? == 0 ) then
   set MACADDR=`ifconfig $IFACE | grep ether | awk '{print $2}' | tr A-Z a-z`
endif

echo "Downloading FreeBSD distribution from kickstart: ${KSIPADDR}"
/usr/local/bin/curl --max-time 30 --output /tmp/${FREEBSD_RELEASE} http://${KSIPADDR}/installs/freebsd/${FREEBSD_RELEASE}
set DISK=`sysctl kern.disks | sed 's/^.* //'`
set DISK_SIZE=`dmesg | grep ${DISK} | grep MB`

echo "Erasing disk partitions on: $DISK of size: ${DISK_SIZE}"
/root/bin/destroygeom -d ${DISK} -p tank
if ( $? != 0 ) then
   echo "Could not erase disk: ${DISK} Please type: dd bs=8k if=/dev/zero of=/dev/${DISK}"
   echo "Manually to erase the entire drive and rerun this tool. This may take some time"
   exit 1
endif

# Create the ZFS filesytem and install FreeBSD to it. Create a 24GB swap partition
/root/bin/zfsinstall -d ${DISK} -t /tmp/${FREEBSD_RELEASE} -s 24G

# make sure the boot environment is able to get to the web and install packages.
cp /etc/resolv.conf /mnt/etc/resolv.conf
cat /etc/rc.conf.d/network >> /mnt/etc/rc.conf

echo "Installing packages needed by sbpost and sbadm"
chroot /mnt /usr/sbin/pkg_add -r bash curl cvsup-without-gui ntp openssl p5-Authen-PAM p5-Crypt-PasswdMD5 p5-Net-SSLeay p5-Text-Iconv p5-WWW-Curl p5-libwww perl rsync sudo wget
sync; sync; sync; sync;

echo "Fetching and running postinstall.txt"
/usr/local/bin/curl --max-time 30 --output /mnt/tmp/postinstall.txt --silent http://${KSIPADDR}/kickstart/postconf/freebsd/postinstall.txt
chmod +x /mnt/tmp/postinstall.txt
chroot /mnt /tmp/postinstall.txt

exit 0
