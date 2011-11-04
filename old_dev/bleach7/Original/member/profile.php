<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !isset ( $user_info['user_id'] ) ) {
	header( 'Location: ' . $site_path . '/login' );
	exit();
	ob_end_flush();
}
?>
<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main" style="width: 100%;">
	<tr>
		<td>
			<form name="form_usercp" method="post" enctype="multipart/form-data" action="<?php echo $site_path . '/usercp' ?>">
<?php
if(empty($errors))
{$errors ='null';}

function EditSuccess ( $type ) {
	global $site_path, $current_location;
	echo '<p style="text-align: center;" class="VerdanaSize1Main"><b>Your ', $type, ' has been successfully editted</b></p>
<table style="height: 10px;">
	<tr>
		<td></td>
	</tr>
</table>
<a href="', $current_location, '">Back to editting your ' . strtolower( $type ) . '</a>';
}


$edit_success = 0;

if(isset($_GET['do']))
{$do = $_GET['do'];}
else
{$do = 'null';} 


function DisplayErrors () {
	global $errors;
	if ( count ( $errors ) == 1 ) {
		echo '<b>The following error was found:</b>';
	}
	else {
		echo '<b>The following errors were found:</b>';
	}
	echo '<ul type="square">';
	foreach ( $errors as $var ) {
		echo '<li>', $var, '</li>';
	}
	echo '</ul>';
}

//function by Daijoubu from PHP.net comments
function FilesizeRemote ( $url, $timeout = 2 ) {
	$url = parse_url( $url );
	if ( $fp = @fsockopen($url['host'], ( $url['port'] ? $url['port'] : 80 ), $errno, $errstr, $timeout ) ) {
		fwrite( $fp, 'HEAD ' . $url['path'].$url['query'] . ' HTTP/1.0\r\nHost: ' . $url['host'] . '\r\n\r\n' );
		stream_set_timeout( $fp, $timeout );
		while (!feof($fp)) {
			$size = fgets( $fp, 4096 );
			if ( stristr( $size, 'Content-Length' ) !== false) {
				$size = trim( substr( $size, 16 ) );
				break;
			}
		}
		fclose ( $fp );
	}
	return is_numeric( $size ) ? intval( $size ) : false;
}

if ( isset ( $_POST['edit_profile'] ) ) {
	$email_address = mysql_real_escape_string ( htmlspecialchars ( $_POST['email_address'], ENT_QUOTES ) );
	$gender = $_POST['gender'];
	$loyalty = $_POST['loyalty'];
	$bday_month = $_POST['bday_month'];
	$bday_day = $_POST['bday_day'];
	$bday_year = mysql_real_escape_string ( htmlspecialchars ( $_POST['bday_year'], ENT_QUOTES ) );
	$location = mysql_real_escape_string ( htmlspecialchars ( $_POST['location'], ENT_QUOTES ) );
	$website = mysql_real_escape_string ( htmlspecialchars ( $_POST['website'], ENT_QUOTES ) );
	$msn = mysql_real_escape_string ( htmlspecialchars ( $_POST['msn'], ENT_QUOTES ) );
	$aim = mysql_real_escape_string ( htmlspecialchars ( $_POST['aim'], ENT_QUOTES ) );
	$biography = mysql_real_escape_string ( htmlspecialchars ( $_POST['biography'], ENT_QUOTES ) );

	if ( isset ( $bday_year ) && $bday_year < 1910 ) {
		$bday_year = '';
	}
	if ( !eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$', $email_address ) ) {
		$errors[] = 'You must enter a valid email address';
	}

	if(empty($errors))
	{$errors = null;}
	if ( count ( $errors ) > 0 ) {
		DisplayErrors();
		$edit_success = 0;
	}
	else {
		$update = mysql_query( 'UPDATE `users` SET `email_address` = \'' . $email_address . '\', `gender` = \'' . $gender . '\', `bday_month` = \'' . $bday_month . '\', `bday_day` = \'' . $bday_day . '\', `bday_year` = \'' . $bday_year . '\', `location` = \'' . $location . '\', `website` = \'' . $website . '\', `msn` = \'' . $msn . '\', `aim` = \'' . $aim . '\', `biography` = \'' . $biography . '\', `loyalty` = \'' . $loyalty .  '\' WHERE `user_id` = \'' . $user_info['user_id'] . '\'' ) or die(mysql_error());
		$current_location = '?page=member/usercp&amp;do=editprofile';
		EditSuccess ( 'Profile');
		$edit_success = 1;
	}
}

