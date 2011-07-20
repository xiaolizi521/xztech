#!/bin/bash

# File: tomcat_rotate.sh
# Package: N/A
# Install Location: /opt/adfrontiers/contrib
# Name: Tomcat Log Rotation
#
# Supported Platforms: N/A
#
# Description: Tomcat has poor log rotation. Currently the only way
# for us to rotate a log is via restart of the tomcat process.
# We have resolved problems that have resulted in a lack of need
# to perform so many restarts. This script performs three tasks.
#
# 1) Appends what is in the LIVE file to the NON-LIVE LOG file.
# Zeros out the LIVE file.
#
# The reason for this, is that tomcat opens a file pointer to
# catalina.out that is unresolved if you delete it or move it.
# This can mean that if you do so, it will continue writing to
# the non existent pointer. This will consume "invisible" space.
# This avoids that, as we leave the file alone. Once the append is
# complete, we zero out the live log file, leaving the pointer
# alone. The advantage to this, is that we maintain as much
# of the actual log as possible. If we were to directly
# compress from the live log file, we would potentially lose
# items that are added while the log is added.
#
# 2) Compresses NON-LIVE LOG file when it reaches a certain size.
#
# 3) Performs maintenance on the log archive, keeping it small and clean.
#
# Author: Adam Hubscher <offbeatadam AT gmail DOT com>
# Version: 1.0
# Last Updated: July 19th, 2011
# Revision: 1

# Logs Location
LOGPATH="/usr/tomcathome/logs"
ARCHPATH="/usr/tomcathome/logs/archive"

# Current Date & Time for Archiving Purposes (If required)
STAMP=`date +%Y%m%d_%H.%M`

# Make sure the log locations are created
mkdir -p "${ARCHPATH}"

# Filename to be rotated
FILE="${LOGPATH}/catalina.out"
DESTFILE="${LOGPATH}/core2.production.log"

# Temporary Message Storage for Emailing

MSGTMP="/tmp/msg.txt"
touch "${MSGTMP}"
# Just in case, let's prep the file.

if [[ ! -f "${DESTFILE}" ]]
then
  
  touch "${DESTFILE}"
  chown tomcat:production "${DESTFILE}"
  chmod +rw "${DESTFILE}"

fi

## Process current catalina.out and deal with it ##

if [[ -f "${FILE}" ]]
then

  echo "Appending ${FILE} to ${DESTFILE}"

  # Mark the file. This is good for debugging.

  echo "******* Timestamp: ${STAMP} **********" >> "${DESTFILE}"
  # Append the log file to the destination file.
  cat "${FILE}" >> "${DESTFILE}"

  echo "Emptying ${FILE}"
  # Zero out the log file, freeing space.
  cat /dev/null > "${FILE}"

fi

## Now, check the destination file, and compress when needed. ##

if [[ -e "${DESTFILE}" ]]
then
  
  # Grab the filesize
  FILESIZE=`stat -c%s ${DESTFILE}`

  # Let's be reasonable. 500MB is good enough.
  # If a file reaches 500MB, it's time to compress.
  # The file will reach much larger beforehand.
  if [[ "${FILESIZE}" -gt 524288000 ]]
  then
    
    # We want to keep tabs on this, so create a message.
    echo "The tomcat log is too large. Compressiong is beginning." >> "${MSGTMP}"

    # Compress the file. We will use tar & bzip2.
    ARCHFILE="core2.production.log.${STAMP}.tar.bz2"
    echo "Compressing ${DESTFILE} to ${ARCHFILE}"
    tar cvjf "${ARCHPATH}/${ARCHFILE}" "${DESTFILE}"

    echo "Archiving complete. Emptying fil" >> "${MSGTMP}"
    echo "Emptying ${DESTFILE}" >> "${MSGTMP}"
    # When it's done, no need for that file to grow anymore.
    cat /dev/null > "${DESTFILE}"

    /bin/mail -s "Tomcat Rotation Archival Occurred" "offbeatadam@gmail.com" < ${MSGTMP}
    rm ${MSGTMP}
  fi

fi

## Archive Cleanup ##

# There are no reasons to keep archives that are over two weeks old.
find "${ARCHPATH}" -ctime 14 -exec rm -f {} \;

# Done