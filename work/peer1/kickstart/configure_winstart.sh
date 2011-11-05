#!/bin/bash

############################################################################
# Filename: configure_winstart.sh
#
# This bash script can be used to reconfigure a kickstart server into
# a Winstart server.
#
# Author: Carlos Avila
#
# Date: May 16th, 2008
# https://pforge.peer1.com/svnroot/kickstart/trunk/exports/kickstart/configure_winstart.sh
#
# changelog:
# cavila@2008-06-16:    * initial file
# oschroeder@2009-11-09 added replacing KS_IPADDR var in pxe/utils_mh/utils.cfg
#
############################################################################

############################################################################
# globals
#
# 	Global variables that configure the script.
#
############################################################################

#default values
HOSTNAME=`hostname`
DC_ABBR=`hostname | awk -F. '{print $2}' | tr a-z A-Z`
DC_NAME="Unknown"
DC_NUMBER="0"
PFORGE_USERNAME=`whoami`

#software that should be removed
#Mostly legace of when Kickstart was sarge based
CLEAN_PKGS="atftp atftpd apache apache-common apache-utils"

#software that should be installed
INSTALL_PKGS="apache2 apache2-common apache2-mpm-prefork apache2-utils \
              tftpd-hpa tftpd-hpa libapache2-mod-php5 libapache2-mod-ldap-userdir \
              php5-common php5-ldap php5-cli php5-odbc php5-pgsql php5-sqlite \
              php5 php5-odbc debootstrap fakeroot libncurses5-dev libkrb53 \
              libcurl3 curl libpq3 libct1 samba samba-common bind9"

#url for Winstart-WebUI
WINSTART_PFORGE="https://pforge.peer1.com/svnroot/kickstart-webui/trunk/winstart"
WINSTART_PATH="/var/www/winstart"

KICKSTART_INSTALL="/opt/kickstart"
KICKSTART_PATH="/exports/kickstart"
KICKSTART_INIT="/etc/init.d/local"
KICKSTART_SCREEN="-d -m -c /exports/kickstart/configs/screenrc"

APACHE_PATH="/etc/apache2"

############################################################################
# usage
# 	Print a usage statement and exit
############################################################################
function usage 
{
	echo "Usage: $0 {-p|-s|-t|-d} ... {-a|-i|-c|-r|-b} ... {-e [arg] -l [arg] -u [arg]} ... [-h]"
	echo "-h	help"
	echo

	if [ "$1" != "" ]; then
		echo
		echo $1
	fi
	exit $2
}

############################################################################
# configure_apache
############################################################################

