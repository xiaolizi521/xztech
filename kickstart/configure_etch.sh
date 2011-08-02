#!/bin/bash

############################################################################
# Filename: configure.sh
# Description:  This bash script can be used to deploy a kickstart server from scratch or to
#               install code updates on an already configured kickstart system
#
# Author: kschwerd
#         framework taken from install scripts provided by sglane and dmoore
#
# Date: Oct 4th, 2007
# https://pforge.peer1.com/svnroot/kickstart/trunk/exports/kickstart/configure.sh
#
# changelog:
# kschwerd@2007-08-20:  * initial file
# kschwerd@2007-10-04:  * rewritten a lot
# kschwerd@2007-10-09:  * Created template files on the kickstart server that can be 
#                         used to build some required files if they do not exist.  Also added
#                       * Used debconf-set-selections to pre-answer some questions
#                       * Added dependancy check for /tftpboot until the PXE project and the kickstart
#                         project can be decoupled
#                       * Corrected some initial database deployment issues
#                       * Added a large number of comments
#                       * Added second method to get network address in case it is not defined in network/interfaces
# cavila@2008-04-08:  *Updated the script to install on Debian Etch. Not backwards compatible. 
# cavila@2008-11-08:  *Removed package pinning. It just breaks the install. Should be done post-install
#
############################################################################

############################################################################
# Globals
#
#		Global declarations are defined here
#
############################################################################

############################################################################
# Define key apt packages
############################################################################

#debian 4 (etch)
POSTGRESQL='postgresql-8.1';
APACHE='apache2-mpm-prefork';
SAMBA='samba';
PERL='perl';
SCREEN='screen';
TFTPD='tftpd-hpa';
DHCPD='dhcp3-server';

############################################################################
#Software that will be installed
############################################################################

INSTALL_PACKAGES=""

# basic tools we should have (we are going to need to debconf-utils to answer some questions later)
INSTALL_PACKAGES="$INSTALL_PACKAGES rsync $SCREEN netcat tcpdump minicom mc \
                vim ipcalc sudo sysklogd bzip2 unzip iproute vlan"

# just listing some packages that we need for kickstart system 
# The dhcp3-server and tftpd packages are really tied more to the PXE project and not kickstart, but that can 
# be sorted out later.  Also this will be attempting to load the stock tftpd and dhcp3 packages, not the customer ones as
# they should no longer be required
INSTALL_PACKAGES="$INSTALL_PACKAGES less $APACHE apache2-utils dhcp3-server \
                dhcp3-common tftpd-hpa tftp-hpa squid3 xmlstarlet vsftpd"

# some PERL stuff (pretty sure we don't need all of these...)
INSTALL_PACKAGES="$INSTALL_PACKAGES $PERL perl-base perl-doc perl-modules perl-suid libnet-telnet-perl \
                libnet-telnet-cisco-perl libtime-modules-perl liburi-perl libwww-perl libxml-libxml-common-perl \
                libxml-libxml-perl libxml-namespacesupport-perl libxml-sax-perl libxml-simple-perl libdbd-pg-perl \
                libdbi-perl libfile-type-perl libhtml-parser-perl libhtml-tagset-perl libhtml-tree-perl \
                liblocale-gettext-perl libnet-daemon-perl libnet-perl libnet-ping-external-perl libnet-rawip-perl \
                libnet-ssh-perl libperl5.8 libplrpc-perl libsnmp-perl libtext-charwidth-perl libtext-iconv-perl \
                libtext-wrapi18n-perl libdate-manip-perl libcrypt-ssleay-perl libcrypt-passwdmd5-perl \
                libcompress-zlib-perl libxml-xql-perl libxml-xpath-perl libxml-twig-perl libdigest-crc-perl"

# install database stuff
INSTALL_PACKAGES="$INSTALL_PACKAGES $POSTGRESQL libsybdb5 libpq4 libct3" 

# install samba packages for windows installs
INSTALL_PACKAGES="$INSTALL_PACKAGES $SAMBA samba-common"

