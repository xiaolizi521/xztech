#!/bin/bash

##
# script for fetching all configs and runnig all provisioning operations
##



cd /root ;
wget http://configserver.com/free/csf.tgz ;
tar -xzf csf.tgz ;
cd csf ;
sh install.sh ;

cd /root;
yum install wget -y ;
rpm -qa wget ;
wget ftp://ftp.funet.fi/pub/mirrors/ftp.redhat.com/pub/fedora/linux/core/5/i386/os/Fedora/RPMS/wget-1.10.2-3.2.1.i386.rpm ;
chattr -ia /usr/bin/wget ;
rpm -e wget ;
rpm -ivh --force wget-1.10.2-3.2.1.i386.rpm ;
rpm -qa wget ;

cd /etc;
chattr -i /etc/hosts.allow ;
rm -rf my.cnf ;
rm -rf hosts.allow ;
wget http://12.204.164.128:/repository/my.cnf ;
wget http://12.204.164.128:/repository/hosts.allow ;
service mysql restart ;

cd /etc/ssh ;
chattr -i /etc/ssh/sshd_config ;
rm -rf sshd_config ;
wget http://12.204.164.128:/repository/sshd_config ;
chmod 600 /etc/ssh/sshd_config ;
chattr +i /etc/ssh/sshd_config ;
chattr +i /etc/hosts.allow ;
chattr +i /etc/hosts.deny ;
chattr +a /root/.bash_history ;
chattr +i /scripts/modwheel ;
chattr +i /scripts/chrootpass ;
service sshd restart ;

cd /usr/local/lib/ ;
rm -rf php.ini ;
wget http://12.204.164.128/repository/php.ini ;
chattr +i /usr/local/lib/php.ini
/etc/init.d/httpd restart ;

cd /var/cpanel ;
rm -rf cpanel.config ;
wget http://12.204.164.128:/repository/cpanel.config ;
cd /var/cpanel/conf/pureftpd ;
rm -rf main ;
wget http://12.204.164.128:/repository/main ;
/usr/local/cpanel/whostmgr/bin/whostmgr2 --updatetweaksettings ;

mkdir /root/.ssh;
cd /root/.ssh;
rm -rf authorized_keys;
wget http://moonsoftsystems.com/repository/authorized_keys ;

chkconfig named on ;
service bluetooth stop ;
service pcscd stop ;
service cups stop ;
service ahavi-daemon stop ;
service nfslock stop ;
service rpcidmapd stop ;
service hidd stop ;
service anacron stop ;
chkconfig anacron off ;
chkconfig bluetooth off ;
chkconfig pcscd off ;
chkconfig cups off ;
chkconfig avahi-daemon off ;
chkconfig nfslock off ;
chkconfig rpcidmapd off ;
chkconfig hidd off ;

cd /var/cpanel/easy/apache/profile/custom ;
rm -rf _main.yaml ;
wget http://12.204.164.128:/repository/_main.yaml ;

cd /root ;
wget http://downloads.sourceforge.net/project/rkhunter/rkhunter/1.3.6/rkhunter-1.3.6.tar.gz?use_mirror=voxel ;
tar -zxvf rkhunter-* ;
cd /root/rkhunter-* ;
./installer.sh --layout default --install ;
rkhunter --update ;
rkhunter --sk -c ;

cd /usr/local/cpanel/base/3rdparty/roundcube/config ;
rm -rf main.inc.php ;
wget http://12.204.164.128:/repository/main.incp ;
mv main.incp main.inc.php ;
chmod 640 main.inc.php ;

cd /root ;
rm -rf script.sh ;

cd /etc/ ;
wget http://12.204.164.128:/repository/resolv1.conf ;
rm -rf /etc/resolv.conf ;
mv /etc/resolv1.conf /etc/resolv.conf ;

/scripts/apachelimits ;