function configure_apache
{
    echo -e "\nConfiguring Apache..."

    echo -e "\n\tDisabling Apache2 default site..."
    if [ -e ${APACHE_PATH}/sites-enabled/000-default ]; then
	rm -rf ${APACHE_PATH}/sites-enabled/000-default
    fi
    echo -e "\tDone"

    echo -e "\n\tGenerating and enabling portals..."
    for portal in winstart winstart-ssl; do
	    if [ -e ${APACHE_PATH}/sites-available/${portal} ]; then
		echo -e "\t${portal} portal found, skipping configuration"
	    else
	        sed "s/@@KS_PUBLIC_IPADDR@@/${KS_ETH0}/g ; \
	             s/@@KS_HOST@@/$(hostname)/g ;    \
	             s/@@KS_IPADDR@@/${KS_ETH1}/g"     \
	             ${KICKSTART_PATH}/configs/templates/apache2/sites-available/${portal} > ${APACHE_PATH}/sites-available/${portal};
                echo -e "\t${APACHE_PATH}/sites-available/${portal} has been configured";
	    fi
    done

    echo -e "\n\tCreating apache symlinks to activate winstart portals...";
    for portal in winstart winstart-ssl; do
	ln -sf ${APACHE_PATH}/sites-available/${portal} ${APACHE_PATH}/sites-enabled/${portal};
    done
    echo -e "\tDone"
    
    echo -e "\n\tCreating modules symlinks..."
    ln -sf ../mods-available/ssl.conf ${APACHE_PATH}/mods-enabled/ssl.conf
    ln -sf ../mods-available/ssl.load ${APACHE_PATH}/mods-enabled/ssl.load
    ln -sf ../mods-available/rewrite.load ${APACHE_PATH}/mods-enabled/rewrite.load
    echo -e "\tDone"

    echo -e "\n\tCopying winstart.pem for ssl site..."
    if [ ! -e ${APACHE_PATH}/ssl ]; then mkdir ${APACHE_PATH}/ssl; fi
    cp -ap /exports/kickstart/configs/certificates/winstart.pem ${APACHE_PATH}/ssl/
    echo -e "\tDone"


    if [ `grep -c 443 ${APACHE_PATH}/ports.conf` == 0 ]; then
    	echo -e "\n\tAdding Listen 443 to ${APACHE_PATH}/ports.conf..."
     	echo "Listen 443" >> ${APACHE_PATH}/ports.conf
     	echo -e "\tDone"
    fi

    echo -e "\n\tEnabling SSL support..."
    a2enmod ssl;
    echo -e "\tDone"

    echo -e "\n\tTesting final configuration..."
    apache2ctl configtest
    if [ $? == 0 ]; then
    	apache2ctl restart
        echo -e "\tDone"
    else 
    	echo -e "\tConfiguration problem found in Apache2"
    fi

    echo -e "\nDone Configuring Apache"
}


############################################################################
# configure_samba
############################################################################

function configure_samba
{
    echo -e "\nConfiguring Samba..."
    
    echo -e "\n\tAdding 'provision' user (*nix account)..."
    useradd -m -g provision -d /home/provision -s /bin/false provision
    if [[ $? == 9 ]]; then
    	echo -e "\tThe 'provision' user already exists"
    fi
    echo -e "\tDone"

    echo -e "\n\tAdding 'provision' user (Samba account)..."
    echo -e 'pr0vi$10N\npr0vi$10N' | smbpasswd -a provision -s 
    if [[ ! $? == 0 ]]; then
    	echo -e "\tFailed to create new account"
    fi
    echo -e "\tDone"
    
    echo -e "\nDone Configuring Samba\n"

}

############################################################################
# configure_bind
# 	Configure Bind for local DNS cache
############################################################################
function configure_bind
{
    echo -e "\nConfiguring Bind..."
    if [[ `egrep -c "allow-query\s*{.*localnets;.*}" /etc/bind/named.conf.options` == 1 ]]; then
	echo -e "\nIt appears that Bind was previously configured, skipping."; 
    else
	echo -e "\n\tReplacing named.conf.options.";
	echo -e "options {
        directory \"/var/cache/bind\";
        fetch-glue no;
        listen-on { localhost; };
        allow-query { localnets; };\n};" > /etc/bind/named.conf.options

    fi
    
    echo -e "\n\tAdding localhost to resolv.conf"
    echo 'nameserver 127.0.0.1' >> /etc/resolv.conf 
    echo -e "\tDone"

    echo -e "\nDone Configuring Bind"
}

############################################################################
# configure_network
#	Winstart requires the 2nd NIC to have a static IP.
############################################################################
function configure_network
{
    echo -e "\nConfiguring Network..."
    if [ ! `egrep -c "iface eth1 inet static" /etc/network/interfaces` == 0 ]; then
        echo -e "\n\tEth1 is already configured, please check it has the proper IP:";
        grep -A8 'iface eth1 inet static' /etc/network/interfaces
    else
        echo -e "\n\tAppending static IP...";
	cat >> /etc/network/interfaces <<EOF

# The secondary network interface
allow-hotplug eth1
auto eth1
iface eth1 inet static
    address ${KS_ETH1}
    netmask ${KS_ETH1_NETMASK}
    network ${KS_ETH1_NETWORK}
    broadcast ${KS_ETH1_BROADCAST}
EOF
    fi
    
    echo -e "\n\tRemoving VLANS..."
    for i in `ifconfig | grep vlan | awk '{print$1}'`; do
        vconfig rem ${i}
    done 
    echo -e "\tDone"

    echo -e "\n\tRestarting Networking..."
    /etc/init.d/networking stop #restart can't be trusted in debian
    /etc/init.d/networking start
    ifup eth1 #debian init script sucks
    echo -e "\tDone"
    
    echo -e "\nDone Configuring Network\n"
}


