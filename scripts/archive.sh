#!/bin/bash

ROOTDIR="/home/triton/.irssi"
LOGDIR="$ROOTDIR/logs"
ARCHIVE="$ROOTDIR/logs.old"

printf "Beginning log archiving\n"

printf "Moving to log directory %s\n" "$LOGDIR"
cd $LOGDIR

for i in `ls -A`
do

  printf "Moving to log subdirectory %s\n" "$i"
  cd $LOGDIR/$i

  for j in `ls -A`
  do
    printf "Current Working Directory: %s/%s/%s\n" "$LOGDIR" "$i" "$j"
    cd $LOGDIR/$i/$j
  
    for k in `find . -type f -atime +7 | cut -b 3-`
    do
      printf "Archiving File %s/%s/%s/%s to %s/%s/%s/%s\n" "$LOGDIR" "$i" $j" "$k" "$ARCHIVE" "$i" $j" "$k"
      cd $LOGDIR; 
    done
  done
done
