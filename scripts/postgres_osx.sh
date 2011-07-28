#!/bin/bash

# File: configure_osx
# Package: N/A
# Install Location: N/A
# Name: Automated Script to Configure Mac OS X
#
# Supported Platforms: Mac OS X >= 10.5.0 (Leopard or Above)
#
# Description: This script is intended for use on Mac OS X only.
# The script facilitiates the installation of Mac Ports, configures paths,
# installs postgres, and configures everything else required for installation.
#
# Pre Requisites: xCode =>3.2 (For GCC)
#
# Author: Adam Hubscher <ahubscher AT cashnetusa DOT com>
# Version: 1.0
# Last Updated: July 20th, 2011
# Revision: 1

echo "Welcome to the OS X configuration script."
echo "This script will automate the installation and configuration of your Mac system."
echo "This script relies upon sudo. You may be prompted for your password, more than once."
echo ""

# Fire off sudo once, to queue up the password auth. This is to save us from having to enter a passwd more than one.

sudo echo "Beginning configurationg of Mac OS X for Postgres 8.3 Development Environment."

## Variables ##

PROFILE="~/.profile"
BASHRC="~/.bashrc"

# MacPorts Paths #
MPORTBIN="/opt/local/bin"
MPORTSBIN="/opt/local/sbin"

# Postgres Variables #

PGBIN="/opt/local/lib/postgresql83/bin"
PGCONTRIB="/opt/local/share/postgresql83/contrib"
PGDATA="/opt/local/var/db/postgresql83/defaultdb"
PGCONFIG="/opt/local/lib/postgresql83/bin/pg_config"

## Installation and Configuration of the System ##

USER=$( whoami )
PWD=$( pwd )

# Environment Configuration

if [[ ! -f ${PROFILE} ]]
then
(
cat <<'EOF'
#!/bin/bash
# Begin Local Profile

# Apple does not automatically source a bashrc. This can cause expected environment variables to not exist.
. ~/.bashrc

# End ~/.profile
EOF
) > ${PROFILE}
else
  # If it already exists, just append it with a single line
  echo ". ~/.bashrc" >> ${PROFILE}
fi

# Create the Bash RC File

if [[ ! -f ${BASHRC} ]]
then
  touch ${BASHRC}
  echo "#!/bin/bash" >> ${BASHRC}
  echo "# This file contains environment configurations loaded upon login." >> ${BASHRC}
  echo "# Edit this file for per-user configurations." >> ${BASHRC}
fi

# Configure bashrc with the new environment variables

echo "export PATH=\$PATH:${PGBIN}:${PGCONTRIB}:${MPORTBIN}:${MPORTSBIN}:~/bin" >> ${BASHRC}
echo "export PGDATA=${PGDATA}" >> ${BASHRC}

# Now that we've done all this, we need these variables.

. ~/.bash_rc

## MacPorts Section ##

# Check if the port command does not exist
if [[ ! -f "${MPORTBIN}/port" ]]
then
  
  # If it does not exist, let's install macports.
  # Move to the temporary directory
  cd /tmp

  # Grab the latest version. Subversion is version independent.
  svn co http://svn.macports.org/repository/macports/trunk macports

  # Move to the directory, build and install it.
  cd /tmp/macports

  # Configure the build
  ./configure --enable-readline

  # Build and Install Mac Ports
  make
  sudo make install

  # Never be sure of anything. Test that we succeeded in the installation.
  if [[ -f "${MPORTBIN}/port" ]]
  then
    echo "MacPorts Installed."
    ${MPORTBIN}/port --version
  else
    echo "MacPorts Installation Failed. Check the build log."
    exit 1
  fi
fi

## Beyond this point we install and configure Postgres, and load the DB. ##

echo "Initial configuration is complete."
echo "Any further steps will produce a fresh install of postgres."
echo "This process deletes any existing databases and removes any previous settings."
echo "Continue? (Yes/No)"

read cont

