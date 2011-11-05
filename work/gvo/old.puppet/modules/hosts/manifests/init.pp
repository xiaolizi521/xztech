# Create the sshd_config file and maintain the service is uptodate and secure.

class hosts
{
    hosts_allow_config{ puppet: listenaddress => $ipaddress }
}

define hosts_allow_config($listenaddress)
{
    file
    { "/etc/hosts.allow":
            path    => "/etc/ssh/sshd_config",
            owner   => root,
            group   => root,
            mode    => 444,
            content => template("sshd/sshd_config.erb"),
            notify  => Service[sshd],
    }

    service
    { sshd:
        ensure  => running
    }
}