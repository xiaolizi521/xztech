#!/bin/sh

# Variables
HOST_NAME=`hostname -f`
# REGEX to find out application domain
REGEX="(((dev|staging|fusion|testing)[.])?peer1.com)"
DCC_REGEX="([.][a-zA-Z][a-zA-Z][a-zA-Z]([0-9])?[.]((dev|staging|fusion|testing)[.])?peer1.com)"

# determine our application domain, should move this upwards
[[ $HOST_NAME =~ $REGEX ]] && APP_DOMAIN=${BASH_REMATCH[0]}
[[ $HOST_NAME =~ $DCC_REGEX ]] && DCC_ID=${BASH_REMATCH[0]}
if [ $DCC_ID ]
then
    DCC_ID=`echo "$DCC_ID" | cut -d . -f 2`
fi


DOMAIN=$APP_DOMAIN
WAIT=2
PSQL=5432
MYSQL=3306
SSL=443
HTTP=80
LHOST=`hostname -f`
COLOR_SUCESS="<font color=\"#00FF00\">"
COLOR_ERROR="<font color=\"#FF0000\">"
COLOR_INFO="<font color=\"#E9AB17\">"
COLOR_OFF="</font>"
# general arguments to netcat with no args
NC="nc -z"

# Connect, log, and move on
function connect {
        HOST=$1
        echo -e "$COLOR_INFO Testing $HOST connectivity...$COLOR_OFF<br>"
       $NC -w $WAIT $HOST.$DOMAIN $PORT
        if [ $? == 0 ]; then
        echo -e "Connectivity from $LHOST -> $HOST.$DOMAIN:$PORT $COLOR_SUCESS PASSED $COLOR_OFF" 
        elif [ $? == 1 ]; then
        echo -e "Connectivity from $LHOST -> $HOST.$DOMAIN:$PORT $COLOR_ERROR FAILED $COLOR_OFF" 
        fi
 }

function service {
 	SERVICE=$1
	PORT=$2
        echo -e "$COLOR_INFO Testing service $SERVICE $COLOR_OFF<br>"

	PS_RESULT=`ps aux | grep $SERVICE  | grep -v grep`
	if [ -z $PS_RESULT ]
	then
		echo -e "$COLOR_ERROR $SERVICE is NOT running! $COLOR_OFF<br>"
	else 
		echo -e "$SERVICE $COLOR_SUCESS IS $COLOR_OFF running.<br>"
	fi
	if [  $PORT ]
	then
        	echo -e "$COLOR_INFO Testing port $PORT for service $SERVICE $COLOR_OFF<br>"

		NETSTAT_RESULT=`netstat -npl| grep ":$PORT "`
		if [ -z $NETSTAT_RESULT ]
		then
			echo -e "$COLOR_ERROR $SERVICE port $PORT is not being used $COLOR_OFF<br>"
		else 
			echo -e "$SERVICE port $PORT $COLOR_SUCESS is $COLOR_OFF being used<br>"
		fi
	fi
}
echo -e "Please verify hostname: $COLOR_INFO $HOST_NAME $COLOR_OFF<br>"

DEBIAN_MAJOR_VERSION=`cat /etc/debian_version | cut -f 1 -d .`
if [ ${DEBIAN_MAJOR_VERSION} -ne 4 ]
then
   echo -e "$COLOR_ERROR Debian major version do not match.  Expected 5 Got ${DEBIAN_MAJOR_VERSION} $COLOR_OFF<br>"
else
   echo -e "Debian version is $COLOR_SUCESS good $COLOR_OFF" 
fi

current_ram=`free -m|grep Mem |cut  -f 2| awk '{print $2}'`
if [ 2000 -gt $current_ram ]
then
   echo -e "$COLOR_ERROR 2gb of ram is required for a dcc $COLOR_OFF<br>"
else
   echo -e "Ram is $COLOR_SUCESS good $COLOR_OFF<br>"
fi

exports_free_space=`df -h | grep ^/dev | grep /exports | awk '{print $2}' | cut -f1 -d G`
mindisk=500
if [ -z $exports_free_space ]
then
	echo -e "$COLOR_ERROR Exports partition does not exist! $COLOR_OFF<br>"
else 
	if [ $exports_free_space -le $mindisk ]
	then
	   echo -e "$COLOR_ERROR minimum of ${mindisk}gb of disk space available $COLOR_OFF<br>"
	else
	   echo -e "Exports disk space is $COLOR_SUCESS good $COLOR_OFF<br>"
	fi
