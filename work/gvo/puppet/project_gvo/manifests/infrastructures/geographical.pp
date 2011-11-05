# A simple example for a geographical Infrastructure based on inheritance tree

node hq inherits basenode {
        $my_local_network = "10.42.200.0/24"
        $my_zone = "hq"
}

node milan inherits basenode {
        $my_local_network = "10.42.201.0/24"
        $my_zone = "milan"
}

node paris inherits basenode {
        $my_local_network = "10.42.202.0/24"
        $my_zone = "paris"
}

node london inherits basenode {
        $my_local_network = "10.42.203.0/24"
        $my_zone = "london"
}


# The same scenario based on class, included by all hosts, that defines settings according to the $domain fact 
# Note that the $my_zone custom variable can be redundant 

class location {

        case $domain {
                "gvo.com": {
                        $my_puppet_server = "puppet.gvo.com"
		        $my_dns_servers = ["10.42.42.1","10.42.10.1"]
		        $my_smtp_server = "mail.gvo.com"
		        $my_syslog_servers = ["10.42.42.15"]
        		$my_zone = "hq"
		}

                "milan.gvo.com": {
                        $my_puppet_server = "puppet.gvo.com"
		        $my_dns_servers = ["10.42.42.1","10.42.10.1"]
		        $my_smtp_server = "mail.gvo.com"
		        $my_syslog_servers = ["10.42.42.15"]
        		$my_zone = "milan"
		}

                "paris.gvo.com": {
                        $my_puppet_server = "puppet.gvo.com"
		        $my_dns_servers = ["10.42.42.1","10.42.10.1"]
		        $my_smtp_server = "mail.gvo.com"
		        $my_syslog_servers = ["10.42.42.15"]
        		$my_zone = "paris"
		}

                "london.gvo.com": {
                        $my_puppet_server = "puppet.london.gvo.com"
		        $my_dns_servers = ["10.42.42.1","10.42.10.1"]
		        $my_smtp_server = "mail.gvo.com"
		        $my_syslog_servers = ["10.42.42.15"]
        		$my_zone = "london"
		}

                default: {
                	err ("domain fact must be correct!")
		}
        }
}