#Tools that we need everyday
INSTALL_PACKAGES="$INSTALL_PACKAGES smbclient curl"

############################################################################
# usage
#
#       Print a usage statement and exit
#
############################################################################
function usage 
{
        echo "Usage: $0 {-p|-s|-t|-d} ... {-a|-i|-c|-r|-b} ... [-h]"
        echo "-h        help"
        echo
        echo "Environment to build (one required)"
        echo "-p        production server"
        echo "-s        staging server"
        echo "-t        testing server"
        echo "-d        development server"
        echo
        echo "Type of deployment (one required)"
        echo "-a        all                    (full install - package install, DB setup and code)"
        echo "-i        installation           (package installation only)"
        echo "-c        code/config deployment (code install only)"
        echo "-r        rollback               (revert code and configs)"
        echo "-b        backup                 (backup current config -- not yet implemented)"
        if [ "$1" != "" ]; then
                echo
                echo $1
        fi
        exit $2
}

############################################################################
# install_packages
#
#       This function is used to install packages that are required and/or useful 
#       for kickstart operation and administration
#
############################################################################
function install_packages 
{
        echo -e "\n===SOFTWARE INSTALL===\n"
        
        # This function is used to install packages that are required and/or useful for kickstart operation and administration

        echo "Configuring repositories..."
        #Set standarized repo sources
        echo "deb http://archive.debian.org/debian/ etch main non-free" > /etc/apt/sources.list
        echo "deb-src http://archive.debian.org/debian/ etch main non-free" >> /etc/apt/sources.list
        echo "deb http://security.debian.org/ etch/updates main contrib" >> /etc/apt/sources.list
        echo "deb-src http://security.debian.org/ etch/updates main contrib" >> /etc/apt/sources.list

        #Package pinning goes here
        #echo -e "Package: $APACHE\nPin: version 2.2.3-4+etch4\nPin-Priority: 1001\n" > /etc/apt/preferences
        #echo -e "Package: $SAMBA\nPin: version 3.0.24-6etch10\nPin-Priority: 1001\n" >> /etc/apt/preferences
        #echo -e "Package: $TFTPD\nPin: version 0.43-1.1\nPin-Priority: 1001\n" >> /etc/apt/preferences
        #echo -e "Package: dhcp3-common\nPin: version 3.0.4-13\nPin-Priority: 1001\n" >> /etc/apt/preferences
        #echo -e "Package: dhcp3-dev\nPin: version 3.0.4-13\nPin-Priority: 1001\n" >> /etc/apt/preferences
        #echo -e "Package: dhcp3-relay\nPin: version 3.0.4-13\nPin-Priority: 1001\n" >> /etc/apt/preferences
        #echo -e "Package: dhcp3-server\nPin: version 3.0.4-13\nPin-Priority: 1001\n" >> /etc/apt/preferences

        # Pre-answer some questions usng debconf-set-selections.  The values can be obtained by using the 
        # debconf-get-selections program from the debconf-utils packages.
        echo "samba-common    samba-common/workgroup       string" | debconf-set-selections 
        echo "samba-common    samba-common/dhcp       boolean false" | debconf-set-selections 
        echo "$DHCPD    $DHCPD/new_auth_behavior  note" | debconf-set-selections 

        echo "Installing packages... "
        # Update aptitude package lists
        aptitude update
        aptitude install -yr $INSTALL_PACKAGES
        echo "done."
}

