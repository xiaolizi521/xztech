#!/bin/sh
# Shell script to run as a cron job. Sets the oom_adjust for postmaster.
# This can be altered to add other PIDs to the change.

for i in `ps auxw | grep postgres | grep -v adfrontiers | grep -v bucardo | grep -v grep | awk '{print $2}'`
do
  echo "-17" > /proc/$i/oom_adj
done
