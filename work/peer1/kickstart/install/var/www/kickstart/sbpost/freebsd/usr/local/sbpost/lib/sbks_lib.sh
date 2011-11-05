#!/usr/local/bin/bash

/usr/local/sbpost/lib/config.sh

fetch() {
	url=$1
	post=$2
	file=$3

	if [ -z $url ] ; then return 1 ; fi
	if [ -z $file ] ; then file="/dev/null" ; fi

    if [[ -n $post ]] ; then
        /usr/local/bin/curl --max-time 30 --data "$post" --output $file --silent --url $url
    else
        /usr/local/bin/curl --max-time 30 --output $file --silent --url $url
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
        echo "$logstring" >> /usr/local/sbpost/postboot.log

        if [ x"$lvl" == x"FATAL" ] || [ x"$lvl" == x"ERR" ] ; then
		post="macaddr=${MACADDR}&ipaddr=${IPADDR}&status=${status}"
		fetch "http://${ks_public_ipaddr}/cgi-bin/register.cgi" "$post"
        post="macaddr=${MACADDR}&error_message=${msg}"
        fetch "http://${ks_public_ipaddr}/cgi-bin/logError.cgi" "$post"
        read FOO
		#exit 1
	fi
}

MSG=`dmesg | awk ' /Ethernet address/ { print $1 }' | sed 's/.\{1\}$//'`
for i in $MSG; do
    if ifconfig $i | grep 'status: active' > /dev/null ; then
    IFACE=$i; export IFACE
    break
    fi
done

getMAC() {
	MACADDR=`ifconfig $IFACE | grep "ether" | awk '{ print $2 }' | tr A-Z a-z`
	export MACADDR
}

getIP() {
	IPADDR=`ifconfig $IFACE | grep "inet" | awk '{print $2}' | cut -d: -f2`
	export IPADDR
}
