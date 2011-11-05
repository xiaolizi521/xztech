<?php

class User extends Controller {
    
    function __construct(){
        
        $this->db = parent::$db;
    }
    
    functin __destruct(){
        
    }
    
    public static function _genID($string) {

		$string = substr(md5($string), 0, -16);

		return $string;
    }
}