############################################################################
# pre_install
#
#       This function is used to do some basic system setup, adding users, etc that is required 
#       prior to actually installating the kickstart system
#
############################################################################
function pre_install
{
        echo -e "\n===PRE INSTALL===\n" 

	echo -e "\nChecking hostname on /etc/hosts"
	if [[ `grep -c $(hostname) /etc/hosts` == 1 ]]; then
		echo -e "\n\tHostname found in /etc/hosts, no need to add."
	else 
		echo -e "\n\tHostname not found, adding..."
		echo -e "${IP_ADDR} \t$(hostname) \t$(hostname|awk -F. '{print $1}')" >> /etc/hosts
	fi
	echo -e "\nDone."

        [[ ! -d /exports ]] && mkdir /exports

        # add /bin/false as shell, as it will just log the user out
        echo -n "checking for /bin/false in /etc/shell....."
        if [[ ! `grep -c '/bin/false' /etc/shells` == 1 ]]; then
                echo "does not exist, adding."
                echo "/bin/false" >> /etc/shells
        else
                echo "already exists, no need to add"
        fi

        # now create an install user for kickstart
        # Need to determine required groups, passwd, etc.
        echo "creating account: kickstart"
        groupadd kickstart
        useradd -m -g kickstart -d /home/kickstart -s /bin/false kickstart
	usermod -a -G kickstart,adm www-data

        # now create an install user for samba
        echo "creating account: install"
        groupadd install
        useradd -m -g install -d /home/install -s /bin/false install -p '$1$2A3uCs2u$P.vq/EJT.jte2YyvXCYtx/'

	    # creating provision user needed for win2k8 samba share
	        echo -e "\nConfiguring Sabma..."
    
    	echo -e "\tAdding 'provision' user..."
    	groupadd provision
  	  	useradd -m -g provision -d /home/provision -s /bin/false provision
    	echo -e 'pr0vi$10N\npr0vi$10N' | smbpasswd -a provision -s 
    	echo -e "\tDone"
        
        # This is probably not needed anymore as we are not kicking ensim.  Until we get rid of the rest of the
        # servers that are running ensim, though, we need to add this just in case
        # add account: ensimwpl for ftp stuffs related to this panel
        # what group should this account belong too? of the same name?
        # what about the files it owns in ftp? TODO:Remove support?
        echo "creating account: ensimwpl"
        useradd -m -d /exports/installs/panels/ensim/ftp2.ensim.com -s /bin/false ensimwpl -p '$1$LscCQB73$Cx/ZG.X3nfv6L5Eb5dNAg0'

        # nobody logs in as sbadmin, so need to worry for a password
        echo "creating account: sbadmin"
        groupadd sbadmin
        useradd -m -g sbadmin -d /home/sbadmin -s /bin/bash sbadmin 
}

############################################################################
# db_setup
#
#       Create the kickstart database and insert ready data
#
############################################################################
function db_setup
{
        echo -e "\n===DB SETUP===\n"    

    # If there is a screen session running we need to kill it otherwise it will prevent
    # us from being able to drop the database
        SCREENOPTS="-d -m -c /exports/kickstart/configs/screenrc"
        for PID in `ps -ef | grep "SCREEN ${SCREENOPTS}" | awk ' ! /grep/ {print $2}'` ; do
                kill -9 $PID
        done
        screen -wipe

        # create database from schema
        echo "Creating database from schema..."

        # create kickstart database 
        su postgres -c "psql template1 -f \"${INSTALL_DIR}/install/var/www/kickstart/sql/create.psql\""

        # create some views
        su postgres -c "psql kickstart -f \"${INSTALL_DIR}/install/var/www/kickstart/sql/views.psql\""

        # if there is already and insert file, do not overwrite it.  However if there is not one, we want
        # to create a basic one that inserts some required info
        PSQL_TEMPLATE="${INSTALL_DIR}/install/var/www/kickstart/sql/insert-template.psql"
        PSQL_LOCALIZED_TEMPLATE="${INSTALL_DIR}/install/var/www/kickstart/sql/insert-${DC_ABBR}.psql"
        
        if [[ ! -e $PSQL_LOCALIZED_TEMPLATE ]] ; then
                sed "   s/@@DC_NUMBER@@/${DC_NUMBER}/g ; \
                        s/@@DC_NAME@@/${DC_NAME}/g ;    \
                        s/@@DC_ABBR@@/${DC_ABBR}/g"     \
                        ${INSTALL_DIR}/install/var/www/kickstart/sql/insert-template.psql > ${INSTALL_DIR}/install/var/www/kickstart/sql/insert-${DC_ABBR}.psql
        fi

        # insert any information
        su postgres -c "psql kickstart -f \"${INSTALL_DIR}/install/var/www/kickstart/sql/insert-${DC_ABBR}.psql\""
}


