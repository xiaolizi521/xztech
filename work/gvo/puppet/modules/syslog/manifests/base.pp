class syslog::base {
        package {
                "syslogd":
                ensure => present,
                name => $operatingsystem ? {
                        default => "sysklogd",
                        },
        }

        file {
                "syslog.conf":
                owner   => "root",
                group   => "root",
                mode    => "644",
                require   => Package["syslogd"],
                path    => $operatingsystem ?{
                           default => "/etc/syslog.conf",
                           },
                content => template("syslog/syslog.conf.erb"),
	}

        file {
                "syslog":
                owner   => "root",
                group   => "root",
                mode    => "644",
                require   => Package["syslogd"],
                path    => $operatingsystem ?{
                           default => "/etc/sysconfig/syslog",
                           },
	}

        service {
                "syslog":
                enable    => "true",
                ensure    => "running",
                hasstatus => "true",
                require   => File["syslog.conf"],
                subscribe => File["syslog.conf"],
                name => $operatingsystem ? {
                        default => "syslog",
                        },
        }

}

