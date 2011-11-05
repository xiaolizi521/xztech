#!/bin/bash

. /usr/local/p1post/lib/config.sh

fetch() {
	url=$1
	post=$2
	file=$3

	if [ -z $url ] ; then return 1 ; fi
	if [ -z $file ] ; then file="/dev/null" ; fi

	if [[ -n $post ]] ; then
		/usr/bin/curl --max-time 30 --data "$post" --output $file --silent --url $url
	else
		/usr/bin/curl --max-time 30 --output $file --silent --url $url
	fi
}

postlog() {                                                    
	lvl=$1
	msg=$2
	status=$3
	prog=`basename $0`

	if [ -z "$status" ] ; then status="ksfail" ; fi

        date=`date "+%Y.%m.%d-%T"`
        logstring="$date $prog[$lvl] $msg"
        echo "$logstring" >> ${postboot_log}

        if [ x"$lvl" == x"FATAL" ] || [ x"$lvl" == x"ERR" ] ; then
		post="macaddr=${MACADDR}&ipaddr=${IPADDR}&status=${status}"
		fetch "http://${ks_public_ipaddr}/cgi-bin/register.cgi" "$post"

		post="macaddr=${MACADDR}&error_message=${msg}"
		fetch "http://${ks_public_ipaddr}/cgi-bin/logError.cgi" "$post"

		echo "${lvl} message type detected. Halting..." >&2
		read FOO
		#exit 1
	fi
}

# ndurr@2008-11-18: all the scripts where written assuming only one interface
getPubInterface() {
        pub_int=$(netstat -nr | awk ' { if ( $1 == "0.0.0.0" )  print $8 } ')
        echo $pub_int
}

getMAC() {
	int=$(getPubInterface)
	MACADDR=$(ifconfig $int | grep HWaddr | awk '{ print $5 }' | tr A-Z a-z)
	export MACADDR
}

getIP() {
        int=$(getPubInterface)
	IPADDR=$(ifconfig $int | grep "inet addr" | awk '{print $2}' | cut -d: -f2)
	export IPADDR
}

checkResult() {
	result=$1
	expectedresult=$2
	successmsg=$3
	failmsg=$4
	if [ $result -ne $expectedresult ] ; then
		postlog "FATAL" "$failmsg"
		echo $failmsg
		exit 1 
	else 
		postlog "INFO" "$successmsg"
		
	fi
}