############################################################################
# configure_postgresql
############################################################################

function configure_postgresql
{
    echo -e "\nConfiguring the database..."
    
    echo -e "\n\tLet's make sure the service is running..."
    invoke-rc.d postgresql-8.1 restart
    echo -e "\tDone"
    
    echo -e "\n\tInserting schema..."
    su postgres -c "psql -f \"${WINSTART_PATH}/schema/winstart_schema.sql\" kickstart"
    echo -e "\tDone"
  
    echo -e "\n\tInserting data..."
    su postgres -c "psql -f \"${WINSTART_PATH}/schema/winstart_data.sql\" kickstart"
    echo -e "\tDone"

    echo -e "\nDone Configuring the database"
}

############################################################################
# get_kickstart_info
############################################################################

function get_kickstart_info
{
    echo -e "\nParsing Kickstart's Info..."

    echo -e "\nPlease enter the directory where kickstart is currently installed"
    echo -en "[${KICKSTART_PATH}]:"
    read -e NEW_PATH
    if [ ! -z $NEW_PATH ]; then
        KICKSTART_PATH=$NEW_PATH
    fi


    if [ -e ${KICKSTART_PATH}/lib/SB/Config.pm ]; then
        echo -e "\n\tFound Kickstart at ${KICKSTART_PATH}"
    else
        echo -e "\n\tKickstart not found at ${KICKSTART_PATH}";
        echo -e "Failed\n"
        exit 4;
    fi
   
    echo -e "\tParsing some necessary values..."
    DC_ABBR=`awk '/dc_abbr/{print $3}' ${KICKSTART_PATH}/lib/SB/Config.pm | sed -r s'/"|,//g'`
    DC_NUMBER=`awk '/dc_number/{print $3}' ${KICKSTART_PATH}/lib/SB/Config.pm | sed -r s'/"|,//g'`
    KS_ETH0=`perl -e 'use lib q(/exports/kickstart/lib); require q(sbks.pm); print "$Config->{ks_public_ipaddr}"'`

    KS_ETH1=`perl -e 'use lib q(/exports/kickstart/lib); require q(sbks.pm); print "$Config->{ks_ipaddr}"'`
    KS_ETH1_NETWORK=`echo ${KS_ETH1} | cut -d. -f 1,2,3`.0/24
    KS_ETH1_BROADCAST=`echo ${KS_ETH1} | cut -d. -f 1,2,3`.255
    KS_ETH1_NETMASK='255.255.255.0'
    echo -e "\tDone\n"

    echo -e "Done Parsing Kickstart's Info\n"
}

############################################################################
# stop_kickstart
#
# 	Stop all provisioning and shutdown all services.
#
############################################################################
function stop_kickstart
{
    echo -e "\nStopping kickstart"
    
    echo -e "\n\tStopping provisioning..."
    ${KICKSTART_INIT} stop
    if [ $? -ne 0 ]; then
        echo -e "\tFailed to stop services automatically. Trying 'manually'"
        for i in dhcp3-server atftpd tftpd-hpa apache apache2 rsync samba ; do
            echo -e "\tStop ${i}"
            /etc/init.d/${i} stop
        done
    fi
    echo -e "\tDone"
        
    echo -e "\n\tKilling screen sessions..."
    for i in `ps -ef | grep "SCREEN ${KICKSTART_SCREEN}" | awk ' ! /grep/ {print $2}'` ; do
        kill -9 ${i}
    done
    screen -wipe
    echo -e "\tDone"
    
    echo -e "\nDone Stopping kickstart\n"
}

