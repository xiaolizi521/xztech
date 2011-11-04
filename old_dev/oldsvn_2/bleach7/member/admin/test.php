<?php
include('members/db.php');
include('members/functions.php');
include('members/settings.php');
include('members/header.php');
?>
<?php
if ( file_exists ( "".$$ident.".php" ) && isset ( $_GET[$ident] ) && !empty ( $_GET[$ident] ) ) {
	if ( ereg ( "media", $_SERVER[QUERY_STRING] ) ) {
		if ( isset ( $user_info[user_id] ) ) {
			include ( "".$$ident.".php" );
		}
		else {
			echo "<p align='center'>You need to be registered to access this page, please <a href='$site_path/login'><b>login</b></a> or <a href='$site_path/register'><b>register</b></a>.</p>";
		}
	}
	else {
		include ( "".$$ident.".php" );
	}
	$delete_oldguests = mysql_query ( "DELETE FROM guests WHERE UNIX_TIMESTAMP(now()) - last_activity_time > 600" );
	if ( isset ( $user_info[user_id] ) ) {
		$result_writeonline = mysql_query ( "UPDATE users SET last_activity_time=$timenow, last_activity_title='$file_title', last_activity_url='$current_location', ip_address='$_SERVER[REMOTE_ADDR]' WHERE user_id='$user_info[user_id]'" );
	}
	else {
		$delete_guestonline = mysql_query ( "DELETE FROM guests WHERE ip_address='$_SERVER[REMOTE_ADDR]'" );
		$insert_guestsonline = mysql_query ( "INSERT INTO guests ( ip_address, last_activity_time, last_activity_title, last_activity_url ) VALUES ( '$_SERVER[REMOTE_ADDR]', $timenow, '$file_title', '$current_location' )" );
	}
}
else {
	include ( "$script_folder/news.php" );
//header ( "Location: $site_path/news" );
//exit();
}?>
