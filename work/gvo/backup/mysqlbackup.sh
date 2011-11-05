#!/bin/bash

HOST=`hostname`
USER='gvobkwr'
PASS='puc8guH8bRA'
SQLHOST='backupdb.ghshosting.com'
FILE="gvodb.$(date +"%d-%m-%Y.%H-%M").gz"

QUERY="INSERT INTO backups 

echo "Beginning MySQL dump $NOW"

mysqldump --skip-lock-tables gvodb | gzip -9 > /var/backups/mysql/gvodb/$FILE

echo "Finished MySQL dump at $(date +"%d-%m-%Y.%H-%M")"

echo "Beginning SCP to Remote Storage at $(date +"%d-%m-%Y.%H-%M")"

scp /var/backups/mysql/gvodb/$FILE 12.204.164.64:/data1/gvomysql/daily/.

echo "Finished SCP to Remote Storage at $(date +"%d-%m-%Y.%H-%M")"