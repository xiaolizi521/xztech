#!/bin/bash


if [ -z $1 ]
then
	echo "This script will install opennms. Usage is as follows:"
	echo "install.sh [yes/no] [database.server.address]"
	echo "If you do not wish to run through the installation of OpenNMS, enter 'no'."
	echo "If you want the script to install OpenNMS for you as well, enter 'yes'."
	echo "Usage Example:"
	echo "install.sh yes rwdb1.iad1.corp.rackspace.com"
	exit;
else
	if [ "$1" == yes ]
	then
		echo "You have opted to install OpenNMS at this time."
		echo "You have selected server $2 as your database target."
	else
		echo "You have opted to not install OpenNMS at this time."
	fi
fi

echo "Installing IPLike - ignore 'Failed' errors"

rpm -Uvh iplike-1.0.6-1.i386.rpm

rpm -Uvh iplike-1.0.6-1.x86_64.rpm

echo "Installing JDK 64bit"
rpm -Uvh jdk-1_5_0_14-linux-amd64.rpm

echo "Installing core OpenNMS components"
rpm -Uvh jicmp-1.0.4-1.x86_64.rpm
rpm -Uvh opennms-core-1.3.9-1.noarch.rpm opennms-webapp-jetty-1.3.9-1.noarch.rpm

echo "Creating directories"
cd /opt/opennms/share
rm opennms
mv r* /var/opennms
cd ..
rm share/.readme
rmdir share
ln -s ../../var/opennms ./share

echo "Setting opennms Java Paramaters"
/opt/opennms/bin/runjava -s

echo "Setting Memory Variable in OpenNMS install"
sed -e "s/256m/6144m/" < /opt/opennms/bin/install > /opt/opennms/bin/install.new
mv /opt/opennms/bin/install /opt/opennms/bin/install.bak
mv /opt/opennms/bin/install.new /opt/opennms/bin/install
chmod +x /opt/opennms/bin/install

if [ "$2" == yes ]
then
	echo "Beginning Install"
	/opt/opennms/bin/install -dis -D jdbc:postgresql://$1/ -A p0stgres
else echo "Install has been skipped."
fi

echo "OpenNMS configuration files can be found in the contained etc directory."
echo "Do not forget to edit the following files:"
echo "opennms.properties - change any instance of localhost to appropriate DB server."
echo "opennms-server.xml - Change servername for appropriate poller name"
echo "opennms-datasources.xml - Change database url appropriately"
echo "These files will need to be changed appropriately for your server address."
echo "After editing, and when the opennms install has been completed, move all files in"
echo "./etc to /opt/opennms/etc overwriting any files that may exist."
echo "You are then able to start OpenNMS at any time."
echo "**** NOTE ****"
echo "This sets up the xmlrpcd to point to PRODUCTION CORE."
echo "This can be modified in xmlrcpd-configuration.xml"
echo "**** NOTE ****"