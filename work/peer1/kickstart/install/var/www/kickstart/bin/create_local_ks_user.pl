#!/bin/bash

OPER=$1
USER=$2
PASS=$3
GROUP=$4
USER_UID=$5

function create {
	user=$1
	pass=$2
	group=$3
	uid=$4

	groupListing=`getent group $group`
	addOpt=''
	if [ -z $groupListing ] 
	then
		echo "Group does not exist, creating..."
		addgroup $group

		echo "Chowning /exports/installs"
		chown -R root.$group /exports/installs
		chown -R root.$group /tftpboot/pxe
		chown -R root.$group /exports/kickstart/postconf
		chown -R root.$group /exports/kickstart/taskfiles.new
		chown -R root.$group /exports/kickstart/sbpost
		chown -R root.$group /exports/kickstart/kscfg
		chown -R root.$group /exports/kickstart/modules



		echo "Allowing SSH login for this group, backing up /etc/pam.d/ssh to ~/ssh.bak"
		auth=`cat /etc/pam.d/ssh | grep "pam_require.so"`
		if [ -z $auth ]
		then
			cat /etc/pam.d/ssh | grep -v "pam_require.so" > /etc/pam.d/ssh.new
			echo "$auth @$group" >> /etc/pam.d/ssh.new
			mv /etc/pam.d/ssh ~/ssh.bak
			mv /etc/pam.d/ssh.new /etc/pam.d/ssh
		fi

		addOpt=' -g'
	fi

	echo "Creating user.."
	useradd -m -u $uid -s /bin/bash -g $group $user
	
	echo "Setting password"
	echo "$user:$pass" | chpasswd -m
	
}

function delete {
	user=$1

	echo "Deleting User account $user"
	deluser $user
	
	echo "Removing home directory"
	rm -rf /home/$user


}
if [ -z $OPER ] || [ -z $USER ] || [ -z $PASS ] || [ -z $GROUP ] || [ -z $UID ]; then
	echo "Usage: create_local_ks_user.sh create|delete USERNAME PASSWORD GROUPi UID"
	exit 0
fi

if [ $OPER = "create" ] 
then
	echo "Creating user $USER in group $GROUP with password $PASS. Are you sure you want to continue [y/n]:"
	read response
	if [[ $response =~ [yY*] ]]
	then
		create $USER $PASS $GROUP $USER_UID
	fi

elif [ $OPER = "delete" ]
then
        echo "Deleting user $USER. Are you sure you want to continue [y/n]:"
	read response
	if [[ $response =~ [yY*] ]]
	then
		delete $USER 
        fi
fi


