#!/bin/bash
#-------------------------------------------------------------------------------
# Company:              Server Beach
# Copyright(c):         Server Beach 2006
# Project:              Kickstart Automated Database Dump
# Code Devloper:        Carlos Avila
# Creation Date:        2008-03-06
#
# File Type:            Script
# File Name:            backup_ks_db.sh
#
# Description:
# -c Creates a gzip backup of the current database on $BACKUP_DIR
# -D Deletes backups in $BACKUP_DIR that are older than $EXPIRE days
#-------------------------------------------------------------------------------

#CONFIG

DATE_FORMAT=+%Y-%m-%d;
export PGDATABASE="kickstart";
DB_USER="kickstart";
export PGPASSWORD="l33tNix";
BACKUP_DIR="/exports/kickstart/sql/backups";
EXPIRE=31; #how old, in days, before deleting a backup

#END CONFIG

#CONSTANTS

CURRENT_DATE=`date ${DATE_FORMAT}`;
HOSTNAME=`hostname | awk -F . '{printf "%s.%s", $1, $2}'`;

#END CONSTANTS

#
if [ -d "$BACKUP_DIR" ]; then 

        date;

        if [ $1 == '-c' ]; then #gz dump (should be once a week)
                DUMP_TARGET="${HOSTNAME}.db_dump.${CURRENT_DATE}.sql.gz";
                echo -e "\nDumping database to ${BACKUP_DIR}/${DUMP_TARGET}...";
                pg_dump -U ${DB_USER} | gzip -9 > "${BACKUP_DIR}/${DUMP_TARGET}";
                echo -e "Done\n"
                exit;
        fi

        if [ $1 = '-D' ]; then #Clean old backups (should be once every two weeks)
                echo "Clearing old backups";
                echo -e "\nDeleting backups older than ${EXPIRE} days from ${BACKUP_DIR}...";
                find "${BACKUP_DIR}" -name "${HOSTNAME}*" -mtime +${EXPIRE} -exec rm {} \;
                echo -e "Done\n";
                exit;
        fi
fi