fi

free_space=`df -h | grep ^/dev | grep -v /exports | grep -v /boot | awk '{print $2}' | cut -f1 -d G`
mindisk=8
if [ -z $free_space ]
then
	echo -e "$COLOR_ERROR root partition does not exist! $COLOR_OFF<br>"
else 
	if [ $free_space -le $mindisk ]
	then
	   echo -e "$COLOR_ERROR minimum of ${mindisk}gb of disk space available $COLOR_OFF<br>"
	else
	   echo -e "Disk space is $COLOR_SUCESS good $COLOR_OFF<br>"
	fi
fi


if [ ! -x /usr/bin/svn ]
then
   echo -e "$COLOR_ERROR subversion needs to be installed $COLOR_OFF<br>"
else
   echo -e "Subversion is $COLOR_SUCESS good $COLOR_OFF<br>"
fi

RETURN_PACKETS=`ping -c 3 pforge.peer1.com|grep loss| awk '{print $4}'`
if [ $RETURN_PACKETS -ne 3 ]
then
   echo -e "$COLOR_ERROR pforge.peer1.com needs to be pingable $COLOR_OFF<br>"
else
   echo -e "pforge is $COLOR_SUCESS good $COLOR_OFF<br>"
fi


#TIMELIMIT=`cat /etc/ldap/ldap.conf | grep ^TIMELIMIT | awk '{print $2}'`
#if [ -z $TIMELIMIT ] || [ $TIMELIMIT -ne 15 ]
#then
#   echo -e "$COLOR_ERROR nscd LDAP (/etc/ldap/ldap.conf) timeouts need to be set to: $COLOR_OFF<br>"
#   echo -e "$COLOR_ERROR TIMELIMIT 15 $COLOR_OFF<br>"
#else
#   echo -e "nscd LDAP (/etc/ldap/ldap.conf) TIMELIMIT settings $COLOR_SUCESS good $COLOR_OFF<br>"
#fi
#
#TIMEOUT=`cat /etc/ldap/ldap.conf | grep ^TIMEOUT | awk '{print $2}'`
#if [ -z $TIMEOUT ] || [ $TIMEOUT -ne 20 ]
#then
#   echo -e "$COLOR_ERROR nscd LDAP (/etc/ldap/ldap.conf) timeouts need to be set to: $COLOR_OFF<br>"
#   echo -e "$COLOR_ERROR TIMEOUT   20 $COLOR_OFF<br>"
#else
#   echo -e "nscd LDAP (/etc/ldap/ldap.conf) TIMEOUT settings $COLOR_SUCESS good $COLOR_OFF<br>"
#fi

TDATA=`dpkg --get-selections ntp|awk '{print $2}'`
if [ "$TDATA" != "install" ]
then
   echo -e "ntp is $COLOR_ERROR not installed $COLOR_OFF<br>"
else
   echo -e "ntp is $COLOR_SUCESS installed $COLOR_OFF<br>"
fi

TDATA=`dpkg --get-selections ntpdate|awk '{print $2}'`
if [ "$TDATA" != "install" ]
then
   echo -e "ntpdate is $COLOR_ERROR not installed $COLOR_OFF<br>"
else
   echo -e "ntpdate is $COLOR_SUCESS installed $COLOR_OFF<br>"
fi

PRODUCT_ENG_USER=`getent group | grep ^pe-local `
if [ -z $PRODUCT_ENG_USER ]
then 
	echo -e "$COLOR_ERROR pe-local user doesn't not exist $COLOR_OFF<br>"
else 
	echo -e "pe-local user $COLOR_SUCESS exists $COLOR_OFF<br>"
fi

#PRODUCT_ENG_USER_SUDO=`cat /etc/sudoers | grep ^pe-local | grep 'ALL=(ALL) ALL'`
cat /etc/sudoers | grep ^pe-local | grep 'ALL=(ALL) ALL'
if [ $? -ne "0" ]
then 
	echo -e "$COLOR_ERROR pe-local user doesn't have SUDO access $COLOR_OFF<br>"
else 
	echo -e "pe-local user $COLOR_SUCESS has $COLOR_OFF sudo access<br>"
fi

service pxed 
service named 53
service dhcpd3 67
service rsync 873
service smb 139
service smb 445
service postgres 
service tftpd 69
service vsftpd 21



# Test the SSL hosts first
PORT=443
connect roomba.${DCC_ID}
echo -e "<br>"
connect dcc.${DCC_ID}