############################################################################
# start_kickstart
#
#       Start all provisioning and shutdown all services.
#
############################################################################
function start_kickstart
{
    echo -e "\nStarting kickstart"

    echo -e "\n\tStart provisioning..."
    ${KICKSTART_INIT} restart
    if [ $? -ne 0 ]; then
	echo -e '\tFailed to restart services!! something is not right'
        exit 2
    fi
    echo -e "\tDone"

    echo -e "\nDone Starting kickstart\n"
}

############################################################################
# clean_packages
#
# 	Remove packages that are either obsolete or not necessary for winstart.
#
############################################################################
function clean_packages
{
    echo -e "\nRemoving obsolete packages...\n"
    aptitude remove -yr $CLEAN_PKGS;
    echo -e "\nDone Removing obsolete packages\n"
}

############################################################################
# install_packages
#
# 	Install packages necessary for winstart.
#
############################################################################
function install_packages
{
    echo -e "\nInstalling new packages..."
    aptitude install -yr $INSTALL_PKGS;
    echo -e "Done Installing new packages\n"
}

############################################################################
# configure_tftpboot
#
############################################################################
function configure_tftpboot 
{
    echo -e "\nConfiguring /tftpboot..."

    echo -e "\n\tConfiguring PXE targets..."
    find /tftpboot/pxe/pxelinux.cfg/* -type f | xargs perl -pi -e "s/KS_IPADDR/$KS_ETH1/" 
	find /tftpboot/pxe/utils_mh/* -type f | xargs perl -pi -e "s/KS_IPADDR/$KS_ETH1/"
    echo -e "\tDone\n"

    echo -e "\n\tConfiguring symlinks..."
    ln -sf default_mh /tftpboot/pxe/pxelinux.cfg/default
    ln -sf bootmenu_mh.txt /tftpboot/pxe/bootmenu.txt
    echo -e "\tDone\n"
    

    echo -e "Done Configuring /tftpboot\n";
}


############################################################################
# build_links
#
#       Create misc links to accommodate the behavior of Winstart.
#
############################################################################
function build_links
{
    echo -e "\nBuilding Symlinks"
    
    #/exports/kickstart/taskfiles.new
    ln -sf audit.txt /exports/kickstart/taskfiles.new/default.txt
    cp --preserve --remove-destination ${KICKSTART_INSTALL}/install/etc/init.d/local_winstart.init /etc/init.d/local 
    cp --preserve --remove-destination ${KICKSTART_INSTALL}/install/etc/firewall.conf /etc/firewall.conf
    cp --preserve --remove-destination ${KICKSTART_INSTALL}/install/etc/iptables /etc/network/if-pre-up.d/iptables
    echo -e "Done Building Symlinks\n";
}

############################################################################
# repair_permissions
#
#       Overall access level adjustments.
#
############################################################################
function repair_permissions
{
    echo -e "\nRepairing Permissions"
    chmod 755 /exports/kickstart/cgi-bin/*.cgi    

    echo -e "Done Repairing Permissions\n";
}

############################################################################
# Install
#       Here is where the fun starts
############################################################################

echo -e "\nThis script will attempt to deploy Winstart on this system."
echo -e "You can stop at any time by pressing control-c.\n"

sleep 2

echo '###################################'
echo '# Pre-install'
echo '################################'
stop_kickstart
get_kickstart_info
clean_packages


echo '###################################'
echo '# Install'
echo '################################'
install_packages
configure_network
configure_apache
configure_samba
configure_bind
configure_postgresql
configure_tftpboot

echo '###################################'
echo '# Post-install'
echo '################################'
build_links
repair_permissions
start_kickstart
