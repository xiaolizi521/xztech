#!/bin/bash
# Installer for Plesk 9.x for Linux. Trying to clean up and make
# more managable than past iterations.
# jbair@2009-4-29
# v.5 - Unified CentOS/RHEL packages, submitted to QA.
# v.4 - Added Debian 2x loop from start.
# v.3 - Cleaned up a bit, testing multi-licensing.
# v.2 - Finished and working for RHEL/Cent5 and Debian 4.0.
# v.1 - Initial clean-up and re-write.

# Make sure this is run as root
if [ $UID -ne 0 ]; then
	echo "This script must be run as root."
	exit 1
fi

# Variables!
installerFlags='--select-release-id PLESK_9_5_3 --install-everything'
sitebuilderFlags='--select-product-id sitebuilder --select-release-id SB_4_5_0 --install-everything'
PATH='/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin'
psaver='9.5'
sbpost='/usr/local/sbpost'
workdir="${sbpost}/plesk"

# Source the following files if present.
for file in /boot/.serverbeach $sbpost/postconf.info $sbpost/lib/sbks_lib.sh; do
	if [ -f $file ]; then
		echo "Found $file - Sourcing It."
		. $file
	else
		echo "Unable to Source $file - Not Found."
	fi
done

# If we don't have a public IP addr for Kickstart, this is a problem.
if [ -z $ks_public_ipaddr ] ; then
	echo "KS IP address not found!"
	exit 1
fi

####################
# BEGIN DEPENDENCY #
####################

# Install dependant packages if needed.
# Install packages that the "auto installer" doesn't give us automatically. Lame.
# Support ticket in to Parallels to hopefully remove the need for this hack.
# Done as a case statement so we can scale easier in the future.
case $osload in

	# Install dependencies on RHEL/Cent. Debian works fine. Originally, this was split
	# by arch and OS, but the packages are the same and it just leaves room for stuff to 
	# break in the future.
	#
	# To find these dependencies, download the full .rpm.tgz and attempt to install 
	# them via:
	#
	# wget psa* (tarball)
	# tar xzvf psa*
	# for i in $(rpm -Uvh */*.rpm */*/*.rpm */*/*/*.rpm 2>&1 | awk '($1 !~ /error:/ && $1 !~ /warning:/) {print $1}' | sort -u | grep -v '.so' | egrep -v '^/'); do echo -n " $i"; done; echo
	
	# Trying RHEL/Centos central package list!
	rhel5ks|rhel5_64ks|centos5ks|centos5_64ks)
	postlog "INFO" "Installing Plesk $psaver dependancies for $osload"
	yum install -y cyrus-sasl-md5 db4-utils gcc-java httpd-devel java-sdk libgcj-devel mailman perl-TimeDate php-gd php-imap php-mbstring php-xml postfix psa-mail-qc-driver ruby ruby-devel ruby-irb ruby-libs sharutils tomcat5 tomcat5-admin-webapps tomcat5-webapps
	yumec=$?
	;;

	# Catch-all for OSes that don't need the above hack
	# Currently for Debian 4.0 (32/64). Note Debian needs to run 2x.
	*)
	postlog "INFO" "No Plesk $psaver dependencies needed for $osload"
	yumec=0
	;;

esac

# Ensure yum exits gracefully
if [ $yumec -ne 0 ]; then
	postlog "FATAL" "Plesk $psaver dependant package install failed ($?)"
else
	postlog "INFO" "Plesk $psaver dependant package install successful."
fi

######################
# BEGIN INSTALLATION #
######################

# The install, at this point, should work the same for both ALL THREE DISTROS. =)
#
# CentOS 5 installs just dandy without needing any help. <--This is how it's supposed to work.
# RHEL 5 needs the above packages installed first. 
# Debian needs the installer run 2x to work (the loop below handles this)
#
# Also, I am using non-specific flags so we can drop in newer installers for zero 
# work in the future, since we went 9.x and not 9.0 for this Plesk version.

