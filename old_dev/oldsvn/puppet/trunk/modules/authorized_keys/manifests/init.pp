define authorized-keys
{
    file { 
        "/root/.ssh":
            ensure  =>  directory,
            owner  =>  root,
            group   =>  root,
            mode    =>  700;
        "/root/.ssh/authorized_keys":
            ensure  =>  present,
            owner   =>  root,
            group   =>  root,
            mode    =>  644,
            require =>  File["/root/.ssh/authorized_keys"];
            source  =>  "puppet://sudo/files/authorized_keys";
    }
}
