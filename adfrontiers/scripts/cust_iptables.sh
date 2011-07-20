# Log all rejected packets
#!/bin/bash
#
#       /etc/init.d/cust_iptables.sh
#
# chkconfig: 12345 44 56
# description: Redirect port 80 traffic to 8080 and 433 to 8433

## Set Variables ##
iptables=/sbin/iptables
public=eth0

LOCALNET="67.203.19.32/27"

if [ "$1" == "start" ]
then
  echo "Flushing iptables..."

  $iptables -F
  $iptables -X

  echo "Configuring everything else..."

  ## Define any Chains that will be used ##

  # Create LOGDROP chain and make sure that it is logged to the appropriate log
  $iptables -N LOGDROP
  $iptables -A LOGDROP -j LOG --log-level 4
  $iptables -A LOGDROP -j DROP
 
  ## Begin Rules Definitions ##

  # Permit Loopback & Loopback Interface
  $iptables -A INPUT -p tcp -s 127.0.0.1 -j ACCEPT
  $iptables -A INPUT -i lo -j ACCEPT
  $iptables -A INPUT -s 127.0.0.1 -d $LOCALNET -p tcp -j ACCEPT
 
  # RFC.1918 Address Space (Private Networks)
  $iptables -A INPUT -s 10.0.0.0/8 -j ACCEPT
  $iptables -A INPUT -d 10.0.0.0/8 -j ACCEPT

  # ICMP
  $iptables -A INPUT -p icmp --icmp-type echo-request -m limit --limit 180/minute -j ACCEPT
  $iptables -A INPUT -p icmp --icmp-type echo-reply -m limit --limit 180/minute -j ACCEPT

  # HTTP/HTTPS Traffic
  $iptables -A INPUT -p tcp --dport 80 -j ACCEPT 
  $iptables -A INPUT -p tcp --dport 443 -j ACCEPT
  
  # JAVA HTTP/HTTPS Traffic
  $iptables -A INPUT -p tcp --dport 8080 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 8443 -j ACCEPT

  # OpenDNS (Primary and Secondary)
  $iptables -A INPUT -p udp --dport 53 -d 208.67.222.222 -s $LOCALNET -j ACCEPT
  $iptables -A INPUT -p udp --sport 53 -s 208.67.222.222 -d $LOCALNET -j ACCEPT 
  $iptables -A INPUT -p udp --dport 53 -d 208.67.220.220 -s $LOCALNET -j ACCEPT
  $iptables -A INPUT -p udp --sport 53 -s 208.67.220.220 -d $LOCALNET -j ACCEPT

  # NTP (time)
  $iptables -A INPUT -p udp --dport 123 -s $LOCALNET -j ACCEPT
  $iptables -A INPUT -p udp --sport 123 -d $LOCALNET -j ACCEPT

  # Public IP Space Allocation from Datacenter
  $iptables -A INPUT -p tcp -s $LOCALNET -j ACCEPT
  
  # Internal Network Traffic
  $iptables -A INPUT -s $LOCALNET -d $LOCALNET -p tcp -j ACCEPT

  # SSH Bruteforce Protection
  # Log recent connections to hashtable: sshattack
  # If within a period of 120 seconds an IP address is seen 3 times, block that IP.
  $iptables -A INPUT -p tcp --syn --dport 22 -m recent --name sshattack --set
  $iptables -A INPUT -p tcp --dport 22 --syn -m recent --name sshattack --rcheck --seconds 120 --hitcount 3 -j LOG --log-prefix 'SSH REJECT: '
  $iptables -A INPUT -p tcp --dport 22 --syn -m recent --name sshattack --rcheck --seconds 120 --hitcount 3 -j REJECT --reject-with tcp-reset

  # SSH Access
  $iptables -A INPUT -p tcp --dport 22 -j ACCEPT

  # Redirect port 8080 to port 80 (HTTP)
  $iptables -A INPUT -p tcp -s 0.0.0.0/0 --dport 8080 -m state --state NEW -j ACCEPT
  $iptables -t nat -A PREROUTING -p tcp -s 0.0.0.0/0 --dport 80 -j REDIRECT --to-ports 8080

  # Redirect port 8443 to 443 (HTTPS)
  $iptables -A INPUT -p tcp -s 0.0.0.0/0 --dport 8443 -m state --state NEW -j ACCEPT
  $iptables -t nat -A PREROUTING -p tcp -s 0.0.0.0/0 --dport 443 -j REDIRECT --to-ports 8443
  
  # EC2 Postgres
  $iptables -A INPUT -p tcp --dport 5432 -s 174.129.49.232 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 184.73.148.68 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 184.72.84.174 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 50.17.104.220 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 184.73.24.190 -j ACCEPT

  # Not sure what this is here for. Will perform additional inquiry.  
  $iptables -A INPUT -d 68.225.175.150 -j LOGDROP

  # Session related rules
  $iptables -A INPUT -m state --state ESTABLISHED,RELATED -p tcp -j ACCEPT
  $iptables -A INPUT -m state --state ESTABLISHED -p tcp -j ACCEPT
  $iptables -A OUTPUT -m state --state NEW,ESTABLISHED,RELATED -p tcp -j ACCEPT
  $iptables -A OUTPUT -m state --state NEW,ESTABLISHED -p tcp -j ACCEPT

  # Sendmail. Local hosts only.
  $iptables -A INPUT -p tcp -s 127.0.0.1 --dport 25 -j ACCEPT
  $iptables -A INPUT -p tcp -s 10.0.0.0/8 --dport 25 -j ACCEPT
  $iptables -A INPUT -p tcp -s $LOCALNET --dport 25 -j ACCEPT

  # Set DEFAULT to DENY (with LOGGING)
  $iptables -A INPUT -j LOGDROP

fi

if [ "$1" == "stop" ]
then
  echo "Warning: iptables disabled, going unsecure..."

  $iptables -F
  $iptables -X
  $iptables -F -t mangle
  $iptables -F -t nat
fi