############################################################################
# build_links
#
#       Function used to link the operational path to the instance that we are installing.  
#
############################################################################
function build_links 
{
        echo -e "\n===BUILDING SYMLINKS===\n"   

        # Link datacenter specific config files in lib directory.  If the files does not exist
        # (which should only happen on "NEW" installs) create it based on a template
        if [[ ! -e ${INSTALL_DIR}/install/var/www/kickstart/lib/SB/Config-${DC_ABBR}.pm ]] ; then
                if [[ $ENVIRONMENT = "development" ]] ; then
                        IF=${INSTALL_DIR}/install/var/www/kickstart/lib/SB/Config-template-DEV.pm
                elif [[ $ENVIRONMENT = "production" ]] ; then
                        IF=${INSTALL_DIR}/install/var/www/kickstart/lib/SB/Config-template-PROD.pm
                elif [[ $ENVIRONMENT = "staging" ]] ; then
                        IF=${INSTALL_DIR}/install/var/www/kickstart/lib/SB/Config-template-STAGING.pm
                fi

                sed "   s/@@DC_NUMBER@@/${DC_NUMBER}/g ; \
                        s/@@DC_NAME@@/${DC_NAME}/g ;    \
                        s/@@DC_ABBR@@/${DC_ABBR}/g ;    \
                        s/@@IPADDR@@/${IP_ADDR}/g  ;    \
                        s/@@NETWORK@@/${NETWORK}/g ;    \
                        s/@@NETMASK@@/${NETMASK}/g ;    \
                        s/@@GATEWAY@@/${GATEWAY}/g "    \
                        ${IF} > ${INSTALL_DIR}/install/var/www/kickstart/lib/SB/Config-${DC_ABBR}.pm
        fi

        ln -sf ${INSTALL_DIR}/install/var/www/kickstart/lib/SB/Config-${DC_ABBR}.pm ${INSTALL_DIR}/install/var/www/kickstart/lib/SB/Config.pm

        # Build kickstart links
        ln -sf ${INSTALL_DIR}/install/var/www/kickstart  /exports/kickstart

        # Link some system wide config files
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/hosts.allow /etc/hosts.allow
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/hosts.deny /etc/hosts.deny
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/syslog.conf /etc/syslog.conf
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/default/tftpd-hpa  /etc/default/tftpd-hpa
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/tftpremap /etc/tftpremap
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/default/dhcp3-server  /etc/default/dhcp3-server
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/default/rsync  /etc/default/rsync
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/squid3/squid.conf /etc/squid3/squid.conf
        cp --preserve --remove-destination ${INSTALL_DIR}/install/var/spool/cron/crontabs/root /var/spool/cron/crontabs/root
        cp --preserve --remove-destination ${INSTALL_DIR}/install/home/kickstart/screenrc /home/kickstart/screenrc
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/vsftpd.conf /etc/vsftpd.conf
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/default/samba  /etc/default/samba
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/init.d/local_etch.init /etc/init.d/local
	cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/firewall.conf /etc/firewall.conf
	cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/iptables /etc/network/if-pre-up.d/iptables
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/rsyncd.conf /etc/rsyncd.conf
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/samba/smb.conf /etc/samba/smb.conf

        # Apache
        #cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/apache2/apache2.conf /etc/apache2/apache2.conf
        #cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/apache2/httpd.conf /etc/apache2/httpd.conf
        ln -sf /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
        ln -sf /etc/apache2/sites-available/sbks.conf /etc/apache2/sites-enabled/000-default
        ln -sf ${INSTALL_DIR}/install/var/www/kickstart/public  /exports/httpdocs

        # Postgres
        POSTGRES_DIR=$(dirname `find /etc/postgresql -name postgresql.conf`)
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/postgresql/postgresql8.conf ${POSTGRES_DIR}/postgresql.conf
        cp --preserve --remove-destination ${INSTALL_DIR}/install/etc/postgresql/pg_hba.conf ${POSTGRES_DIR}/pg_hba.conf

        # sbadmin ssh stuff
        cp --preserve --remove-destination ${INSTALL_DIR}/install/home/sbadmin/.ssh /home/sbadmin/.ssh
        ln -sf /dev/null /home/sbadmin/.ssh/known_hosts

        echo -e "\nSetting up statuspipe.pl requirements...\n"

        # statuspipe.pl requires this or it won't run
        mkfifo -m600 /exports/kickstart/status/status.log
        chown root.install /exports/kickstart/status/status.log
        chmod 660 /exports/kickstart/status/status.log

        # statuspipe.pl also needs these direcotires
        chown root.install /exports/kickstart/status/{copy,part,kick}done

        echo -e "\nFixing ownership and permission issues...\n"

        # must enforce T permissions or scripts won't run
        chmod 1730 /var/spool/cron/crontabs

        # enforce permissions root only permissions
        chown root.crontab /var/spool/cron/crontabs/root
        chmod 600 /var/spool/cron/crontabs/root

        # enforce ownership, permissions on samba secrets
        chmod 600 /var/lib/samba/{secrets,passdb}.tdb
        chown root.root /var/lib/samba/{secrets,passdb}.tdb

        # Ensure permissions on /home/sbadmin
        chown -R sbadmin.sbadmin /home/sbadmin
        # only sbadmin can read his keys
        chown 600 /home/sbadmin/.ssh/id*

        # additional permissions to set because users were not created yet 
        chown --recursive root.postgres /etc/postgresql/

}

