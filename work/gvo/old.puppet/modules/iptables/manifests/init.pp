# Handles iptables concerns.  See also ipt_fragment definition
define ipt_fragment($ensure) {
    case $ensure {
        absent: {
            file { "/etc/iptables.d/$name":
                ensure => absent,
            }
        }
        present: {
            file {
               "/etc/iptables.d/$name":
                    source => "puppet://puppet/iptables/fragments/$name",
                    notify => Exec[rebuild_iptables],
            }
        }
    }
}

class iptables {
    package { "iptables":
        ensure => present
    }

    exec { "rebuild_iptables":
        command => "/usr/sbin/rebuild-iptables",
        refreshonly => true,
        require => File["/usr/sbin/rebuild-iptables"],
    }

    file {
        "/etc/iptables.d":
            ensure => directory,
            purge => true,
            recurse => true,
            force => true,
            source => "puppet:///iptables/empty",
            notify => Exec["rebuild_iptables"];
        "/usr/sbin/rebuild-iptables":
            source => "puppet://puppet/iptables/rebuild-iptables";
    }
}