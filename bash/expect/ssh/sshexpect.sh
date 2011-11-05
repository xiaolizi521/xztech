#!/usr/bin/expect -f

# For example:
#  ./sshlogin.exp password 192.168.1.11 who
# ------------------------------------------------------------------------
# Copyright (c) 2004 nixCraft project <http://cyberciti.biz/fb/>
# This script is licensed under GNU GPL version 2.0 or above
# -------------------------------------------------------------------------
# This script is part of nixCraft shell script collection (NSSC)
# Visit http://bash.cyberciti.biz/ for more information.
# ----------------------------------------------------------------------
# set Variables
set password [lrange $argv 0 0]
set ipaddr [lrange $argv 1 1]
set scriptname [lrange $argv 2 2]

set timeout -1
# now connect to remote UNIX box (ipaddr) with given script to execute
spawn ssh pe-local@$ipaddr $scriptname
match_max 100000
# Look for passwod prompt
expect "*?assword:*"
# Send password aka $password
send -- "$password\r"
# send blank line (\r) to make sure we get back to gui
send -- "\r"
expect eof