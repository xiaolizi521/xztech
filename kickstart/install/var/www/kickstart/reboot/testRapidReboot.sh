#!/bin/bash

# written by donald ray moore jr.

# port rapid reboot daemon listens on
RR_PORT=2250

# ip for rapid reboot in LAX1
RR_IP=10.6.0.10

# [serial port]:[rack board id]:[machine id]-[command]
# a machine id of 153 = test, if board hears you it 
# will respond but won't do anything

# blueheat card has 8 ports [0-7], look through each one
# and send a test command, it will repsond with either
# a ERR (bad) or ACK (good)
#
# it maybe helpful for you to sudo to root, su sbadmin, then
# ssh 10.5.0.10, once inside that rapid reboot, then screen -x
# there's a lot of details there! you been warned!
for j in 0 1 2 3 4 5 6 7; do 
for i in 0 1 2; do 
        echo "${j}:${i}:${1:-153}-on" | nc ${RR_IP} ${RR_PORT}
done
done
