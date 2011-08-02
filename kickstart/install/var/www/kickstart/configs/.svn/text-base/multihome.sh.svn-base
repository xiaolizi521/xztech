#!/bin/bash

/etc/init.d/networking restart

ifconfig eth1 0.0.0.0

IF1=eth0
IP1="66.135.45.84"          # kickstart eth0
P1="66.135.45.81"           # gateway
P1_NET="66.135.45.80/28"    # /28 = 255.255.255.240

IF2=vlan405
IP2="10.7.0.2"              # piggybacked on eth1
P2="10.7.0.1"               # gateway
P2_NET="10.7.0.0/24"        # /24 = 255.255.255.0

ip route add $P1_NET dev $IF1 src $IP1 table T1
ip route add 127.0.0.0/8 dev lo table T1 #
ip route add default via $P1 table T1

ip route add $P2_NET dev $IF2 src $IP2 table T2
ip route add 127.0.0.0/8 dev lo table T2 #
ip route add default via $P2 table T2

ip route add $P1_NET dev $IF1 src $IP1
ip route add $P2_NET dev $IF2 src $IP2

ip route add default via $P1

# Apparently these aren't necessary and cause things to break ..
#ip rule add from $IP1 table T1
#ip rule add from $IP2 table T2

