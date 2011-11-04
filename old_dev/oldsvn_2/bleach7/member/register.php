<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( ereg ( '/register.php', $_SERVER['SCRIPT_NAME'] ) ) {
	header ( 'Location: ' . $site_url . '/?page=member/register' );
}

$file_title = 'Registration';
$register_form = true;

if(isset($_POST['dst']))
{ $dst = $_POST['dst']; }
else
{ $dst = 0; }

if(isset($_POST['timezone']))
{ $timezone = $_POST['timezone']; }
else
{ $timezone = -6; }

echo '<table cellpadding="0" cellspacing="0" style="width: 100%;" class="VerdanaSize1Main">
	<tr>
		<td>
			<form action="?page=member/register" method="post">
';
if ( !isset ( $user_info['user_id'] ) ) {
	if ( isset ( $_POST['submit'] ) ) {
		$username = stripslashes( $_POST['username'] ); 
		$password = stripslashes( $_POST['password'] ); 
		$re_password = stripslashes( $_POST['re_password'] ); 
		$email_address = stripslashes( $_POST['email_address'] ); 
		$re_email_address = stripslashes( $_POST['re_email_address'] ); 
		//$verification = stripslashes( $_POST['verification'] );
		$email_msg = 'Welcome $username,\n\nThank you for taking your time to register at $sitetitle. You are now officially a member and we are glad to have you as one. Your username and password are as follows:\n\nUsername: ' . $username . '\nPassword: ' . $password . '\n\nOnce again, thank you for joining $sitetitle and we hope that you\'ll enjoy your stay.\n\n$sitetitle\n$site_url';
		$errors = 0;
		if ( strlen ( $username ) < 3 || strlen ( $username ) > 20 ) {
			echo '<i>Username must be between 3 to 20 characters long</i><br />';
			$errors++;
		}
		if ( !eregi ( '^[a-z0-9\-_\.]+$', $username ) ) {
			echo '<i>Username must be alphanumeric</i><br />';
			$errors++;
		}
		if ( strlen ( $password ) < 3 || strlen ( $password ) > 20 ) {
			echo '<i>Password must be between 3 to 20 characters long</i><br />';
			$errors++;
		}
		if ( !eregi ( '^[a-z0-9\-_\.]+$', $password ) ) {
			echo '<i>Password must be alphanumeric</i><br />';
			$errrors++;
		}
		if ( $password != $re_password ) {
			echo '<i>The passwords do not match</i><br />';
			$errors++;
		}
		if ( empty ( $email_address ) ) {
			echo '<i>You cannot leave the Email Address field empty</i><br />';
			$errors++;
		} 
		if ( $email_address != $re_email_address ) {
			echo '<i>The email addresses do not match</i><br />';
			$errors++;
		}
		if ( !eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$', $email_address ) ) {
			echo '<i>You must enter a valid email address</i><br />';
			$errors++;
		}
		if ( $errors >= 1 )
                { echo '<b>There was a problem with your registartion, please try again.</b>'; }

		else {
			$errors2 = 0;
			$result_email_check = mysql_query( 'SELECT `email_address` FROM `users` WHERE `email_address` = \'' . $email_address . '\'' ); 
			$result_username_check = mysql_query( 'SELECT `username` FROM `users` WHERE `username` = \'' . $username . '\'' ); 

			$email_check = mysql_num_rows( $result_email_check ); 
			$username_check = mysql_num_rows( $result_username_check ); 

			if ( $email_check > 0 ) { 
				echo '<i>That email address has already been registered with by another user</i><br />';
				$errors2++;
			}
			if ( $username_check > 0 ) { 
				echo '<i>That username has already been registered with by another user</i><br />';
				$errors2++;
			}

			if ( $errors2 >= 1 )
                        { echo '<b>There was a problem with your registartion, please try again.</b>'; }
			elseif ($errors == 0 && $errors2 == 0)
			{
				//mail ( "$email_address", "Welcome To $sitetitle!", "$email_msg", "From: $sitetitle <$contact_email>" );
				$password = md5 ( $password );
				$insert_users = mysql_query( 'INSERT INTO `users` ( username, password, email_address, type, registered_on, timezone, dst, ip_address, category ) VALUES ( \'' . $username . '\', \'' . $password . '\', \'' . $email_address . '\', \'1\', ' . $timenow . ', \'' . $timezone . '\', \'' . $dst . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\', \'0\' )' );

				echo '				<p style="width: 100%; text-align:center;"><b>You have been successfully registered!</b></p>
				<p style="width: 100%; text-align:center;"><a href="', $site_url, '">Click here to continue</a></p>
';
				$register_form = false;
			}
		}
	}
}
else {
	$register_form = false;
	echo '				<p style="width: 100%; text-align: center;"><b>You are already registered</b></p>
				<p style="width: 100%; text-align:center;"><a href="', $site_url, '">Click here to continue</a></p>
';
}
if ( $register_form == true ) {
	//echo $verify_new_string;
?>				<fieldset>
					<legend class="VerdanaSize1Main">Username</legend>
					<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
						<tr>
							<td>
								<b>*Username:<br /></b>
								<input type="text" name="username" maxlength="30" value="<?php if(isset($_POST['username'])){ echo $_POST['username']; } ?>" style="width: 300px;" class="form" /></td>
						</tr>
					</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
					<legend class="VerdanaSize1Main">Password</legend>
					<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
						<tr>
							<td>
								<b>*Password:<br /></b>
								<input type="password" name="password" maxlength="20" style="width: 200px;" class="form" /></td>
							<td style="width: 5px;"></td>
							<td>
								<b>*Confirm Password:<br /></b>
								<input type="password" name="re_password" maxlength="20" style="width: 200px;" class="form" /></td>
						</tr>
					</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
					<legend class="VerdanaSize1Main">Email Address</legend>
					<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
						<tr>
							<td>
								<b>*Email Address:<br /></b>
								<input type="text" name="email_address" value="<?php if(isset($_POST['email_address'])){ echo $_POST['email_address']; } ?>" style="width: 200px;" class="form" /></td>
							<td style="width: 5px;"></td>
							<td>
								<b>*Confirm Email Address:<br /></b>
								<input type="text" name="re_email_address" value="<?php if(isset($_POST['re_email_address'])){ echo $_POST['re_email_address']; } ?>" style="width: 200px;" class="form" /></td>
						</tr>
					</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
					<legend class="VerdanaSize1Main">Timezone</legend>
					<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
						<tr>
							<td>
								<b>Timezone:<br /></b>
								<select name="timezone" class="VerdanaSize1Main">
									<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>
									<option value="-11">(GMT -11:00) Midway Island, Samoa</option>
									<option value="-10">(GMT -10:00) Hawaii</option>
									<option value="-9">(GMT -9:00) Alaska</option>
									<option value="-8">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
									<option value="-7">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
									<option value="-6">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
									<option value="-5" selected="selected">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
									<option value="-4">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
									<option value="-3.5">(GMT -3:30) Newfoundland</option>
									<option value="-3">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
									<option value="-2">(GMT -2:00) Mid-Atlantic</option>
									<option value="-1">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
									<option value="0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
									<option value="1">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
									<option value="2">(GMT +2:00) Kaliningrad, South Africa</option>
									<option value="3">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
									<option value="3.5">(GMT +3:30) Tehran</option>
									<option value="4">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
									<option value="4.5">(GMT +4:30) Kabul</option>
									<option value="5">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
									<option value="5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
									<option value="6">(GMT +6:00) Almaty, Dhaka, Colombo</option>
									<option value="7">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
									<option value="8">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
									<option value="9">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
									<option value="9.5">(GMT +9:30) Adelaide, Darwin</option>
									<option value="10">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
									<option value="11">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
									<option value="12">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
								</select></td>
						</tr>
						<tr>
							<td style="height: 10px;"></td>
						</tr>
						<tr>
							<td>
								<b>DST Options:<br /></b>
								<select name="dst" class="VerdanaSize1Main">
									<option value="1" <?php if ( $dst == "1" ) { echo "selected"; } ?>>Turn Daylight Savings Time On</option>
									<option value="0" <?php if ( $dst == "0" ) { echo "selected"; } ?>>Turn Daylight Savings Time Off</option>
								</select></td>
						</tr>
					</table>
				</fieldset>

				<p style="width: 100%; text-align: center;">
					<input type="submit" name="submit" value="Register" class="form" />
					<input type="button" value="Reset Fields" class="form" />
				</p>
<?php
}
else {
	echo '';
}
?>
			</form>
		</td>
	</tr>
</table>


