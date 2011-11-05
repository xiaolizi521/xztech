<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

include ( "../$_SERVER%5BDOCUMENT_ROOT%5D/settings.php" );
include ( "../$_SERVER%5BDOCUMENT_ROOT%5D/online/onlinecheck.php" );

if ( !ereg ( "/member.php", "$_SERVER[SCRIPT_NAME]" ) ) {
echo "<script type=\"text/javascript\" src=\"header.js\"></script>";
}

if ( isset ( $_COOKIE[user_id] ) && isset ( $_COOKIE[password] ) ) {
$result_userinfo = mysql_query ( "SELECT * FROM users WHERE user_id='$_COOKIE[user_id]' AND password='$_COOKIE[password]'" );
if ( mysql_num_rows ( $result_userinfo ) > 0 ) {
$user_info = mysql_fetch_array ( $result_userinfo );
}
}

$result_pm_count_cp = mysql_query ( "SELECT sent_to FROM pm WHERE sent_to='$user_info[username]'" );
$result_pm_status = mysql_query ( "SELECT * FROM pm WHERE sent_to='$user_info[username]' AND status='3'" );
$pm_count_cp = mysql_num_rows ( $result_pm_count_cp );
$pm_count_status = mysql_num_rows ( $result_pm_count_cp );

while ( $pm_status = mysql_fetch_array ( $result_pm_status ) ) {
if ( $pm_count_status > 0 ) {
$pm_status_date = date ( 'm/d/y \a\\t h:i A', strtotime( $pm_status[sent_on] ) ); 
echo "<script type=\"text/javascript\">alert('You have recieved a new PM from $pm_status[sent_by] on $pm_status_date')</script>";
$update_pm_status_recieved = mysql_query ( "UPDATE pm SET status='0' WHERE id='$pm_status[id]' AND sent_to='$user_info[username]' AND status='3'" );
}
}
?>
<script type="text/javascript" src="../Member.js"></script>