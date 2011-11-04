<?php

class connection
{

 function connect($host, $user, $pass, $db)
 {
  mysql_connect ( $host, $user, $pass ) or die ( 'Cannot connect to the database because: ' . mysql_error() );
  mysql_select_db ( $db ) or die ( 'SELECT error: ' . mysql_error() );
 }
 function query($query)
 {
  $myQuery = mysql_query($query);
 }
 
}

class user
{
 public $id;
 public $pass;
 public $info;
 
 function auth()
 {
  $login = mysql_query('SELECT * FROM `users` WHERE `user_id` = \''.$this->id.'\' AND `password` = \''.$this->pass.'\'');
  
  if(mysql_num_rows($login) > 0)
  { return true; }
  else
  { return false; }
 }
 
 function fetch()
 {
  $login = mysql_query('SELECT * FROM `users` WHERE `user_id` = \''.$this->id.'\' AND `password` = \''.$this->pass.'\'');
  $info = mysql_fetch_array($login);
  $this->info = $info;
  
  return $info;
 }
 
 function admin()
 {
  if($this->info['type'] > 20)
  { return true; }
  else
  { return false; }
 }
 
 function __construct($id,$pass)
 { 
  $this->id = $id;
  $this->pass = $pass;
 }

}
?>