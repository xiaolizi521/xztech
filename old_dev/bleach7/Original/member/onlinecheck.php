<?php

####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################
/*
include ( 'member/db.php' );
include ( 'member/settings.php' );

$site_path = "$site_url/$main_filename?$ident=$script_folder";
$timeout = 600;
$timenow = time();

//$result_membercount = mysql_query ( "SELECT 'user_id' FROM users" );
$result_onlinecheck_admin = mysql_query ( "SELECT 'user_id' FROM `users` WHERE $timenow - last_activity_time <= $timeout AND `type` >= 20" );
$result_onlinecheck_users = mysql_query ( "SELECT 'user_id' FROM `users` WHERE $timenow - last_activity_time <= $timeout AND `type` < 20" );
$result_onlinecheck_guests = mysql_query ( "SELECT `ip_address` FROM `guests`" );

$onlinecheck_admins = mysql_num_rows($result_onlinecheck_admin);
$onlinecheck_members = mysql_num_rows($result_onlinecheck_users);
$onlinecheck_guests =  mysql_num_rows($result_onlinecheck_guests);
//$onlinecheck_mem = mysql_num_rows($result_membercount);


while ( $onlinecheck1 = mysql_fetch_array ( $result_onlinecheck_users ) ) {
if ( $onlinecheck1['type'] == "98" ||  $onlinecheck1['type'] == "99" ) {
$onlinecheck_admins++;
}
if ( $onlinecheck1['type'] == "1" || $onlinecheck1['type'] == "2" ||  $onlinecheck1['type'] == "10" ||  $onlinecheck1['type'] == "11" 
||  $onlinecheck1['type'] == "20" ||  $onlinecheck1['type'] == "21" ||  $onlinecheck1['type'] == "30" ||  $onlinecheck1['type'] == "31" 
||   $onlinecheck1['type'] == "80" ||  $onlinecheck1['type'] == "81" || $onlinecheck1['type'] == "90" || $onlinecheck1['type'] == "91" ) {
$onlinecheck_members++;
} 

while ( $onlinecheck2 = mysql_fetch_array ( $result_onlinecheck_guests ) ) {
$onlinecheck_guests++;
}

while ( $onlinecheck3 = mysql_fetch_array ( $result_membercount ) ) {
$onlinecheck_mem++;
}

$onlinecheck_total = ( $onlinecheck_admins + $onlinecheck_members + $onlinecheck_guests );

$onlinelist = "											<div id=\"side_Stats\" class=\"side_bar\">
										&nbsp;&nbsp;&nbsp;- Admins Online: <a href=\"?page=member/online\"><b></b></a><br />
										&nbsp;&nbsp;&nbsp;- Members Online: <a href=\"?page=member/online\"><b></b></a><br />
										&nbsp;&nbsp;&nbsp;- Guests Online: <a href=\"?page=member/online\"><b></b></a><br />
										&nbsp;&nbsp;&nbsp;- Total Users Online: <a href=\"?page=member/online\"><b></b></a><br />
										<!--&nbsp;&nbsp;&nbsp;- Total Members: <b><font color=\"#a60000\"></font></b><br />-->
									</div>

";
*/

$onlinelist = "											<div id=\"side_Stats\" class=\"side_bar\"><br />
										&nbsp;&nbsp;&nbsp;Member stats temporarily offline

									</div>

";

( $fp = fopen ( "/home/bleach7/public_html/member/onlinelist.php", "w" ) ) or die ( "couldn't open" );
fwrite ( $fp, "$onlinelist" );
fclose ( $fp );

?>

