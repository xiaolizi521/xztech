# Here's defined the baseline node (note that this is a Puppet default). 
# It can be empty if wanted.
# Here we use it to define general variables: they can be overriden by
# nodes that inherit basenode 

node basenode {
# Example42 approach permits to manage different projects (puppet nodes configurations) by the same puppetmaster:
# Different Puppet environments can map to different project modules.
        $my_project = "gvo"

        $my_puppet_server = "puppet.ghshosting.com"
        $my_dns_servers = ["12.68.140.254","208.67.220.220"]
        $my_domain = "ghshosting.com"
        $my_smtp_server = "secure.gvocom.com"

# Local root mail is forwarded to $my_root_email - CHANGE IT!
        $my_root_email = "servers@gvocom.com"

# Syslog servers. Can be an array.
        $my_syslog_servers = ["10.42.42.15"]


        $my_timezone = "American/Central"
        $my_ntp_server = "pool.ntp.org"

        $my_update = "no"   # Auto Update packages (yes|no)

# Collectd Central server (here we use unicast networking)
# Define the server IP (not the hostname)
	$collectd_server = "10.42.42.9"

}