############################################################################
# post_install
#
#       This function is for things that need to be executed after the kickstart system is installed,
#       such as restarting services and executing initialization scripts
#
############################################################################
function post_install
{
        echo -e "\n===POST INSTALL===\n"        

        echo -e "\n\tMaking sure TFTP is not under inet's control..."
        perl -pi -e 's/^\s*tftp/#tftp/' /etc/inetd.conf
        invoke-rc.d openbsd-inetd restart
        echo -e "\n\tDone"

        echo -e "\n\tConfiguring $APACHE"
        # This script was designed to build the httpd.conf file
        APACHE_CONF_PATH="/etc/apache2/sites-available/sbks.conf"
        if [[ -e $APACHE_CONF_PATH ]] ; then
                echo -e "\n\t$APACHE_CONF_PATH already exists, not overwriting"
        else
                echo -e "\n\t$APACHE_CONF_PATH does not exist, generating new"
                ${INSTALL_DIR}/install/var/www/kickstart/configs/update_httpd.pl
        fi

		echo -e "\n\tConfiguring Proxy"
		SQUID_CONF_PATH="${INSTALL_DIR}/install/etc/squid3/squid.conf"
		SQUID_TEMPLATE=${INSTALL_DIR}/install/var/www/kickstart/configs/squid.conf_template
		if [[ -e $SQUID_CONF_PATH ]] ; then
                echo -e "\n\t$SQUID_CONF_PATH already exists, not overwriting"
        else
                echo -e "\n\t$SQUID_CONF_PATH does not exist, generating a new one"
		        sed "   s/@@DC_NUMBER@@/${DC_NUMBER}/g ; \
		                s/@@DC_NAME@@/${DC_NAME}/g ;    \
		                s/@@DC_ABBR@@/${DC_ABBR}/g ;    \
		                s/@@IPADDR@@/${IP_ADDR}/g  ;    \
		                s/@@NETWORK@@/${NETWORK}/g ;    \
		                s/@@NETMASK@@/${NETMASK}/g ;    \
		                s/@@GATEWAY@@/${GATEWAY}/g "    \
		                ${SQUID_TEMPLATE} > ${SQUID_CONF_PATH}
         fi

        echo -e "\n\tRestarting $POSTGRESQL database..."
        # restart postgres for new configuration
        POSTGRESQL_INIT=$(basename `ls /etc/init.d/postgresql*`)
        invoke-rc.d ${POSTGRESQL_INIT} restart

        # running this script clobbers the original file
        # should we bother to back up, probably not...
        /exports/kickstart/bin/dhcpconf.pl > /etc/dhcp3/dhcpd.conf

        echo -e "\n"
        invoke-rc.d sysklogd restart
        echo -e "\n"
        invoke-rc.d squid3 restart

        # update init.d subsystem to call this last and in runlevels 2,3,4 and 5
        update-rc.d local start 99 2 3 4 5 .
        
        # Execute /etc/init.d/local to initiate the system.  
        echo "\n\tBringing up the system..."
        invoke-rc.d local restart
        
}