if ( isset ( $_POST['edit_options'] ) ) {
	$timezone = $_POST['timezone'];
	$dst = $_POST['dst'];
	$category = $_POST['category'];
	$update = mysql_query ( 'UPDATE `users` SET `timezone` = \'' . $timezone . '\', `dst` = \'' . $dst . '\', `category` = \'' . $category . '\' WHERE `user_id` = \'' . $user_info['user_id'] . '\'' );
	$current_location = '?page=member/usercp&amp;do=editoptions';
	EditSuccess ( 'Options' );
	$edit_success = 1;
}


if ( isset ( $_POST['edit_avatar'] ) ) {
	$filesize_limit = 51200; //50 KB
	$avatar_custom = mysql_real_escape_string ( $_POST['avatar_custom'] );
	$avatar_upload = mysql_real_escape_string ( $_FILES['avatar_upload']['tmp_name'] );

	if ( $_POST['avatar_choice'] == 'n' || ( empty ( $avatar_custom ) && empty ( $avatar_upload ) ) ) {
		$update = mysql_query ( 'UPDATE `users` SET `avatar` = \'\' WHERE `user_id`=\'' . $user_info['user_id'] . '\'' );
		$current_location = '?page=member/usercp&amp;do=editavatar';
		EditSuccess ( 'Avatar' );
		$edit_success = 1;
	}
	else if ( $_POST['avatar_choice'] == "y" ) {
		if ( !empty ( $avatar_custom ) && !empty ( $avatar_upload ) ) {
			$avatar_type = 'upload';
		}
		else if ( empty ( $avatar_custom ) && !empty ( $avatar_upload ) ) {
			$avatar_type = 'upload';
		}
		else if ( !empty ( $avatar_custom ) && empty ( $avatar_upload ) ) {
			$avatar_type = 'custom';
		}

		if ( $avatar_type == 'custom' ) {
			$avatarc_filesize = FilesizeRemote ( $avatar_custom, '2' );
			if ( empty ( $avatar_custom ) ) {
				$errors[] = 'You must enter a URL to an avatar';
			}
			if ( $fp = @fopen ( $avatar_custom, 'r' ) ) {
				list( $avatarc_width, $avatarc_height, $avatarc_type ) = getimagesize( $avatar_custom );
				if ( $avatarc_type != 1 ) {
					$errors[] = 'Avatar must be a GIF file';
				}
			}
			else {
				$errors[] = 'The specified avatar does not exist';
			}
			if ( $avatarc_filesize > $filesize_limit ) {
				$errors[] = 'Avatar must be less than 50 KB.';
			}
			if ( count ( $errors ) > 0 ) {
				DisplayErrors();
				$edit_success = 0;
			}
			else {
				$update = mysql_query( 'UPDATE `users` SET `avatar` = \'' . $avatar_custom . '\' WHERE `user_id` = \'' . $user_info['user_id'] . '\'' );
				$current_location = '?page=member/usercp&amp;do=editavatar';
				EditSuccess ( 'Avatar' );
				$edit_success = 1;
			}
		}
		else if ( $avatar_type == 'upload' ) {
			if ( $_FILES['avatar_upload']['type'] != 'image/gif' ) { 
				$errors[] = 'Avatar must be a GIF file.';
			}
			if ( $_FILES['avatar_upload']['size'] > $filesize_limit ) {
				$errors[] = 'Avatar must be less than 50 KB.';
			}
			if ( count ( $errors ) > 1 ) {
				DisplayErrors();
				$edit_success = 0;
			}
			else {
				copy ( $_FILES['avatar_upload']['tmp_name'], $script_folder . '/images/avatars/' . $user_info['user_id'] . '.gif' ) or die ( 'Couldn\'t Copy File' );
				$avatar_url = $site_url . '/' . $script_folder . '/images/avatars/' . $user_info['user_id'] . '.gif';
				$update = mysql_query( 'UPDATE `users` SET `avatar` = \'' . $avatar_url . '\' WHERE `user_id` = \'' . $user_info['user_id'] . '\'' );
				$current_location = '?page=member/usercp&amp;do=editavatar';
				EditSuccess ( 'Avatar' );
				$edit_success = 1;
			}
		}
	} 
}

