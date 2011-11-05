<?php 
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

require_once ( 'class.php' );
ob_start();

$sql_location = 'localhost'; //location of MySQL || Was "localhost"
$sql_username = 'bleach7_b7'; //username || Was "remote"
$sql_password = 'funwithbleach.'; //password || Was "af32@Q#%#@FSDFDS%#@"
$sql_database = 'bleach7_b7'; // database name || Was "interaction"

$dbh = mysql_connect ( $sql_location, $sql_username, $sql_password ) or die ( include ( 'indexdowntime.php' ) );
mysql_select_db ( $sql_database ) or die ( 'SELECT error: ' . mysql_error() );  

if ( isset ( $_COOKIE['user_id'] ) && isset ( $_COOKIE['password'] ) ) {
	$result_userinfo = mysql_query ( 'SELECT * FROM `users` WHERE `user_id`=\'' . mysql_real_escape_string ( $_COOKIE['user_id'] ) . '\' AND `password`=\'' . mysql_real_escape_string ( $_COOKIE['password'] ) . '\'' );
	if ( mysql_num_rows ( $result_userinfo ) > 0 ) {
		$user_info = mysql_fetch_array ( $result_userinfo );
		$user_B7 = new B7_User ( $user_info );
	}
}
?> 










