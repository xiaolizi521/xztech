# GVO Server Roles

class hardened::noc {
    
    $my_role = "noc"
    
    include general
}

class hardened::db {
    
    $my_role = "db"
    
    include general
    include mysql
    include postgres