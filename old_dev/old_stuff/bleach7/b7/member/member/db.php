<?php 
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

ob_start();

$dbh = mysql_connect ( "localhost", "interact1on", "enteract1on" ) or die ( 'Cannot connect to the database because: ' . mysql_error() );
mysql_select_db ( "interaction" ) or die(mysql_error());  

if ( isset ( $_COOKIE['user_id'] ) && isset ( $_COOKIE['password'] ) ) {
$result_userinfo = mysql_query ( "SELECT * FROM users WHERE user_id='$_COOKIE[user_id]' AND password='$_COOKIE[password]'" );
if ( mysql_num_rows ( $result_userinfo ) > 0 ) {
$user_info = mysql_fetch_array ( $result_userinfo );
}
}
?> 










