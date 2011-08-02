#!/bin/bash

NOTES="329.63 311.13 329.63 311.13 329.63 246.94 311.13 261.63 220.00 130.81 164.81 220.00 246.94 164.81 207.65 246.94 261.63 164.81"

while [ 1 == 1 ] ; do
    for tone in $NOTES ; do
        beep -f $tone
    done
    sleeptime=$[ $RANDOM / 8 ]
    echo "Sleeping $sleeptime"
    sleep $sleeptime
done

