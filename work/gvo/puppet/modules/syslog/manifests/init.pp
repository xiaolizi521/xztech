class syslog {
    case $operatingsystem {
	Suse: { include syslog-ng }
	default: { include syslog }
    }
}
