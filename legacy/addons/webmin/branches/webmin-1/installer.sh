#!/bin/bash

sbpost="/usr/local/sbpost"
. /usr/local/sbpost/lib/sbks_lib.sh
postlog "INFO" "Running Webmin Installer"


workdir="${sbpost}/webmin"
. /boot/.serverbeach
. ${sbpost}/postconf.info

cd ${workdir}

# kschwerdtfeger@03-13-2007
#   Making a couple of changes.  First, the script was checking to see what the os type was multiple times.
#   Not a big deal, but seemed kinda silly to me.  Secondly, there seems to be an error with debian building
#   the Net::SSLeay perl module so we are going to just pull the latest version from the web.
webminhome="/usr/share/webmin/"


if [ -e "/etc/redhat-release" ] ; then
	#add to sources
	postlog "INFO" "Adding Webmin Repository to yum"
	
	# Yum expects properly formatted files. Spaces will cause errors.
    ( cat <<'EOF'
[Webmin]
name=Webmin Distribution Neutral
baseurl=http://download.webmin.com/download/yum
enabled=1
EOF
    ) > /etc/yum.repos.d/webmin.repo

	postlog "INFO" "Adding repo Key to yum"
	#download key
	rpm --import http://www.webmin.com/jcameron-key.asc
	checkResult $? 0 "Yum Key downloaded" "Failed to download Yum Key from http://www.webmin.com/jcameron-key.asc" 

	postlog "INFO" "Checking for Centos 4"
	cat /etc/redhat-release | grep 'CentOS release 4.*'
	if [ $? == 0 ] ; then
		postlog "INFO" "Installing SSLeay"
		yum -y install perl-Net-SSLeay
	fi
	postlog "INFO" "Installing Webmin"
	#install webmin
	yum install -y webmin
	checkResult $? 0 "Webmin Installed sucessfully" "Failed to install webmin from repo" 

	webminhome="/usr/libexec/webmin/"

elif [ -e "/etc/debian_version" ] ; then
	#add to sources
	postlog "INFO" "Adding apt repository"
	echo "deb http://download.webmin.com/download/repository sarge contrib" >> /etc/apt/sources.list
	checkResult $? 0 "Webmin added to repository list" "Unable to add Webmin to repository list"

	postlog "INFO" "Downloading apt key from http://www.webmin.com/jcameron-key.asc"
	#download key
	wget -O /tmp/jcameron-key.asc http://www.webmin.com/jcameron-key.asc
	checkResult $? 0 "Webmin Key downloaded sucessfully" "Unable to download webmin key from http://www.webmin.com/jcameron-key.asc" 

	postlog "INFO" "Adding key to apt-key"
	apt-key add /tmp/jcameron-key.asc
	checkResult $? 0 "Key added to apt-key" "Unable to add Webmin key" 

	postlog "INFO" "Installing Webmin"
	#install webmin
	apt-get update
	checkResult $? 0 "Upated repository sucessfully" "Unable to update repository" 
	apt-get -y install webmin
	checkResult $? 0 "Webmin Installed sucessfully" "Unable to install Webmin from repository" 

	webminhome="/usr/share/webmin/"
fi

postlog "INFO" "Removing webmin configuration directory"
rm -rf /etc/webmin
	checkResult $? 0 "Webmin config directory was removed" "Unable to remove Webmin config directory" 

postlog "INFO" "Removing webmin from chkconfig or from /etc/rc.d"
if [ -x /sbin/chkconfig ] ; then
	chkconfig --del webmin
else
	
	find "/etc/rc.d/rc[0-6].d" -name "[SK][0-9][0-9]webmin" -exec rm -f {} ';'
fi

postlog "INFO" "Exporting Webmin variables"
export config_dir="/etc/webmin"
export var_dir="/var/webmin"
export perl="/usr/bin/perl"
export perldef="/usr/bin/perl"
#Setting the port to preset value: 10010
#export port="${WEBPORT}"
export port="10044"
export login="${PUSER}"
export password="${PPASS}"
export password2="${PPASS}"
export ssl=1
export sslyn="y"
export atboot=1
export atbootyn="y"
export host="${fqdn}"
export os_type

postlog "INFO" "Configuring Webmin"
${webminhome}/setup.sh
checkResult $? 0 "Configured Webmin sucessfully" "Failed to configure Webmin" 

postlog "INFO" "Removing listen from webmin configuration"
grep -v ^listen /etc/webmin/miniserv.conf > /tmp/miniserv.conf
mv -f /tmp/miniserv.conf /etc/webmin/miniserv.conf

postlog "INFO" "Configuring Webmin for sendail"
perl -ne '$_ =~ s/^(sendmail_cf)=.*/$1=\/etc\/mail\/sendmail.cf/; print $_' \
    </etc/webmin/sendmail/config >/tmp/sendmail-config.$$
mv -f /tmp/sendmail-config.$$ /etc/webmin/sendmail/config

postlog "INFO" "Configuring Webmin for Mysql"
perl -ne 'if (/^start_cmd=/) { $_ =~ s/mysql(\s+)/mysqld /; } print $_' \
    </etc/webmin/mysql/config >/tmp/mysql-config.$$
mv -f /tmp/mysql-config.$$ /etc/webmin/mysql/config

exit 0