if ( isset ( $_POST['edit_password'] ) ) {
	if ( empty ( $password_old ) ) { 
		$errors[] = 'You must enter your old password';
	}
	if ( md5 ( $password_old ) != $user_info['password'] ) { 
		$errors[] = 'The password you have entered doesn\'t match the one in our database';
	}
	if ( strlen ( $password_new ) < 3 || strlen ( $password_new ) > 20 ) {
		$errors[] = 'The new password must be between 3 to 20 characters long';
	}
	if ( $password_new != $re_password_new ) { 
		$errors[] = 'The new password and the confirmed passwords don\'t match';
	}

	if ( count ( $errors ) > 0 ) {
		DisplayErrors();
		$edit_success = 0;
	}
	else {
		$password_new = $_POST['password_new'];
		$email_msg = $user_info['username'] . ',\n\nThis email has been sent to inform you that your password has been changed.\n\nYour new password is: ' . $password_new . '.\n\nRegards,\n\n' . $sitetitle . '\n' . $site_url;
		//mail ( $user_info['email_address'], 'Password Change Notice At ' . $sitetitle, $email_msg, 'From: ' . $sitetitle . ' ' . $contact_email );
		$password_new = md5 ( $password_new );
		$update = mysql_query( 'UPDATE `users` SET `password` = \'' . $password_new . '\' WHERE `user_id` = \'' . $user_info['user_id'] . '\'' );
		$current_location = '?page=member/usercp&amp;do=editpassword';
		EditSuccess ( 'Password' );
		$edit_success = 1;
		setcookie( "user_id", "", time()-60*60*24*30, "/", "$_SERVER[HTTP_HOST]", 0 );
		setcookie( "password", "", time()-60*60*24*30, "/", "$_SERVER[HTTP_HOST]", 0 );
		setcookie( "user_id", $user_info['user_id'], time()+60*60*24*30, "/", "$_SERVER[HTTP_HOST]", 0 );
		setcookie( "password", $password_new, time()+60*60*24*30, "/", "$_SERVER[HTTP_HOST]", 0 );
		ob_end_flush();
	}
}

