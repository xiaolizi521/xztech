#!/bin/sh

cd /usr/src;

mkdir auditupdate;

cd auditupdate;

wget "http://cloud.github.com/downloads/offbeatadam/gvo/audit.tar.gz";

tar xvzf audit.tar.gz;

mv /etc/audit/auditd.conf /etc/audit/auditd.conf.bak;
mv /etc/audit/audit.rules /etc/audit/audit.rules.bak;

cp -f audit* /etc/audit;

/etc/init.d/auditd restart;

cd ..;
rm -rf auditupdate;
rm -rf audit.tar.gz;

exit;