# Find and unpack the actual Plesk autoinstaller tgz
cd $workdir
archive=$(find . -name "psa_${psaver}_*.tgz")
if [ -s $archive ]; then
	postlog "INFO" "Plesk $psaver archive found: $archive"
	tar -xzvf $archive
	if [ $? -eq 0 ]; then
		postlog "INFO" "Plesk $psaver archive unpack complete"
	else
		postlog "FATAL" "Plesk $psaver archive unpack failed"
	fi
else
	postlog "INFO" "Plesk $psaver archive not found"
fi

# Find our autoinstaller file.
installer=$(find . -name "parallels_installer_*")
if [ -z "$installer" ]; then
	postlog "FATAL" "Unable to find our Plesk $psaver installer file for RHEL/CentOS."
else
	postlog "INFO" "Found our Plesk $psaver installer file: $installer"
	chmod +x $installer
fi

# Start the installation process.
# We also loop this bad boy 5 times, just in case. This is something support does 
# by hand, so let's automate it.
loop=0
ec=1
while [ "$loop" -lt 5 ] && [ "$ec" -ne 0 ]; do
	# If this is a loop, sleep for a bit. Exponentially waits longer per retry 
	# to try and let the repos fix themselves.
	if [ "$loop" -ne 0 ]; then
		# Sleep for 4 mins * loop number. So that would be 4,8,12,16.
		sleepTime=$((240 * $loop))
		postlog "INFO" "Attempt #${loop} did not succeed. Sleeping for $sleepTime seconds, then trying again."
		sleep $sleepTime
	fi

	# Preliminary installer for Debian since it needs to be run 2x 
	if [ "$loop" -eq 0 ] && [ -s /etc/debian_version ]; then
		postlog "INFO" "Running preliminary Plesk $psaver install for Debian."
		./${installer} $installerFlags
	fi

	# Run our installer
	# Our $loop number is always 1 lower than the actual iteration.
	attemptNumber=$(($loop +1))
	postlog "INFO" "Attempting to install Plesk $psaver - Attempt #${attemptNumber}"
	./${installer} $installerFlags

	# Catch the installer's exit code.
	ec=$?
	# Increment our loop by one.
	loop=$(($loop + 1))
done

# Find out if we exited the above loop gracefully or not.
if [ "$ec" -eq 0 ]; then
	postlog "INFO" "Plesk $psaver Installation Successful."
else
	postlog "FATAL" "Plesk $psaver Installation Failed."
fi

# Plesk Sitebuilder Installation Process
# As of 9.5.x, this is included as a bundled feature.
# Installation should be the same as above, with different options.

loop=0
ec=1
while [ "$loop" -lt 5 ] && [ "$ec" -ne 0 ]; do
    # If this is a loop, sleep for a bit. Exponentially waits longer per retry
    # to try and let the repos fix themselves
    if [ "$loop" -ne 0 ]; then
        # Sleep for 4 mins * loop number. SO that would be 4,8,12,16.
        sleepTime=$((240 * $loop))
        postlog "INFO" "Attempt #${loop} did not succeed. Sleeping for $sleepTime seconds, then trying again."
        sleep $sleepTime
    fi

    # Preliminary installer for debian since it needs to be run 2x
    if [ "$loop" -eq 0 ] && [ -s /etc/debian_version ]; then
        postlog "INFO" "Running preliminary Plesk $psaver install for Debian."
        ./${installer} $sitebuilderFlags
    fi

    # Run our installer
    # Our $loop number is always 1 lower than the actual iteration
    attemptNumber=$(($loop +1))
    postlog "INFO" "Attempting to install Plesk $psaver - Attempt #${attemptNumber}"
    ./${installer} $sitebuilderFlags

    # Catch the installer's exit code.
    ec=$?
    # Increment our loop by one.
    loop=$(($loop + 1))
done

# Find out if we exited the above loop gracefully or not
if [ "$ec" -eq 0 ]; then
    postlog "INFO" "Plesk Sitebuilder Installation Successful."
else
    postlog "FATAL" "Plesk Sitebuilder Installation Failed."
fi

