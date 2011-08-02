#!/bin/bash
############################################################################
# Filename: install.sh
#
# This bash script can be used to deploy a kickstart server from scratch.
# It works as a wrapper for the respective configure_*.sh script. This
# file should copied by itself to the destination system and executed. It
# is not necessary to export/checkout the kickstart tree.
#
# Author: Carlos Avila
# Some of the subs are copied over from Kevin's script.
#
# Date: Apr 10th, 2008
#
# changelog:
# cavila@2008-10-20:  * initial file
# cavila@2008-06-25: Lets move away from using /opt/trunk.
# Doesn't make too much sense 
#
############################################################################

############################################################################
# Globals
#
#       Global variables used on the script
#
############################################################################

INSTALLS=( "Sarge" "Etch" "Winstart" "UPI" );

#tftpboot
TFTPBOOT_PFORGE="https://pforge.peer1.com/svnroot/pxe/trunk/tftpboot"
TFTPBOOT_PATH="/tftpboot"

#Kickstart
KICKSTART_PFORGE="https://pforge.peer1.com/svnroot/kickstart/trunk"
KICKSTART_PATH="/opt/kickstart"

#Winstart
WINSTART_PFORGE="https://pforge.peer1.com/svnroot/kickstart-webui/trunk/winstart"
WINSTART_PATH="/var/www/winstart"

#UPI
UPI_PFORGE="https://pforge.peer1.com/svnroot/kickstart-webui/trunk/kickstart-upi"
UPI_PATH="/opt/kickstart-upi"

############################################################################
# usage
#       Print a usage statement and exit
############################################################################
function usage 
{
    echo "Usage: $0 {-p|-s|-t|-d} ... {-a|-i|-c|-r|-b} ... [-h]"
    echo "-h    help"
    echo
    echo "Environment to build (one required)"
    echo "-p    production server"
    echo "-s    staging server"
    echo "-t    testing server"
    echo "-d    development server"
    echo
    echo "Type of deployment (one required)"
    echo "-a    all            (full install - package install, DB setup and code)"
    echo "-i    installation       (package installation only)"
    echo "-c    code/config deployment (code install only)"
    echo "-r    rollback           (revert code and configs)"
    echo "-b    backup         (backup current config -- not yet implemented)"
    if [ "$1" != "" ]; then
        echo
        echo $1
    fi
    exit $2
}


############################################################################
# checkout_or_die
#       Helper to checkout project using svn
############################################################################
function checkout_or_die
{

    echo -e "\n\tChecking out ${2} to ${3}"
    if [ ! -e ${3} ]; then
        svn checkout -r 'HEAD' --username ${1} ${2} ${3}
    else
        echo -e "\tFound destination, rewritting..."
        svn checkout -r 'HEAD' --username ${1} ${2} ${3}
    fi

    if [ $? -ne 0 ]; then
        echo -e "\tERROR: Unable to properly checkout from Pforge (wrong password?)\n"
        exit 2;
    fi
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
# Install
#    Here is where the fun starts
############################################################################

echo -e "\nThis script will attempt to deploy kickstart on this system."
echo -e "You can stop at any time by pressing control-c.\n"

echo "Please select a version of Kickstart to install:"
for (( i = 0 ; i < ${#INSTALLS[@]} ; i++ )) do
  echo "[$i] : ${INSTALLS[$i]}"
done
echo -n ":"
read -e KICKSTART

if [ -z "$KICKSTART" ] || \
    [ $KICKSTART -lt 0 ] || \
    [ $KICKSTART -gt $((${#INSTALLS[@]}+1)) ] ; then
  echo "User abort.";
  exit 1;
fi;

echo -e "\nUpdating all packages installed...\n"
sleep 1;
apt-get -y update

echo -e "\nMaking sure that we have the basic tools we need..."
sleep 1;
apt-get -y install subversion ssh

echo -e "\nI'm going to need to download software from Pforge..."
echo -ne "\tPlease enter your Pforge username:"
read -e USERNAME

case ${INSTALLS[$KICKSTART]} in

    "Sarge" )
        INSTALLER="${KICKSTART_PATH}/configure.sh"
        checkout_or_die ${USERNAME} ${TFTPBOOT_PFORGE} ${TFTPBOOT_PATH}
        checkout_or_die ${USERNAME} ${KICKSTART_PFORGE} ${KICKSTART_PATH}

        echo -e "\nExecuting $INSTALLER..."
        sh $INSTALLER $*
        ;;

    "Etch" )
        INSTALLER="$KICKSTART_PATH/configure_etch.sh"
        checkout_or_die ${USERNAME} ${TFTPBOOT_PFORGE} ${TFTPBOOT_PATH}
        checkout_or_die ${USERNAME} ${KICKSTART_PFORGE} ${KICKSTART_PATH}

        echo -e "\nExecuting $INSTALLER..."
        sh $INSTALLER $*
        ;;

    "Winstart" )
        checkout_or_die ${USERNAME} ${TFTPBOOT_PFORGE} ${TFTPBOOT_PATH}
        checkout_or_die ${USERNAME} ${KICKSTART_PFORGE} ${KICKSTART_PATH}
        checkout_or_die ${USERNAME} ${WINSTART_PFORGE} ${WINSTART_PATH}

        INSTALLER="$KICKSTART_PATH/configure_etch.sh"
        echo -e "\n\nExecuting $INSTALLER..."
        sh $INSTALLER $*

        INSTALLER="$KICKSTART_PATH/configure_winstart.sh"
        echo -e "\n\nExecuting $INSTALLER..."
        sh $INSTALLER $*
        ;;

    "UPI" )
        checkout_or_die ${USERNAME} ${TFTPBOOT_PFORGE} ${TFTPBOOT_PATH}
        checkout_or_die ${USERNAME} ${KICKSTART_PFORGE} ${KICKSTART_PATH}
        checkout_or_die ${USERNAME} ${WINSTART_PFORGE} ${WINSTART_PATH}
        checkout_or_die ${USERNAME} ${UPI_PFORGE} ${UPI_PATH}

        INSTALLER="$KICKSTART_PATH/configure_etch.sh"
        echo -e "\n\nExecuting $INSTALLER..."
        sh $INSTALLER $*

        INSTALLER="$KICKSTART_PATH/configure_upi.sh"
        echo -e "\n\nExecuting $INSTALLER..."
        sh $INSTALLER $*
        ;;
    * )
        #this code should be unreachable
        echo "WARNING:This script may have errors."
        echo "Distribution not supported."
        ;;
esac

echo -e "\n\nAll Done!\n";
