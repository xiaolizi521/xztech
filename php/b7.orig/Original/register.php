<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( ereg ( '/register.php', $_SERVER['SCRIPT_NAME'] ) ) {
	require_once ( './member/header.php' );
	header ( 'Locaction: ' . $site_url . '/' . $main_filename . '?page=register' );
}

$register_form = 'true';

/*
if ( !isset ( $_POST[submit] ) ) {
srand((double)microtime()*1000000); 
$_SESSION[verify_string] = md5(rand(0,9999)); 
$_SESSION[verify_new_string] = substr($_SESSION[verify_string],17,5);
}
*/
?>
<form action="?page=register" method="post">
<table cellpadding="0" cellspacing="0" style="width: 100%; text-align: center">
	<tr>
		<td style="width: 100%; text-align: center; vertical-align: middle;" class="main"><?php
if ( !isset ( $user_info['user_id'] ) ) {
	if ( isset ( $_POST[submit] ) ) {
		$username = stripslashes( $_POST['username'] ); 
		$password = stripslashes( $_POST['password'] ); 
		$email_address = stripslashes( $_POST['email_address'] ); 
		if ( empty ( $username ) || empty ( $password ) || empty ( $email_address ) ) { 
			echo '<span style="color: red;"><b>Username/Password/Email Address</b></span><br />
<br />
';
		}
		else if ( strlen( $password ) < 5 ) {
			echo '<span style="color: red;"><b>Password Must Be Greater Than 5 Characters.</b></span><br />
<br />
';
		}
		else if ( !ereg ( "@", $email_address ) ) {
			echo '<span style="color: red;"><b>Email Address Must Be A Valid Email Address.</b></span><br />
<br />
';
		}
		else if ( !eregi ( "^[a-z0-9\-_\.]+$", $username ) || !eregi ( "^[a-z0-9\-_\.]+$", $password ) ) {
			echo '<span style="color: red;"><b>Username And/Or Password Must Be Alphanumeric</b></span><br />
<br />
';
		}
		else if ( strlen ( $username ) > 15 || strlen ( $password ) > 15 ) {
			echo '<span style="color: red;"><b>Username And/Or Password Must Be Less Than 15 Characters</b></span><br />
<br />
';
		}
		else {
			$result_email_check = mysql_query( 'SELECT `email_address` FROM `users` WHERE `email_address` = \'' . $email_address . '\'' ); 
			$result_username_check = mysql_query( 'SELECT `username` FROM `users` WHERE `username` = \'' . $username . '\'' ); 
			$email_check = mysql_num_rows( $result_email_check ); 
			$username_check = mysql_num_rows( $result_username_check ); 
			if ( $email_check > 0 ) { 
				echo '<span style="color: red;"><b>Email Address Is Already In Database.<br>Choose Another Email Address And Try Again.</b></span><br />
<br />
';
				unset( $email_address ); 
			}
			else if ( $username_check > 0 ) { 
				echo '<span style="color: red;"><b>Username Is Already In Use. Choose Another Username And Try Again.</b></span><br />
<br />
';
				unset( $username ); 
			}
			else {
				$password = md5 ( $password );
				$insert_users = mysql_query( 'INSERT INTO `users` ( `username`, `password`, `email_address`, `type`, `registered_on` ) VALUES ( \'' . $username . '\', \'' . $password . '\', \'' . $email_address . '\', \'1\', now() )' );
				if ( session_is_registered ( 'referrer_id' ) ) {
					$update_referrer = mysql_query( "UPDATE users SET referrals=(referrals+1) WHERE username='$_SESSION[referrer_id]'" );
					unset( $_SESSION['referrer_id'] );
				} 
				echo '<b>Successfully Registered!</b>
			<table style="height: 10px;">
				<tr>
					<td></td>
				</tr>
			</table>
			<a href="' . $site_url . '/' . $main_filename . '">Click here to continue</a>
';
				$register_form = "false";
			}
		}
	}
}
else {
	unset( $_SESSION['referrer_id'] );
	$register_form = 'false';
	echo '<b>You Are Already Registered</b>
			<table style="height: 10px;">
				<tr>
					<td></td>
				</tr>
			</table>
			<a href="' . $site_url . '/' . $main_filename . '">Click here to continue</a>
';
}

if ( $register_form == "true" ) {
?>
			<table cellpadding="0" cellspacing="0" style="width: 100%; text-align: center;" class="main">
				<tr>
					<td style="width: 50%; text-align: right;"><b>Username</b></td>
					<td style="width: 7px;"></td>
					<td style="text-align: left;"><input type="text" name="username" size="25" class="textbox" /></td>
				</tr>
				<tr>
					<td style="height: 5px;"></td>
				</tr>
				<tr>
					<td style="width: 50%; text-align:right;"><b>Password</b></td>
					<td style="width: 7px;"></td>
					<td style="text-align: left;"><input type="password" name="password" size="25" class="textbox" /></td>
				</tr>
				<tr>
					<td style="height: 5px;"></td>
				</tr>
				<tr>
					<td style="width: 50%; text-align: right;"><b>Email Address</b></td>
					<td style="width: 7px;"></td>
					<td style="text-align: left;"><input type="text" name="email_address" size="25" class="textbox" /></td>
				</tr>
<?php
	if ( session_is_registered ('referrer_id') ) {
		echo '				<tr>
					<td style="height: 5px;"></td>
				</tr>
				<tr>
					<td style="width: 50%; text-align: right;"><b>Referrer</b></td>
					<td style="width: 7px;"></td>
					<td style="text-align: left;"><i>', $_SESSION['referrer_id'], '</i></td>
				</tr>';
	}
?>
			</table>
			<table style="height: 5px;">
				<tr>
					<td></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" style="width: 100%; text-align: center;">
				<tr>
					<td style="width: 17px;"></td>
					<td><input type="submit" name="submit" value="Register" class="submit_button" /></td>
				</tr>
			</table>
<?php
}
else {
	echo '';
}
?>
		</td>
	</tr>
</table>
</form>