# See if our qmail symlink is there and create it if missing.
# Not sure if needed for 9.x Keeping around just in case. 
if [ ! -L /usr/local/psa/qmail ]; then
	postlog "INFO" "Plesk $psaver missing qmail symlink, creating."
	ln -sf /var/qmail /usr/local/psa/qmail
fi

# Set the Plesk admin password
if [ -n "$PPASS" ] ; then
	postlog "INFO" "Found admin password for Plesk $psaver - setting admin password"
	# Seriously doubt this will fail, so not checking.
	echo $PPASS > /etc/psa/.psa.shadow
	mysqladmin --user=admin --password=setup password $PPASS
	# This however, is worth checking.
	if [ $? -eq 0 ]; then
		postlog "INFO" "Configuration of the Plesk $psaver admin password successful."
	else
		postlog "FATAL" "Failed to configure Plesk $psaver admin password."
	fi
# If we don't have a password for Plesk, this is a problem.
# This could be disabled to be used as an upgrader script.
else
	postlog "FATAL" "Unable to set our Plesk $psaver admin password."
fi

# Fake an initial configuration so we can make changes to single sign on
/usr/local/psa/bin/init_conf --init
if [ $? -eq 0 ]; then
  postlog "INFO" "Successfully set 'initial configuration' option to true"

  # Fix single sign on issue with Plesk 9.x
  /usr/local/psa/bin/sso -d
  if [ $? -eq 0 ]; then
    postlog "INFO" "Successfully disabled single sign on for Plesk"
  else
    postlog "FATAL" "Could not disable single sign on. Contact PE"
  fi

else
  postlog "FATAL" "Failed to set 'initial configuration' option to true"
fi


#Install Plesk 9.x fix for SSL

sslfix=$(find . -name "sw-cp-server-*")
if [ -z "$sslfix" ]; then
	postlog "INFO" "No SSL Fix found, not installing"
else
	postlog "INFO" "Installing pre-requisite OpenSSL package (libssl.so.4) for DrWeb"
	yum -y install openssl097a
	if [ $? -eq 0 ]; then
		postlog "INFO" "Successfully installed pre-requisite package"
	else
		postlog "FATAL" "Could not install pre-requisite package"
	fi

	postlog "INFO" "Installing Plesk $psaver SSL fix for $osload"
	rpm -Uvh --force sw-cp-server-*.rpm
	if [ $? -eq 0 ]; then
		postlog "INFO" "Successfully installed the $psaver SSL fix"
	else
		postlog "INFO" "Could not install $psaver SSL fix"
	fi
fi

##################
# BEGIN LICENSE ##
##################

cd "$sbpost/licenses"

# Now find and install our license. Note that with 9.x the .sh files
# are no longer supported. You must use .xml files.
psalicense=$(find . -name PLSK\*.xml)
if [ -z "$psalicense" ] ; then
	postlog "FATAL" "Plesk $psaver license not found."
else
	postlog "INFO" "Plesk $psaver license file $psalicense found - attempting to license."
	# Ensure the Key Manager is present, and if so, license our system.
	if [ -x /usr/local/psa/admin/sbin/keymng ]; then
		/usr/local/psa/admin/sbin/keymng --install --source-file $psalicense
		if [ $? -eq 0 ]; then
			postlog "INFO" "Plesk $psaver has been licensed successfully."
		else
			postlog "FATAL" "Attempted to apply license file $psalicense to Plesk $psaver and failed."
		fi
	else
		postlog "FATAL" "Key Manager binary for Plesk $psaver Missing."
	fi
fi

# Restart Plesk. This was around in the previous installer script. There's no real way to check 
# exit codes (or at least I can't get it to fail) and in the past it didn't check either. However, 
# we can restart the serivces on all 3 platforms the same, so no checking $osload. Still, if this 
# process is broken, it's better to know directly after provisioning than later when customers try 
# to reboot/restart the process manually.
/etc/init.d/psa stopall
/etc/init.d/psa start

# All done!
postlog "INFO" "Plesk $psaver Full Installation Completed Successfully!"
exit 0