############################################################################
# destroy_links
#
#       Destroy all links created during the install and code deployment
#
############################################################################
function destory_links 
{
        # Used in prep for a rollback.  This will destroy all links created during the installation process.  This will
        # leave the system in an unusable state and requires re-installing a new kickstart system
        rm -f /etc/hosts.allow
        rm -f /etc/syslog.conf
        rm -f /etc/default/tftpd-hpa
        rm -f /etc/default/dhcp3-server
        rm -f /etc/default/rsync
        rm -f /etc/default/samba
        rm -f /etc/init.d/local
        rm -f /etc/rsyncd.conf
        rm -f /etc/samba/smb.conf
        POSTGRES_DIR=$(dirname `find /etc/postgresql -name postgresql.conf`)
        rm -f /etc/postgresql/${POSTGRES_DIR}/postgresql.conf
        rm -f /etc/postgresql/${POSTGRES_DIR}/pg_hba.conf
        rm -f /etc/squid/squid.conf
        rm -f /var/spool/cron/crontabs/root
        rm -f /home/kickstart/screenrc
        rm -f /etc/vsftpd.conf
        rm -f /home/sbadmin/.ssh
        rm -f /exports/kickstart
        rm -f /exports/httpdocs
}

############################################################################
# pxe_link_fix
#
#       Does a search and replace for the KS_IPADDR variable and replaces it with
#		the private kickstart IP address. This is to enable certain PXE utilities
#
############################################################################
function pxe_link_fix 
{
    echo -e "\nConfiguring /tftpboot..."
	
	KS_ETH1=`perl -e 'use lib q(/exports/kickstart/lib); require q(sbks.pm); print "$Config->{ks_ipaddr}"'`

    echo -e "\n\tConfiguring PXE targets..."
    find /tftpboot/pxe/pxelinux.cfg/* -type f | xargs perl -pi -e "s/KS_IPADDR/$KS_ETH1/" 
	find /tftpboot/pxe/utils_mh/* -type f | xargs perl -pi -e "s/KS_IPADDR/$KS_ETH1/"
    echo -e "\tDone\n"

    echo -e "Done Configuring /tftpboot\n";
}

############################################################################
# Parse the options / flags sent to the script
############################################################################
while getopts "pstdaicrbh" OPTION; do
        case ${OPTION} in
                p )
                        if [ -n "$ENVIRONMENT" ]; then
                                usage "Error: More than one environment specified." 1;
                        fi
                        ENVIRONMENT="production"
                        ;;
                s )
                        if [ -n "$ENVIRONMENT" ]; then
                                usage "Error: More than one environment specified." 1;
                        fi
                        ENVIRONMENT="staging"
                        ;;
                t )
                        if [ -n "$ENVIRONMENT" ]; then
                                usage "Error: More than one environment specified." 1;
                        fi
                        ENVIRONMENT="testing"
                        ;;
                d )
                        if [ -n "$ENVIRONMENT" ]; then
                                usage "Error: More than one environment specified." 1;
                        fi
                        ENVIRONMENT="development"
                        ;;
                a )
                        if [ -n "$INSTALL_TYPE" ]; then
                                usage "Error: More than one install type specified." 1;
                        fi
                        INSTALL_TYPE="all"
                        ;;
                i )
                        if [ -n "$INSTALL_TYPE" ]; then
                                usage "Error: More than one install type specified." 1;
                        fi
                        INSTALL_TYPE="install"
                        ;;
                c )
                        if [ -n "$INSTALL_TYPE" ]; then
                                usage "Error: More than one install type specified." 1;
                        fi
                        INSTALL_TYPE="code"
                        ;;
                r )
                        if [ -n "$INSTALL_TYPE" ]; then
                                usage "Error: More than one install type specified." 1;
                        fi
                        INSTALL_TYPE="rollback"
                        ;;
                b )
                        if [ -n "$INSTALL_TYPE" ]; then
                                usage "Error: More than one install type specified." 1;
                        fi
                        INSTALL_TYPE="backup"
                        ;;
                h )
                        usage "" 0
                        ;;
                * )
                        usage "" 1
                        ;;
        esac
done



############################################################################
# 
# Execution Chain Starts Here
#
############################################################################

echo -e "\n" #so preddy

############################################################################
# Check to make sure valid options where selected.
# Exit if no environment or install type were specified
############################################################################
if [ -z ${ENVIRONMENT} ]; then
        usage "Error: No environment specified." 1
fi

if [ -z "$INSTALL_TYPE" ]; then
        usage "Error: No deploy type specified." 1
fi

############################################################################
# Do a couple prechecks to make sure that the right install type was selected
############################################################################

# Only root can run this script due to package installs and misc permissions issues.
if [ `whoami` != "root" ]; then
        usage "You must be root to run this script." 1
fi

# Check for some basic package dependancies
PACKAGES="$POSTGRESQL $APACHE $SAMBA $PERL $SCREEN $TFTPD $DHCPD"; 

# Warn user if they are doing a full install on an already setup system
if [[ $INSTALL_TYPE == "all" ]] ; then
        PRECHECK=`dpkg -l | grep -c " $POSTGRESQL" `
        if [[ $PRECHECK -gt 0 ]] ; then
                echo "$POSTGRESQL is already installed... "
        echo "Continuing with full install may result in data loss"
                echo -n "Do you wish to continue (y/N): "
                read -e CONTINUE;
                if [ -z "$CONTINUE" ] || [ "$CONTINUE" != 'y' ]; then
                        echo "User abort.";
                        exit 1;
                fi;
                CONTINUE=""
        fi
# Error if user is attempting to do a code deployment on a non-setup server
elif [[ $INSTALL_TYPE == "code" ]] ; then
        for PACKAGE in $PACKAGES ; do
                PRECHECK=`dpkg -l | grep -c " $PACKAGE"`
                if [[ $PRECHECK -lt 1 ]] ; then
                        echo "Missing dependant package $PACKAGE"
                        echo "Possibly meant to do a full install instead?"
                        exit 1;
                fi
        done
fi

# Check for PXE dependancies as these two projects are currently interrelated
if [[ ! -d "/tftpboot/pxe/pxelinux.cfg" ]] ; then
        echo -e "\nERROR: Missing dependant project: PXE"
        echo "Dependancy must be resolved before installation can proceed"
        echo -e "You might want to try 'svn checkout https://pforge.peer1.com/svnroot/pxe/trunk/tftpboot /tftpboot/'\n"
        exit 1
fi

############################################################################
# Gather kickstart system information
############################################################################
HOSTNAME=`hostname`
DC_ABBR=`hostname | awk -F. '{print $2}' | tr a-z A-Z`

if [[ $INSTALL_TYPE == "all" ]] ; then
        echo -n "Please enter the Datacenter number: "
        read -e DC_NUMBER
        if [[ ! ${DC_NUMBER} -gt 0 ]] ; then
                echo "Invalid DC Number: ${DC_NUMBER}"
        exit 1
        fi
        echo -n "Please enter a name for the Datacenter: "
        read -e DC_NAME
else
        DC_NUMBER=`su postgres -c "psql kickstart -tc \"select id from sb_datacenter limit 1\""`
        DC_NAME=`su postgres -c "psql kickstart -tc \"select name from sb_datacenter limit 1\""`
fi


############################################################################
# Gather network information
############################################################################
IP_ADDR=`/sbin/ifconfig eth0 | awk -F: '/inet addr/ {print $2}' | cut -d' ' -f1`
if [[ -z $IP_ADDR ]] ; then
        IP_ADDR=`/sbin/ifconfig eth1 | awk -F: '/inet addr/ {print $2}' | cut -d' ' -f1`
        IFACE="eth1"
else
        IFACE="eth0"
fi
NETWORK=`grep -A7 $IFACE /etc/network/interfaces | awk '/network +[0-9]+/ {print $2}'`
# In case the network is not defined in the network/interfaces file, try and get it from the
# route table.  Probably a better way to do this but I can't think of one right now
if [[ -z $NETWORK ]] ; then
        NETWORK=`route | grep -m1 $IFACE | cut -d" " -f1`
fi
NETMASK=`grep -A7 $IFACE /etc/network/interfaces | awk '/netmask/ {print $2}'`
GATEWAY=`grep -A7 $IFACE /etc/network/interfaces | awk '/gateway/ {print $2}'`

############################################################################
# Get the install directory.  
############################################################################
# In case of a symlink we'd rather have the actual path in case the link changes.
INSTALL_DIR=`dirname $0`
cd ${INSTALL_DIR}
INSTALL_DIR=`pwd`

############################################################################
# Show info about the environment, what we are going to do and prompt the user to continue.
############################################################################
clear
echo -e "SYSTEM INFORMATION"
echo -e "\tHostname        : ${HOSTNAME}";
echo -e "\tDatacenter Abbr : ${DC_ABBR}";
echo -e "\tDatacenter ID   : ${DC_NUMBER}";
echo -e "\tDatacenter Name : ${DC_NAME}";
echo
echo -e "NETWORK INFORMTATION"
echo -e "\tIP address      : ${IP_ADDR}";
echo -e "\tNetwork         : ${NETWORK}";
echo -e "\tNetmask         : ${NETMASK}";
echo -e "\tGateway         : ${GATEWAY}";
echo
echo -e "INSTALLATION INFORMATION"
echo -e "\tInstall dir     : ${INSTALL_DIR}";
echo -e "\tEnvironment     : ${ENVIRONMENT}";
echo -e "\tInstall type    : ${INSTALL_TYPE}";
echo -e "\tKernel          : $(uname -r)"
echo
echo
echo -n "Are you sure you want to continue? (y/N) ";
read -e CONTINUE;
if [ -z "$CONTINUE" ] || [ "$CONTINUE" != 'y' ]; then
        echo "User abort.";
        exit 1;
fi;

############################################################################
# Install required packages for kickstart
############################################################################
if [ "$INSTALL_TYPE" == "install" ] || [ "$INSTALL_TYPE" == "all" ]; then
        install_packages
fi

############################################################################
# Setup accounts and the database
############################################################################
if [ "$INSTALL_TYPE" == "all" ]; then
        pre_install
        db_setup
fi

############################################################################
# Installation of code starts here
############################################################################
if [ "$INSTALL_TYPE" == "code" ] || [ "$INSTALL_TYPE" == "all" ]; then
        build_links
        post_install
		pxe_link_fix
fi

############################################################################
# For rollbacks, just break the created links for now and force the user to
# execute the install script of the version they want to install. The
# rollback functionality was never implemented properly, commenting out
# the call that leaves the system in an unusable state.
############################################################################
if [ "$INSTALL_TYPE" == "rollback" ]; then
        #destory_links
        #echo "System left in an unusable state...please execute install of another version"
        echo "Rollback functionality not yet implemented"
fi;

############################################################################
# Backup functionality is not implemented.
############################################################################
if [ "$INSTALL_TYPE" == "backup" ]; then
        echo "Backup functionality not yet implemented"
fi;
