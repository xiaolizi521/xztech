class hosts::gvo inherits hosts {

        File["hosts"] {
                content => template("project_gvo/hosts/hosts.erb"),
        }

}
