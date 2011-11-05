<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

ob_start();

$file_title = "Login";

function DisplayErrors( $msg ) {
global $script_folder;
echo "<center><b>$msg</b></center><p>";
include ( "./$script_folder/login_form.php" );
}

if ( isset ( $user_info['user_id'] ) ) {
echo "<center><b>You Are Already Logged In</b>";
echo "<table height='10'><tr><td></td></tr></table>";
echo "<a href='$site_url/$main_filename'>Click here to continue</a></center>";
} else {
if ( isset ( $_POST['login_submit'] ) ) {
$username = mysql_real_escape_string( $_POST['username'] ); 
$password = mysql_real_escape_string( md5( $_POST['password'] ) ); 

$cookievar = $_POST['cookieuser'];
 if($cookievar == 1)
 { $cookieuser = 1; }
 else
 { $cookieuser = 0; }

if ( !eregi ( "^[a-z0-9\-_\.]+$", $username ) || !eregi ( "^[a-z0-9\-_\.]+$", $password ) ) {
$username = htmlspecialchars ( $username, ENT_QUOTES );
$password = htmlspecialchars ( $password, ENT_QUOTES );
}

if ( empty ( $username ) || empty ( $password ) ) {
DisplayErrors( "You must enter a username or password to log in" );
} else {



$result = mysql_query( "SELECT * FROM users WHERE username='$username' AND password='$password'" );
$info = mysql_fetch_array( $result );
$login_check = mysql_num_rows( $result );

if ( $login_check == 0 ) {
DisplayErrors( "You have entered an invalid username or password" );
} else {
if ( $cookieuser == 1 ) {
$expire = time()+60*60*24*30;
} else {
$expire = 0;
}

setcookie( "user_id", "$info[user_id]", $expire, "/", 'bleach7.com', false );
setcookie( "password", "$info[password]", $expire, "/", 'bleach7.com', false );
ob_end_flush();
echo "<center><b>Successfully Logged In!</b>";
echo "<table height='10'><tr><td></td></tr></table>";
echo "<a href='$site_url/'>Click here to continue</a></center>";
} 

}

} else {
include ( "./$script_folder/login_form.php" );
}
}

function getDomain() {
   if ( isset($_SERVER['HTTP_HOST']) ) {
       // Get domain
       $dom = $_SERVER['HTTP_HOST'];
       // Strip www from the domain
       if (strtolower(substr($dom, 0, 4)) == 'www.') { $dom = substr($dom, 4); }
       // Check if a port is used, and if it is, strip that info
       $uses_port = strpos($dom, ':');
       if ($uses_port) { $dom = substr($dom, 0, $uses_port); }
       // Add period to Domain (to work with or without www and on subdomains)
       $dom = '.' . $dom;
   } else {
       $dom = false;
   }
   return $dom; 
}
?>