if ( $edit_success == 0 ) {
	if ( $do == 'editpassword' ) {
	$file_title = "UserCP:split:Edit Password";
?>

				<fieldset>
				<legend class="VerdanaSize1Main">Old Password</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td>
							<input type="password" name="password_old" maxlength="20" style="width: 300px" class="form" />
						</td>
					</tr>
				</table>
				</fieldset>

				<table style="height: 10px;" class="VerdanaSize1Main">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">New Password</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td>Password:<br />
							<input type="password" name="password_new" maxlength="20" style="width: 200px" class="form" /></td>
						<td style="width: 5px;"></td>
						<td>Confirm Password:<br />
							<input type="password" name="re_password_new" maxlength="20" style="width: 200px" class="form" /></td>
					</tr>
				</table>
				</fieldset>

				<p style="text-align: center;">
					<input type="submit" name="edit_password" value="Edit Password" class="form" />
					<input type="button" value="Reset Fields" class="form" onclick="document.form_usercp.reset()" />
				</p>

<?php
}
else if ( $do == 'editavatar' ) {
	$file_title = "UserCP:split:Edit Avatar";
?>

				<fieldset>
				<legend class="VerdanaSize1Main">Current Avatar</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">Avatars are small images that are located near your username in various parts of the site i.e. Comments, Profile. If you wish to have an avatar, select the &quot;Use an avatar&quot; choice and either upload your own or use a custom one. Otherwise, select the &quot;Do not use an avatar&quot; option where a custom image stating you do not have an avatar will be used instead.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
								<tr>
									<td>
<?php
	$avatar_no = 'null';
	if ( empty ( $user_info['avatar'] ) ) {
		$avatar_no = 'checked';
		echo '<img src="', $site_url, '/', $script_folder, '/images/avatars/none.gif" alt="', $user_info['username'], '" style="width: 90px; height: 90px;" />';
	}
	else {
		list ( $avatar_width, $avatar_height ) = getimagesize ( $user_info['avatar'] );
		$avatar_yes = 'checked';
		if ( $avatar_width > 60 || $avatar_height > 60 ) {
			echo '<img src="', $user_info['avatar'], '" alt="', $user_info['username'],'" style="width: 90px; height: 90px;" />';
		}
		else {
			echo '<img src="', $user_info['avatar'], '" alt="', $user_info['username'], '" />';
		}
	}
?>
									</td>
									<td style="width: 5px;"></td>
									<td>
										<input type="radio" name="avatar_choice" value="n" <?php echo $avatar_no ?> />Do not use an avatar<br />
										<input type="radio" name="avatar_choice" value="y" <?php echo $avatar_yes ?> />Use an avatar</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">Custom Avatar</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">All avatars that you choose to use must be less than 50 KB and no greater than 90 x 90 in size. If the avatar exceeds 90 x 90, it will automatically be resized. At the moment, the available file types for avatars are GIF. If you wish to use an avatar that is already uploaded on a remote location, enter the URL below. Otherwise, browse for an existing avatar on your computer.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>Enter the URL to the avatar:<br />
							<input type="text" name="avatar_custom" style="width: 300px" class="form" /><br />
							Look for an avatar on your computer:<br />
							<input type="file" name="avatar_upload" value="Browse" style="width: 375px; height: 18px" class="form" /></td>
					</tr>
				</table>
				</fieldset>

				<p style="text-align: center">
					<input type="submit" name="edit_avatar" value="Edit Avatar" class="form" />
				</p>

<?php
	}
	else if ( $do == 'editoptions' ) {
		$file_title = "UserCP:split:Edit Options";
?>

				<fieldset>
				<legend class="VerdanaSize1Main">Timezone</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">In order to view the correct time in your location of the world for various parts of the site (i.e. News, Comments), you will need to specify your timezone. Simply select a timezone from the list below and the timestamps will automatically be corrected.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>Timezone:<br />
						<select name="timezone" class="form">
							<option value="-12" <?php if ( $user_info['timezone'] == "-12" ) { echo "selected=\"selected\""; } ?>>(GMT -12:00) Eniwetok, Kwajalein</option>
							<option value="-11" <?php if ( $user_info['timezone'] == "-11" ) { echo "selected=\"selected\""; } ?>>(GMT -11:00) Midway Island, Samoa</option>
							<option value="-10" <?php if ( $user_info['timezone'] == "-10" ) { echo "selected=\"selected\""; } ?>>(GMT -10:00) Hawaii</option>
							<option value="-9" <?php if ( $user_info['timezone'] == "-9" ) { echo "selected=\"selected\""; } ?>>(GMT -9:00) Alaska</option>
							<option value="-8" <?php if ( $user_info['timezone'] == "-8" ) { echo "selected=\"selected\""; } ?>>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
							<option value="-7" <?php if ( $user_info['timezone'] == "-7" ) { echo "selected=\"selected\""; } ?>>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
							<option value="-6" <?php if ( $user_info['timezone'] == "-6" ) { echo "selected=\"selected\""; } ?>>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
							<option value="-5" <?php if ( $user_info['timezone'] == "-5" ) { echo "selected=\"selected\""; } ?>>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
							<option value="-4" <?php if ( $user_info['timezone'] == "-4" ) { echo "selected"; } ?>>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
							<option value="-3.5" <?php if ( $user_info['timezone'] == "-3.5" ) { echo "selected=\"selected\""; } ?>>(GMT -3:30) Newfoundland</option>
							<option value="-3" <?php if ( $user_info['timezone'] == "-3" ) { echo "selected=\"selected\""; } ?>>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
							<option value="-2" <?php if ( $user_info['timezone'] == "-2" ) { echo "selected"; } ?>>(GMT -2:00) Mid-Atlantic</option>
							<option value="-1" <?php if ( $user_info['timezone'] == "-1" ) { echo "selected"; } ?>>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
							<option value="0" <?php if ( $user_info['timezone'] == "0" ) { echo "selected=\"selected\""; } ?>>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
							<option value="1" <?php if ( $user_info['timezone'] == "1" ) { echo "selected=\"selected\""; } ?>>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
							<option value="2" <?php if ( $user_info['timezone'] == "2" ) { echo "selected=\"selected\""; } ?>>(GMT +2:00) Kaliningrad, South Africa</option>
							<option value="3" <?php if ( $user_info['timezone'] == "3" ) { echo "selected"; } ?>>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
							<option value="3.5" <?php if ( $user_info['timezone'] == "3.5" ) { echo "selected=\"selected\""; } ?>>(GMT +3:30) Tehran</option>
							<option value="4" <?php if ( $user_info['timezone'] == "4" ) { echo "selected=\"selected\""; } ?>>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
							<option value="4.5" <?php if ( $user_info['timezone'] == "4.5" ) { echo "selected=\"selected\""; } ?>>(GMT +4:30) Kabul</option>
							<option value="5" <?php if ( $user_info['timezone'] == "5" ) { echo "selected=\"selected\""; } ?>>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
							<option value="5.5" <?php if ( $user_info['timezone'] == "5.5" ) { echo "selected=\"selected\""; } ?>>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
							<option value="6" <?php if ( $user_info['timezone'] == "6" ) { echo "selected=\"selected\""; } ?>>(GMT +6:00) Almaty, Dhaka, Colombo</option>
							<option value="7" <?php if ( $user_info['timezone'] == "7" ) { echo "selected=\"selected\""; } ?>>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
							<option value="8" <?php if ( $user_info['timezone'] == "8" ) { echo "selected=\"selected\""; } ?>>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
							<option value="9" <?php if ( $user_info['timezone'] == "9" ) { echo "selected=\"selected\""; } ?>>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
							<option value="9.5" <?php if ( $user_info['timezone'] == "9.5" ) { echo "selected=\"selected\""; } ?>>(GMT +9:30) Adelaide, Darwin</option>
							<option value="10" <?php if ( $user_info['timezone'] == "10" ) { echo "selected=\"selected\""; } ?>>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
							<option value="11" <?php if ( $user_info['timezone'] == "11" ) { echo "selected=\"selected\""; } ?>>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
							<option value="12" <?php if ( $user_info['timezone'] == "12" ) { echo "selected=\"selected\""; } ?>>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
						</select></td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td style="text-align: justify">Additionally, you may choose to turn Daylight Savings Time on or off. Just select the option of your choice below. </td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>DST Options:<br />
							<select name="dst" class="form">
								<option value="1" <?php if ( $user_info['dst'] == "1" ) { echo "selected=\"selected\""; } ?>>Turn Daylight Savings Time On</option>
								<option value="0" <?php if ( $user_info['dst'] == "0" ) { echo "selected=\"selected\""; } ?>>Turn Daylight Savings Time Off</option>
							</select></td>
					</tr>
				</table>
				</fieldset>
				<br />
				<fieldset>
				<legend class="VerdanaSize1Main">Viewing News</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">You can also customize what news you want to view.  Just change the options below to whatever news you would prefer to view.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>News Options:<br />
							<select name="category" class="form">
								<option value="0" <?php if ( $user_info['category'] == "0" ) { echo "selected=\"selected\""; } ?>>Show All News</option>
								<option value="1" <?php if ( $user_info['category'] == "1" ) { echo "selected=\"selected\""; } ?>>Show Just Site News</option>
								<option value="2" <?php if ( $user_info['category'] == "2" ) { echo "selected=\"selected\""; } ?>>Show Just Manga News</option>
								<option value="3" <?php if ( $user_info['category'] == "3" ) { echo "selected=\"selected\""; } ?>>Show Just Anime News</option>
								<option value="4" <?php if ( $user_info['category'] == "4" ) { echo "selected=\"selected\""; } ?>>Show Site and Manga News</option>
								<option value="5" <?php if ( $user_info['category'] == "5" ) { echo "selected=\"selected\""; } ?>>Show Site and Anime News</option>
								<option value="6" <?php if ( $user_info['category'] == "6" ) { echo "selected=\"selected\""; } ?>>Show Manga and Anime News</option>
							</select></td>
					</tr>
				</table>
				</fieldset>
				<p style="text-align: center;">
					<input type="submit" name="edit_options" value="Edit Options" class="form" />
				</p>

<?php
	}
	else if ( $do == "editprofile" ) {
		$file_title = "UserCP:split:Edit Profile";
?>

				<fieldset>
				<legend class="VerdanaSize1Main">Email Address</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">In order for us to contact you when necessary, you must input a valid email address. Note that we will never send you any kind of spam or harmful emails of any kind. Furthermore, your email address will be used only for contact purposes and will not be shared with any third parties.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>
							<input type="text" name="email_address" value="<?php echo stripslashes ( $user_info['email_address'] ) ?>" style="width:300px" class="form" /></td>
					</tr>
				</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">Gender</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">In order to gain a better understanding of who you are, please specify what gender you are by selecting one from the list below. If you wish to not reveal your gender for whatever reason, please select &quot;Not Telling&quot;.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>
							<select name="gender" class="form">
<?php
		$gender_array = array ( "n" => "Not Telling", "m" => "Male", "f" => "Female" );
		foreach ( $gender_array as $key => $var ) {
			if ( $key == $user_info['gender'] ) {
				echo "								<option selected value='$key'>$var</option>
";
			}
			else {
				echo "								<option value='$key'>$var</option>
";
			}
		}
?>
							</select></td>
					</tr>
				</table>
				</fieldset>
				<br />
				<fieldset>
				<legend class="VerdanaSize1Main">Alignment</legend>
				<table cellpadding="0" cellspacing="0" class="main"><tr>
				<td style="text-align: justify">
				For whom do you swear your allegiance?
				</td></tr>
				<tr><td height="10"></td></tr>
				<tr><td>
				<select name="loyalty" class="form">
				<?php
				$loyalty_array = array ( "0" => "Soul Society", "1" => "Hueco Mundo" );
				foreach ( $loyalty_array as $loyaltykey => $loyaltyvar ) {
				if ( $loyaltykey == $user_info['loyalty'] ) {
				echo "<option selected value='$loyaltykey'>$loyaltyvar</option>";
				} else {
				echo "<option value='$loyaltykey'>$loyaltyvar</option>";
				}
				}
				?>
				</select>
				</td></tr></table>
				</fieldset>
				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">Birthday</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">In order to see when exactly our members were born, and to let the other members see when your birthday is, we ask that you specify your birthday. Inputting the year that you were born is optional.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
								<tr>
									<td>Month:<br />
										<select name="bday_month" class="form">
<?php
		if ( empty ( $user_info['bday_month'] ) ) {
			echo "											<option>-</option>
";
		}
		$bday_month_array = array ( "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );
		foreach ( $bday_month_array as $var ) {
			if ( $var == $user_info['bday_month'] ) {
				echo "											<option selected value='$var'>$var</option>
";
			}
			else {
				echo "											<option value='$var'>$var</option>
";
			}
		}
?>
										</select></td>
									<td style="width: 10px;"></td>
									<td>Day:<br/>
										<select name="bday_day" class="form">
<?php
		if ( $user_info['bday_day'] == 0 ) {
			echo "											<option>-</option>
";
		}
		for ( $x = 1; $x <= 31; $x++ ) {
			if ( $x == $user_info['bday_day'] ) {
				echo "											<option selected value='$x'>$x</option>";
			}
			else {
				echo "											<option value='$x'>$x</option>";
			}
		}
?>
										</select></td>
									<td style="width: 10px;"></td>
									<td>Year:<br />
										<input type="text" name="bday_year" size="10" maxlength="4" class="form" value="<?php 
if ( $user_info['bday_year'] != 0 ) {
	echo stripslashes ( $user_info['bday_year'] );
}
?>" /></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">Location</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">It is always interesting to know from what parts of the world our members are visiting the site from. For that matter, we ask that you specify where in the world you are from. However, if you do not wish to share this information, simply leave it blank.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>
							<input type="text" name="location" value="<?php echo stripslashes ( $user_info['location'] ) ?>" style="width: 300px" class="form" /></td>
					</tr>
				</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">Website</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">If you want the other visitors on this site to know your website or simply to visit it, please input a valid website URL starting with http://. However, if you do not have a website or do not wish to share this information, simply leave it blank.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>
							<input type="text" name="website" maxlength="50" value="<?php echo stripslashes ( $user_info['website'] ) ?>" style="width:300px" class="form" /></td>
					</tr>
				</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">MSN Messenger</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">If you wish to interact with other members of the site using MSN Messenger, please input the email address that you use to access it. However, if you do not use MSN Messenger, do not have an account or do not wish to share this information, simply leave it blank.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>
							<input type="text" name="msn" value="<?php echo stripslashes ( $user_info['msn'] ) ?>" style="width: 300px" class="form" /></td>
					</tr>
				</table>
				</fieldset>

				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">AIM Screen Name</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">If you wish to interact with other members of the site using AIM (AOL Instant Messaging), please input your AIM account. However, if you do not use AIM, do not have an account or do not wish to share this information, simply leave it blank.</td></tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td><input type="text" name="aim" value="<?php echo stripslashes ( $user_info['aim'] ) ?>" style="width: 300px" class="form" /></td>
					</tr>
				</table>
				</fieldset>



				<table style="height: 10px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<fieldset>
				<legend class="VerdanaSize1Main">Biography</legend>
				<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
					<tr>
						<td style="text-align: justify">If you have any other information that you wish to share with the other members of the site, please input it below. However, if you do not wish to share any more information, simply leave it blank.</td>
					</tr>
					<tr>
						<td style="height: 10px;"></td>
					</tr>
					<tr>
						<td>
							<textarea name="biography" style="width: 300px; height: 100px; overflow: auto" class="form"><?php echo stripslashes ( $user_info['biography'] ) ?></textarea></td>
					</tr>
				</table>
				</fieldset>

				<p style="text-align: center;">
					<input type="submit" name="edit_profile" value="Edit Profile" class="form" />
					<input type="button" value="Reset Fields" class="form" onclick="document.form_usercp.reset()" />
				</p>

<?php
	}
	/*else {
		header ( 'Location: ' . $site_path . '/usercp&do=editprofile' );
	}*/
} 
?>

				<input type="hidden" name="submit_usercp" />
			</form></td>
	</tr>
</table>