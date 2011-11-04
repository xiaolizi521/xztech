<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

ob_start();

$file_title = 'Login';

function DisplayErrors( $msg ) 
{
	global $script_folder;
	echo '<p style="text-align: center;"><b>', $msg, '</b></p>';
	require_once ( $script_folder . '/login_form.php' );
}

if ( isset ( $user_info['user_id'] ) ) {
	echo '<p style="text-align: center;"><b>You Are Already Logged In</b></p>
<table style="height: 10px;">
	<tr>
		<td></td>
	</tr>
</table>
<p style="text-align: center;"><a href="', $site_url, '/', $main_filename, '">Click here to continue</a></p>';
} 
else {
	if ( isset ( $_POST['login_submit'] ) ) 
	{
		$username = mysql_real_escape_string( $_POST['username'] ); 
		$password = mysql_real_escape_string( md5( $_POST['password'] ) ); 
		$cookiesuser = $_POST['cookiesuser'];

		if ( !eregi ( '^[a-z0-9\-_\.]+$', $username ) || !eregi ( '^[a-z0-9\-_\.]+$', $password ) ) 
		{
			$username = htmlspecialchars ( $username, ENT_QUOTES );
			$password = htmlspecialchars ( $password, ENT_QUOTES );
		}

		if ( empty ( $username ) || empty ( $password ) ) {
			DisplayErrors( 'You must enter a username or password to log in' );
		}
		else {
			$result = mysql_query( 'SELECT * FROM `users` WHERE `username` = \'' . $username . '\' AND `password` = \'' . $password . '\'' );
			$info = mysql_fetch_array( $result );
			$login_check = mysql_num_rows( $result );
			if ( $login_check == 0 ) 
			{
				DisplayErrors( 'You have entered an invalid username or password' );
			}
			else
			{
				if ( isset ( $cookieuser ) ) 
				{
					setcookie( 'user_id', $info['user_id'], time()+60*60*24*30, '/', $_SERVER['HTTP_HOST'], 0 );
					setcookie( 'password', $info['password'], time()+60*60*24*30, '/', $_SERVER['HTTP_HOST'], 0 );
				}
				else
				{
					setcookie( 'user_id', $info['user_id'], '0', '/', $_SERVER['HTTP_HOST'], 0 );
					setcookie( 'password', $info['password'], '0', '/', $_SERVER['HTTP_HOST'], 0 );
				}
				ob_end_flush();
				echo '<p style="width: 100%; text-align: center;"><b>Successfully Logged In!</b></p>
<table style="height: 8px;">
	<tr>
		<td></td>
	</tr>
</table>
<p style="width: 100%; text-align: center;"><a href="', $site_url , '/', $main_filename, '">Click here to continue</a></p>';
			} 
		}
	}
	else
	{
		require_once ( $script_folder . '/login_form.php' );
	}
}
?>
