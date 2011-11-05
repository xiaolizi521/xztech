<?php
if ( isset ( $user_info['user_id'] ) ) {
header ( "Location: $site_path/news" );
exit();
}
include "./member/settings.php";

$site_main = "$site_url/$main_filename";

echo "<form method='post'>";

$validation_code = md5( time() );

if (!isset($_GET['do']))
{ $do = ''; }
else
{ $do = $_GET['do']; }

if (!isset($errors))
{ $errors = ''; }


if ( isset ( $_POST['pwr_submit'] ) ) {
$pwr_email_address = stripslashes ( $_POST['pwr_email_address'] );
$pwr_username = stripslashes ( $_POST['pwr_username'] );

if ( empty ( $pwr_email_address ) && empty ( $pwr_username ) ) {
$errors[] = "You must enter an email address or username.";
}

if ( !empty ( $pwr_email_address ) && !empty ( $pwr_username ) ) {
$errors[] = "You cannot enter both an email address and a username.";
} else {
if ( !empty ( $pwr_email_address ) ) {
$result_emailcheck = mysql_query ( "SELECT user_id, username, email_address FROM users WHERE email_address='$pwr_email_address'" );
if ( mysql_num_rows ( $result_emailcheck ) == 0 ) {
$errors[] = "The email address that you have entered does not match an email address in our database";
} else {
$pwr_info = mysql_fetch_array ( $result_emailcheck );
}
} elseif ( !empty ( $pwr_username ) ) {
$result_usernamecheck = mysql_query ( "SELECT user_id, username, email_address FROM users WHERE username='$pwr_username'" );
if ( mysql_num_rows ( $result_usernamecheck ) == 0 ) {
$errors[] = "The username that you have entered does not match a username in our database";
} else {
$pwr_info = mysql_fetch_array ( $result_usernamecheck );
}
}
}

if ( count ( $errors ) > 1 ) {
if ( count ( $errors ) == 2 ) {
echo "<b>The following error was found:</b>";
} else {
echo "<b>The following errors were found:</b>";
}
echo "<ul type='square'>";
foreach ( $errors as $var ) {
echo "<li>$var</li>";
}
echo "</ul>";
} else {
$result_validation_exists = mysql_query ( "SELECT user_id FROM pwrecover WHERE user_id='$pwr_info[user_id]'" );
if ( mysql_num_rows ( $result_validation_exists ) > 0 ) {
$delete_validation = mysql_query ( "DELETE FROM pwrecover WHERE user_id='$pwr_info[user_id]'" );
}
$insert_validation = mysql_query ( "INSERT INTO pwrecover ( user_id, validation_code ) VALUES ( '$pwr_info[user_id]', '$validation_code' )" );
$email_msg = "$pwr_info[username],\nThis email has been sent because you or another person has decided to recover your lost password. If you did not request to recover your password, please ignore and delete this email. Otherwise, follow the steps given below to recover your lost password.\n\n";
$email_msg .= "------------------------------\nActivation Instructions\n------------------------------\n\nIn order to assure that you requested to recover your password, we need you to validate this email by clicking (or copy and pasting) the link below:\n\n$site_main$site_path/pwrecover&do=validate&uid=$pwr_info[user_id]&aid=$validation_code\n\n";
$email_msg .= "------------------------------\nActivation Not Working?\n------------------------------\n\nIf you were unable to validate your account, please click (or copy and paste) the link below:\n\n$site_main$site_path/pwrecover&do=validate\n\nIt will ask you for your user id and the validation code. Just type (or copy and paste) the following user id and validation code provided below:\n\nUser ID: $pwr_info[user_id]\nValidation Code: $validation_code\n\n\n\n";
$email_msg .= "If you are still having problems, please send an email to the adminstrator and we will try to fix the problem asap.\n\nThank You,\n\n$sitetitle\n$site_main";
mail ( "$pwr_info[email_address]", "Password Recovery Information At $sitetitle", "$email_msg", "From: $sitetitle <$contact_email>" );
echo "<script>alert('An email has been sent to the $pwr_info[email_address] with information on how to recover your password. You will now be redirected to the front page.')</script>";
echo "<script>document.location='$site_path/news'</script>";
}

}



