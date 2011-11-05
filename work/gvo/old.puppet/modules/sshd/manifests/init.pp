# Create the sshd_config file and maintain the service is uptodate and secure.

class sshdconfig
{
    sshd_config{ puppet: listenaddress => $ipaddress }
}

define sshd_config($listenaddress)
{
    file
    { "/etc/ssh/sshd_config":
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