if [[ $cont -eq "Yes" ]]
then
  
  echo "Continuing with Installation"

  echo "Cleanup Process. This deletes any existing postgres install."
  # Stop existing databases
  sudo su postgres -c '/opt/local/lib/postgresql83/bin/pg_ctl -D /opt/local/var/db/postgresql83/defaultdb -m smart stop'

  # Prepare a fresh MacPorts Environment
  sudo port uninstall autoconf ghostscript glib2 help2man irssi p5-locale-gettext texi2html graphvis groff cairo gts pango
  sudo port deactivate perl5.8
  sudo port uninstall perl5.8
  sudo port deactivate postgresql83-server
  sudo port uninstall postgresql83-server
  sudo port deactivate py27-psycopg2 @2.4.1_0+postgresql83
  sudo port uninstall py27-psycopg2 @2.4.1_0+postgresql83
  sudo port deactivate postgresql83
  sudo port uninstall postgresql83

  # PERL Installation [REQUIRED]
  sudo port deactivate perl5.12
  sudo port uninstall perl5.12
  sudo port install perl5.12 +shared +threads

  # gsed (runtest)
  supo port install gsed

  # gmake (check_updates)
  sudo port install gmake

  # libraries (skytools)
  sudo port install asciidoc xmlto xml2

  # Postgresql Server 8.3 [REQUIRED]
  sudo port install postgresql83-server +perl
  sudo port -fn upgrade postgresql83

  # Additional Python/Postgres Modules [REQUIRED]
  sudo port install py27-psycopg2 @2.4.1_0+postgresql83

  # Initialize Postgres and Initial Database
  sudo mkdir -p /opt/local/var/db/postgresql83/defaultdb
  sudo chown postgres:postgres /opt/local/var/db/postgresql83/defaultdb
  sudo su postgres -c '/opt/local/lib/postgresql83/bin/initdb -D /opt/local/var/db/postgresql83/defaultdb'


  # Start the new instance

  sudo su postgres -c '/opt/local/lib/postgresql83/bin/pg_ctl -D /opt/local/var/db/postgresql83/defaultdb -l /opt/local/var/log/postgresql83/postgres.log start'

  # Wait for server to finish starting before sending requests
  sleep 1

  # Create dev user
  sudo su postgres -c '/opt/local/lib/postgresql83/bin/createuser -s -d -r -l -i ${USER}'

  # First, check_updates. We need to move the directory to the contrib directory and then build it.
  mv ${PWD}/check_update ${PGCONTRIB}
  cd ${PGCONTRIB}/check_update

  # Build the check_updates package
  sudo gmake USE_PGXS=1 install

  cd ${PWD}

  # Next we need to install skytools
  mv ${PWD}/skytools ${PGCONTRIB}
  cd ${PGCONTRIB}/skytools

  # With Mac OS X, skytools can have a hard time finding the pg_config utility.
  # Passing it to configure solves nearly all build problems
  sudo ./configure --with-pgconfig=${PGCONFIG}
  sudo gmake install

  ## SysCtl.conf ##

  # This part updates sysctl.conf to contain appropriate settings for postgres
  if [[ -f /etc/sysctl.conf ]]
  then
    echo "sysctl.conf already exists."
    echo "Please place the following into the file if the options do not already exist."
    echo <<'EOF'
kern.sysv.shmmax=33554432
kern.sysv.shmmin=1
kern.sysv.shmmni=256
kern.sysv.shmseg=64
kern.sysv.shmall=8192
EOF
    echo "If these options did not exist, you will need to reboot your sytem."
  else
    echo "Configuring sytem kernel option controls."
(
sudo cat <<'EOF'
kern.sysv.shmmax=33554432
kern.sysv.shmmin=1
kern.sysv.shmmni=256
kern.sysv.shmseg=64
kern.sysv.shmall=8192
EOF
) > /etc/sysctl.conf
    echo "sysctl.conf has been modified. These seetings require a reboot to take effect."
  ## Done ##

  # Import CNU Postgres Server Configs (Included in Package)

  cp -f ./etc/postgresql/8.3/pg_hba.conf ${PGDATA}/pg_hba.conf
  cp -f ./etc/postgresql/8.3/postgresql.conf ${PGDATA}/postgresql.conf
  
  # Restart postgres with the newly added postgres

  sudo su postgres -c '/opt/local/lib/postgresql83/bin/pg_ctl -D /opt/local/var/db/postgresql83/defaultdb -l /opt/local/var/log/postgresql83/postgres.log start'

else
  echo "Installation has been stopped. Exiting"
  exit 1
fi

## This section is for setting up the DB ##

echo "Installation of Postgres is complete."
echo "The next steps are to install the database. You can do this on your own."
echo "Directions are found on the wiki."
echo "Be sure to reboot if any changes to sysctl.conf had to be made."
echo "IF you do not reboot, you will fail to import the US database."