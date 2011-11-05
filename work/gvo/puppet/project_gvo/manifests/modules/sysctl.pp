class sysctl::gvo inherits sysctl {

        File["/etc/sysctl.conf"] {
                        source => [
                                "puppet://$servername/project_gvo/sysctl/sysctl.conf-$hostname",
                                "puppet://$servername/project_gvo/sysctl/sysctl.conf"
                        ],
        }
}

