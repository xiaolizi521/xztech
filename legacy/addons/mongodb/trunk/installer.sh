#!/bin/bash

sbpost="/usr/local/sbpost"
. /usr/local/sbpost/lib/sbks_lib.sh
postlog "Running MongoDB Installer"


workdir="${sbpost}/mongo"
. /boot/.serverbeach
. ${sbpost}/postconf.info

cd ${workdir}

# kschwerdtfeger@03-13-2007
#   Making a couple of changes.  First, the script was checking to see what the os type was multiple times.
#   Not a big deal, but seemed kinda silly to me.  Secondly, there seems to be an error with debian building
#   the Net::SSLeay perl module so we are going to just pull the latest version from the web.


if [ -e "/etc/redhat-release" ] ; then
	#add to sources

	postlog "Adding Mongo Repository to yum"
	echo """[10gen]
name=10gen Repository
baseurl=http://downloads.mongodb.org/distros/centos/5.4/os/x86_64/
gpgcheck=0""" > /etc/yum.repos.d/mongodb.repo
	
	postlog "Installing MongoDB"
	#install mongo
	postlog "Updating Yum" 
	yum -y update
	yum install -y mongo-stable mongo-stable-server
	checkResult $? 0 "MongoDB Installed sucessfully" "Failed to install MongoDB from repo" 

elif [ -e "/etc/debian_version" ] ; then
	#add to sources
	postlog "Adding key to Apt"
	#The key is provided as a pre-downloaded gpg file which can be added to the system using apt-key add.
	#To update the key simply add the key to a system using
	#apt-key adv --keyserver pgp.mit.edu --recv 7F0CEB10 
	#where 7F0CEB10 is the ID of the new key. then export the key to a gpg file using
	#apt-key export 7F0CEB10 > mongodb.gpg
	#Finally replace /exports/installs/db/mongodb/mongodb.gpg with this file. 
	apt-key add mongodb.gpg 
		
	postlog "Adding apt repository"
	echo "deb http://downloads.mongodb.org/distros/debian 5.0 10gen" >> /etc/apt/sources.list
	checkResult $? 0 "MongoDB added to repository list" "Unable to add MongoDB to repository list"


	postlog "Installing MongoDB"
	#install mongo

	apt-get -y update
	checkResult $? 0 "Upated repository sucessfully" "Unable to update repository" 
	apt-get -y install mongodb-stable 
	checkResult $? 0 "MongoDB Installed sucessfully" "Unable to install MongoDB from repository" 
	/etc/init.d/mongodb stop
fi
exit 0
