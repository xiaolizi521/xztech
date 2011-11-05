# Define here your nodes
# (you can split the nodes definitions in different files to be managed by different people)
# You can override variables defined in the infrastructure tree
# Each node should inherit a zone node defined in infrastructure.pp
# A anode can include single module-classes or a role class
# Roles are defined in roles.pp

# Same example nodes 

# Puppet Master
node 'puppet.gvo.com' inherits devel {
	include foreman
	include apache
	include minimal
	include general
	include puppet::dashboard
#	include puppet::foreman
#	include puppet::foreman::externalnodes

	include ssh::auth::keymaster

}

## Testing hosts (Used for modules testing)

node 'test.gvo.com' inherits devel {
	include minimal
	include general

	include backup::server
	include monitor::server
}

node 'debiantest.gvo.com' inherits devel {
	include minimal
	include general
}

node 'opensusetest.gvo.com' inherits devel {
	include minimal
	include general
}

node 'solaristest.gvo.com' inherits devel {
	include minimal
#	include general
}


# Cobbler Server 
node 'cobbler.gvo.com' inherits devel {
	$my_cobbler_server = "10.42.10.10"
	$my_tftp_server = "10.42.10.10"

	include general::provisioner
}

# Central Syslog Server
node 'syslog.gvo.com' inherits devel {
	include general
}

# Cacti Monitoring Server
node 'cacti.gvo.com' inherits intranet {
        $my_cacti_mysqluser = "cactiuser"
        $my_cacti_mysqlpassword = "gvo"
        $my_cacti_mysqlhost = "localhost"
        $my_cacti_mysqldbname = "cacti"
        $my_mysql_passwd = "gvo"
        include general::monitor
}



# Internet Services


# Postfix+Mailscanner+Mailwatch Mail Server
node 'mail.gvo.com' inherits prod {
        $my_postfix_mysqluser = "postfix"
        $my_postfix_mysqlpassword = "gvo"
        $my_postfix_mysqlhost = "127.0.0.1"
        $my_postfix_mysqldbname = "postfix"
        $my_postfix_mynetworks = $my_network/$my_netmask

        $my_mailwatch_mysqluser = "mailwatch"
        $my_mailwatch_mysqlpassword = "exampl42"
        $my_mailwatch_mysqlhost = "127.0.0.1"
        $my_mailwatch_mysqldbname = "mailscanner"

        $my_mysql_passwd = "gvo"

        include general::mail
}


node 'web01.gvo.man' inherits prod {
        $my_apache_namevirtualhost = "10.42.10.12"
        $my_mysql_passwd = "gvo"
        $my_postfix_mynetworks = $my_network/$my_netmask
        
	include general::webhosting
}


# Samba PDC - Ldap backend 
node 'dc.gvo.com' inherits intranet {
        $ldap_master = "127.0.0.1"
        $ldap_slave  = "127.0.0.1"
        $ldap_basedn = "dc=gvo,dc=com"
        $ldap_rootdn = "cn=Manager,dc=gvo,dc=com"
        $ldap_rootpw = "{SSHA}gvotosha"
        $ldap_rootpwclear = "gvo"
        $samba_sid        = "S-1-5-21-3645972101-772173552-949487278"
        $samba_workgroup  = "EXAMPLE42"
        $samba_pdc        = "dc.gvo.com"
        $mysql_passwd     = "gvo"

        include general::file
}




# Security
node 'pentester.gvo.com' inherits intranet {
	include general::scan
}

node 'lanfirewall.gvo.com' inherits intranet {
	include general::gateway
}

node 'vpn.gvo.com' inherits intranet {
	include general::vpn
}




# Bare metal
node 'xen01.gvo.com' inherits intranet {
	include minimal::xenmaster
}

node 'xen02.gvo.com' inherits intranet {
	include minimal::xenmaster
}



# Development 

node 'devel.gvo.com' inherits devel {
	include general::devel
}

node 'build.gvo.com' inherits devel {
	include general::build
}

node 'fileserver.gvo.com' inherits remotebranch {
	include general
}
