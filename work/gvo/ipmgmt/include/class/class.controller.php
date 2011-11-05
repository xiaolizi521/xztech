<?php

class Controller {
    
    public $exists = TRUE;
    
    function __construct() {

        $this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $this->session = new Session();
    }

    function __destruct() {

    }
    
    public static function login($username, $password) {
        
        if(!self::$exists):
        
            $db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
            $session = new Session($db);
            
            self::doLogin($username, $password, $db, $session,);
        
        else:
        
            self::doLogin($username, $password);
            
        endif;  
    }
    
    public static function logout() {
        
        if(!self::$exists):
        
            $db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            $session = new Session($db);
            
            unset($_SESSION);
            
            $db->close;
            
        else:
        
            unset($_SESSION);
        
        endif;
    }
    
    public function doLogin($username, $password, DB $db = NULL, Session $session = NULL){
        
            $query = "select uuid,username from users where password = '" . $password . "' AND username = '" .$username."'";
            
            if(!self::$exists):
                
                $result = $db->query($query);
                
            else:
            
                $result = $this->db->query($query);
                
            endif;
            
            if($result->num_rows === 1): 
                
                $data = $result->fetch_assoc();
                
                $_SESSION['id'] = $data['uuid'];
                $_SESSION['loggedin'] = time();
                $_SESSION['username'] = $data['username'];
                
                $result->free();
            
            else:
            
                self::failedLogin();
                
            endif;
    }
    
    public static function isPermitted(Array $types, $userPermission) {
        
        $resourcePermission = array_reduce($types,create_function('$n,$m', '$n += (int)$m; return $n;'), 0);
        return ($resourcePermission & (int)$userPermission?true:false);
    }
    
}

?>