if ( isset ( $_POST['pwr_validate'] ) ) {
$pwr_user_id = stripslashes ( $_POST['pwr_user_id'] );
$pwr_validation_code = stripslashes ( $_POST['pwr_validation_code'] );
$pwr_new_password = stripslashes ( $_POST['pwr_new_password'] );
$pwr_new_password_verify = stripslashes ( $_POST['pwr_new_password_verify'] );
$result_validate = mysql_query ( "SELECT * FROM pwrecover WHERE user_id='$pwr_user_id' AND validation_code='$pwr_validation_code'" );
if ( mysql_num_rows ( $result_validate ) == 0 ) {
$errors[] = "The user id and validation code that you have entered is not valid or has expired. Please check your email and make sure that your user id and validation code is correct.";
}
if ( empty ( $pwr_new_password ) ) {
$errors[] = "Please enter a new password";
} 
if ( $pwr_new_password != $pwr_new_password_verify ) {
$errors[] = "The new password and the confirmed passwords don't match";
}
if ( strlen ( $pwr_new_password ) < 3 || strlen ( $pwr_new_password ) > 20 ) {
$errors[] = "Password must be between 3 to 20 characters long";
}
if ( count ( $errors ) > 1 ) {
if ( count ( $errors ) == 2 ) {
echo "<b>The following error was found:</b>";
} else {
echo "<b>The following errors were found:</b>";
}
echo "<ul type='square'>";
foreach ( $errors as $var ) {
echo "<li>$var</li>";
}
echo "</ul>";
} else {
$delete_validation = mysql_query ( "DELETE FROM pwrecover WHERE user_id='$pwr_user_id' AND validation_code='$pwr_validation_code'" );
$result_userinfo = mysql_query ( "SELECT user_id, username, email_address FROM users WHERE user_id='$pwr_user_id'" );
$pwr_info = mysql_fetch_array ( $result_userinfo ); 
$pwr_old_password = $pwr_new_password;
$email_msg = "$pwr_info[username],\nThis email has been sent to inform you of your new login details which are specified below:\n\nUsername: $pwr_info[username]\nPassword: $pwr_old_password\n\n";
$email_msg .= "For safety purposes, it is recommended that you do not give your password to anyone else.\n\nRegards,\n\n$sitetitle\n$site_url";
mail ( "$pwr_info[email_address]", "New Login Details At $sitetitle", "$email_msg", "From: $sitetitle <$contact_email>" );
$pwr_new_password = md5 ( $pwr_new_password );
$result_changepw = mysql_query ( "UPDATE users SET password='$pwr_new_password' WHERE user_id='$pwr_user_id'" );
echo "<script>alert('Your password has been successfully changed! An email has been sent with your new login information in case that you forget them. You will now be redirected to the login page where you can login and enjoy browsing $sitetitle')</script>";
echo "<script>document.location='$site_path/login'</script>";
}

}



if ( $do == "validate" ) {
?><fieldset>
<legend class="main">User ID</legend>
<table cellpadding="0" cellspacing="0" class="main"><tr>
<td style="text-align: justify">
Input the user id number that was specified in the email that you recieved in the text field below, if not already automatically inputted.
This is used to recognize your account so that the password can be changed for the correct account.
</td></tr>
<tr><td style="height: 10px"></td></tr>
<tr><td>
<b>Input your user id:</b><br><input name="pwr_user_id" type="text" readonly="true" class="form" style="width: 250px" value="<?php echo "$_GET[uid]" ?>">
</td></tr></table>
</fieldset>



<table style="height: 10px"><tr><td></td></tr></table>

<fieldset>
<legend class="main">Validation Code</legend>
<table cellpadding="0" cellspacing="0" class="main"><tr>
<td style="text-align: justify">
Input the validation code that was specified in the email that you recieved in the text field below, if not already automatically inputted.
This is used to ensure that you have recieved the email and so that random people cannot simply change your password.
<tr><td style="height: 10px"></td></tr>
<tr><td>
<b>Input the validation code</b><br><input type="text" name="pwr_validation_code" value="<?php echo "$_GET[aid]" ?>" style="width: 250px" class="form">
</td></tr></table>
</fieldset>

<table style="height: 10px"><tr><td></td></tr></table>

<fieldset>
<legend class="main">New Password</legend>
<table cellpadding="0" cellspacing="0" class="main"><tr>
<td style="text-align: justify">
Input the new password that you wish to login with in the first field.
Next, confirm the password in order to elimate chances of mistyped passwords. 
</td>
</tr></table>
<table style="height: 10px"><tr><td></td></tr></table>
<table cellpadding="0" cellspacing="0" class="main"><tr>
<td><b>New Password:</b><br><input type="password" name="pwr_new_password" maxlength="20" style="width: 225px" class="form"></td>
<td width="5"></td>
<td><b>Confirm Password:</b><br><input type="password" name="pwr_new_password_verify" maxlength="20" style="width: 225px" class="form"></td>
</tr></table>
</fieldset>

<center><p><input type="submit" name="pwr_validate" value="Validate Password" class="form"></center>

<?php
} else {
?>

<fieldset>
<legend class="main">Email Address</legend>
<table cellpadding="0" cellspacing="0" class="main"><tr>
<td style="text-align: justify">
It is always unfortunate when you forget your password to a certain site. 
That is why we have provided the option of recovering your password by simply inputting your email address in the text field below.
After inputting your email address and submitting the form, an email with information on how to recover your password will be sent to the email address that you specify.
However, if you have forgotten the email address that you registered with, please try the next option located below.
</td></tr>
<tr><td style="height: 10px"></td></tr>
<tr><td>
<b>Input your email address:</b><br><input type="text" name="pwr_email_address" style="width: 250px" class="form">
</td></tr></table>
</fieldset>

<table style="height: 10px"><tr><td></td></tr></table>

<fieldset>
<legend class="main">Username</legend>
<table cellpadding="0" cellspacing="0" class="main"><tr>
<td style="text-align: justify">
If you forgot the email address that you registered with, this option allows you to recover your password by simply inputting your username in the text field below. 
After inputting your username and submitting the form, an email with information on how to recover your password will be sent to the email address that was specified under this username.</td></tr>
<tr><td style="height: 10px"></td></tr>
<tr><td>
<b>Input your username:</b><br><input type="text" name="pwr_username" style="width: 250px" class="form">
</td></tr></table>
</fieldset>

<center><p><input type="submit" name="pwr_submit" value="Recover Password" class="form"></center>
<?php
}

echo "</form>";
?>