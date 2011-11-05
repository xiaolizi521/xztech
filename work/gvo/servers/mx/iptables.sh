#!/bin/sh
#
#############################################################################
#
# File: iptables.sh
#
# Purpose: To build a basic iptables policy with default log and drop rules.
#          This script was written for the book "Linux Firewalls: Attack
#          Detection and Response" published by No Starch Press.
#
#
# License (GNU Public License):
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with this program; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307
#   USA
#
#
#############################################################################
#
# $Id: iptables.sh 1406 2008-04-14 01:48:04Z mbr $
#

IPTABLES=/sbin/iptables
MODPROBE=/sbin/modprobe

### flush existing rules and set chain policy setting to DROP
echo "[+] Flushing existing iptables rules…"
$IPTABLES -F
$IPTABLES -X
$IPTABLES -P INPUT DROP
$IPTABLES -P OUTPUT DROP
$IPTABLES -P FORWARD DROP

### load connection-tracking modules
#
$MODPROBE ip_conntrack
$MODPROBE ip_conntrack_ftp

###### INPUT chain ######
#
echo "[+] Setting up INPUT chain…"

### state tracking rules
$IPTABLES -A INPUT -s 127.0.0.1 -j ACCEPT

$IPTABLES -A INPUT -m state --state INVALID -j LOG --log-prefix "DROP INVALID " --log-ip-options --log-tcp-options
$IPTABLES -A INPUT -m state --state INVALID -j DROP
$IPTABLES -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

### ACCEPT rules

## SSH
$IPTABLES -A INPUT -p tcp --dport 2 --syn -m state --state NEW -j ACCEPT

## SMTP
$IPTABLES -A INPUT -p tcp --dport 25 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A INPUT -p tcp --dport 53 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A INPUT -p udp --dport 53 --syn -m state --state NEW -j ACCEPT


$IPTABLES -A INPUT -p icmp --icmp-type echo-request -j ACCEPT

### default INPUT LOG rule
$IPTABLES -A INPUT -i ! lo -j LOG --log-prefix "DROP " --log-ip-options --log-tcp-options

###### OUTPUT chain ######
#
echo "[+] Setting up OUTPUT chain…"

### state tracking rules
$IPTABLES -A OUTPUT -m state --state INVALID -j LOG --log-prefix "DROP INVALID " --log-ip-options --log-tcp-options
$IPTABLES -A OUTPUT -m state --state INVALID -j DROP
$IPTABLES -A OUTPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

### ACCEPT rules for allowing connections out

### IMAP OUTBOUND ###
$IPTABLES -A OUTPUT -p tcp --dport 143 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A OUTPUT -p tcp --dport 993 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A OUTPUT -p udp --dport 143 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A OUTPUT -p udp --dport 993 --syn -m state --state NEW -j ACCEPT

$IPTABLES -A OUTPUT -p tcp --dport 53 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A OUTPUT -p udp --dport 53 --syn -m state --state NEW -j ACCEPT
### POP3 OUTBOUND ###
$IPTABLES -A OUTPUT -p tcp --dport 110 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A OUTPUT -p tcp --dport 995 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A OUTPUT -p udp --dport 110 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A OUTPUT -p udp --dport 995 --syn -m state --state NEW -j ACCEPT

$IPTABLES -A OUTPUT -p icmp --icmp-type echo-request -j ACCEPT

### default OUTPUT LOG rule
$IPTABLES -A OUTPUT -o ! lo -j LOG --log-prefix "DROP " --log-ip-options --log-tcp-options

###### FORWARD chain ######
#
echo "[+] Setting up FORWARD chain…"

### state tracking rules
$IPTABLES -A FORWARD -m state --state INVALID -j LOG --log-prefix "DROP INVALID " --log-ip-options --log-tcp-options
$IPTABLES -A FORWARD -m state --state INVALID -j DROP
$IPTABLES -A FORWARD -m state --state ESTABLISHED,RELATED -j ACCEPT

### ACCEPT rules

$IPTABLES -A FORWARD -p tcp --dport 2 --syn -m state --state NEW -j ACCEPT

$IPTABLES -A FORWARD -p tcp --dport 143 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A FORWARD -p tcp --dport 993 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A FORWARD -p tcp --dport 143 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A FORWARD -p tcp --dport 993 --syn -m state --state NEW -j ACCEPT

$IPTABLES -A FORWARD -p tcp --dport 110 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A FORWARD -p tcp --dport 995 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A FORWARD -p tcp --dport 110 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A FORWARD -p tcp --dport 995 --syn -m state --state NEW -j ACCEPT

$IPTABLES -A FORWARD -p icmp --icmp-type echo-request -j ACCEPT

$IPTABLES -A FORWARD -p tcp --dport 53 --syn -m state --state NEW -j ACCEPT
$IPTABLES -A FORWARD -p udp --dport 53 --syn -m state --state NEW -j ACCEPT

### default LOG rule
$IPTABLES -A FORWARD -i ! lo -j LOG --log-prefix "DROP " --log-ip-options --log-tcp-options

exit
### EOF ###
