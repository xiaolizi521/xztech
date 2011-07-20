# Log all rejected packets
#!/bin/bash
#
#       /etc/init.d/cust_iptables.sh
#
# chkconfig: 12345 44 56
# description: Redirect port 80 traffic to 8080 and 433 to 8433

iptables=/sbin/iptables
public=eth0
us="67.203.19.32/27"
#BRETT=68.6.55.0/24
#BRETT=71.114.37.105/32
#BRETT=65.220.17.162/32
#BRETT=71.102.164.0/24
#BRETT=68.6.71.111/32
BRETT=98.234.185.155/32
ROBERT=24.184.0.0/16
BRETT2=67.169.112.190/32
BRETTOVALE=71.197.107.235/32
BRETTDSV=72.20.4.6/32
BRETTLINKSYSGOLETA=71.102.222.140/32
BRETTCANDICE=70.143.87.240/32
BRETTMUDDYWATERS=98.173.199.38/32
BRETTTMOBILE=208.54.83.54/32
BRETTNIUHI=206.83.1.2/32
DEV=207.154.84.74
JAMES=71.57.146.214/32

if [ "$1" == "start" ]
then
  echo "Flushing iptables..."

  $iptables -F
  $iptables -X

  echo "Configuring everything else..."

  # allow ping
  #$iptables -A INPUT -p ICMP -s 0/0 --icmp-type echo-request -j ACCEPT
  $iptables -A INPUT -p icmp --icmp-type echo-request -m limit --limit 180/minute -j ACCEPT
  $iptables -A INPUT -p icmp --icmp-type echo-reply -m limit --limit 180/minute -j ACCEPT

  # Block postgres from accepting remote connections
  #$iptables -A INPUT -p tcp --dport 5432 -s 127.0.0.1 -j ACCEPT
  #$iptables -A INPUT -p tcp --dport 5432 -j REJECT

  # Allow ec2 postgres
  $iptables -A INPUT -p tcp --dport 5432 -s 174.129.49.232 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 184.73.148.68 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 184.72.84.174 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 50.17.104.220 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 5432 -s 184.73.24.190 -j ACCEPT

  # allow home net
  $iptables -A INPUT -s 10.0.0.0/8 -j ACCEPT
  $iptables -A INPUT -d 10.0.0.0/8 -j ACCEPT

  # allow localhost
  $iptables -A INPUT -p tcp -s 127.0.0.1 -j ACCEPT
 
  $iptables -A INPUT -p tcp -s 67.203.19.32/29 -j ACCEPT
  $iptables -A INPUT -p tcp -s 64.235.252.0/26 -j ACCEPT
 
  # allow HTTP
  $iptables -A INPUT -p tcp --dport 80 -j ACCEPT 
  $iptables -A INPUT -p tcp --dport 443 -j ACCEPT
  
  $iptables -A INPUT -p tcp --dport 8080 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 8443 -j ACCEPT

  #redirect ports
  $iptables -A INPUT -p tcp -s 0.0.0.0/0 --dport 8080 -m state --state NEW -j ACCEPT
  $iptables -t nat -A PREROUTING -p tcp -s 0.0.0.0/0 --dport 80 -j REDIRECT --to-ports 8080

  $iptables -A INPUT -p tcp -s 0.0.0.0/0 --dport 8443 -m state --state NEW -j ACCEPT
  $iptables -t nat -A PREROUTING -p tcp -s 0.0.0.0/0 --dport 443 -j REDIRECT --to-ports 8443

  # temp allow netacuity
  $iptables -A INPUT -s $BRETT -p tcp --dport 5500 -j ACCEPT
  $iptables -A INPUT -s $ROBERT -p tcp --dport 5500 -j ACCEPT

  $iptables -A INPUT -s $BRETT -p udp -j ACCEPT


  # allow some ssh
  $iptables -A INPUT -s $BRETT -p tcp --dport 22 -j ACCEPT
  $iptables -A INPUT -s $BRETT2 -p tcp --dport 22 -j ACCEPT
  $iptables -A INPUT -s $BRETTOVALE -p tcp --dport 22 -j ACCEPT
  $iptables -A INPUT -s $BRETTDSV -p tcp --dport 22 -j ACCEPT
  $iptables -A INPUT -s $BRETTTMOBILE -p tcp --dport 22 -j ACCEPT
  $iptables -A INPUT -s $BRETTNIUHI -p tcp --dport 22 -j ACCEPT


  $iptables -A INPUT -s $BRETTLINKSYSGOLETA -p tcp --dport 22 -j ACCEPT
  $iptables -A INPUT -s $DEV -p tcp --dport 22 -j ACCEPT
  $iptables -A INPUT -s $BRETTCANDICE -p tcp --dport 22 -j ACCEPT
  
  # allow any tcp we send to ourselves
  $iptables -A INPUT -s $us -d $us -p tcp -j ACCEPT

  # Log/drop SSH brute force atacks
  $iptables -A INPUT -p tcp --syn --dport 22 -m recent --name sshattack --set
  $iptables -A INPUT -p tcp --dport 22 --syn -m recent --name sshattack \
  --rcheck --seconds 120 --hitcount 3 -j LOG --log-prefix 'SSH REJECT: '
  $iptables -A INPUT -p tcp --dport 22 --syn -m recent --name sshattack \
  --rcheck --seconds 120 --hitcount 3 -j REJECT --reject-with tcp-reset


  $iptables -N LOGDROP
  #$iptables -A LOGDROP -j LOG
  $iptables -A LOGDROP -j DROP

  $iptables -N IPCHECKDROP
  $iptables -A IPCHECKDROP -j LOG
  $iptables -A IPCHECKDROP -j DROP

  $iptables -N IPCHECK
  $iptables -A IPCHECK -d 68.225.175.150 -j IPCHECKDROP

  $iptables -A INPUT -j IPCHECK

  # allow ssh
  $iptables -A INPUT -p tcp --dport 22 -j ACCEPT

  # allow previously established connections
  $iptables -A INPUT -j ACCEPT -m state --state ESTABLISHED,RELATED -p tcp

  $iptables -A OUTPUT -j ACCEPT -m state \
        --state NEW,ESTABLISHED,RELATED -p tcp

  # allow localhost
  $iptables -A INPUT -j ACCEPT -i lo

  # allow zimbra
  $iptables -A INPUT -p tcp --dport 9080 -j ACCEPT
  $iptables -A INPUT -s 68.6.55.160/32 -p tcp --dport 7071 -j ACCEPT
  $iptables -A INPUT -p tcp --dport 25 -j ACCEPT
  $iptables -A INPUT -s 127.0.0.1 -d $us -p tcp -j ACCEPT

  # allow DNS
  $iptables -A INPUT -p udp --dport 53 -d 208.67.222.222 -s $us -j ACCEPT
  $iptables -A INPUT -p udp --sport 53 -s 208.67.222.222 -d $us -j ACCEPT
 
  $iptables -A INPUT -p udp --dport 53 -d 208.67.220.220 -s $us -j ACCEPT
  $iptables -A INPUT -p udp --sport 53 -s 208.67.220.220 -d $us -j ACCEPT

  # allow ntp
  $iptables -A INPUT -p udp --dport 123 -s $us -j ACCEPT
  $iptables -A INPUT -p udp --sport 123 -d $us -j ACCEPT


  #allow output
$iptables -A INPUT     -p tcp -m state --state ESTABLISHED -j ACCEPT
$iptables -A OUTPUT -p tcp -m state --state NEW,ESTABLISHED -j ACCEPT 

  # deny everything else
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
