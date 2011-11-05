<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

include ( "$_SERVER[DOCUMENT_ROOT]/db.php" );
include ( "$_SERVER[DOCUMENT_ROOT]/header.php" );

if ( !isset ( $user_info['user_id'] ) ) {
echo "<script>parent.mainwindow.location='$site_url/login.php'</script>";
echo "<script>window.close()</script>";
exit();
}

function DisplayInfo( $header, $content, $type ) {
global $user_info;
echo "<fieldset class='main'><legend class='main'><b>$header</b></legend>";
if ( $type == "textbox" ) {
echo "<input type='text' name='$content' value='$user_info[$content]' size='40' class='textbox'>";
} elseif ( $type == "textbox-r" ) {
echo "<input type='text' name='$content' size='40' class='textbox'>";
} elseif ( $type == "textarea" ) {
echo "<textarea name='$content' cols='39' rows='5' class='textbox'>$user_info[$content]</textarea>";
} elseif ( $type == "textfile" ) {
echo "<input type='file' name='$content' value='browse' size='25' class='textbox'>";
} elseif ( $type == "password" ) {
echo "<input type='password' name='$content' size='40' class='textbox'>";
}
echo "</fieldset>";
}


if ( isset ( $_POST[edit_profile] ) ) {
$update = mysql_query( "UPDATE users SET email_address='$email_address', website='$website', msn='$msn', aol='$aol' WHERE user_id='$user_info[user_id]'" );
echo "<script>alert( 'Profile Successfully Updated' )</script>";
echo "<script>document.location='$PHP_SELF'</script>";
}


if ( isset ( $_POST[upload_avatar] ) ) {
$filesize_limit = 20480; //20 KB
if ( $_FILES['imagefile']['type'] != "image/gif" ) { 
echo "<script>alert( 'Couldn\'t upload file because it is not a GIF filetype or there was a blank submission' )</script>";
} elseif ( $_FILES['imagefile']['size'] > $filesize_limit ) { 
echo "<script>alert( 'Couldn\'t upload file because it must be less than 20 KB ($filesize_limit bytes) in size' )</script>";
} else {
copy ( $_FILES['imagefile']['tmp_name'], "$_SERVER[DOCUMENT_ROOT]/members/avatars/$user_info[user_id].gif" ) or die ( '<b>Solution</b>: CHMOD the \'members/\' and \'members/avatars\' folders to 777<br><a href=\'profile.php\'>Back To Profile Editting</a>' );
$update = mysql_query( "UPDATE users SET 
avatar='$site_url/members/avatars/$user_info[user_id].gif' WHERE user_id='$user_info[user_id]'" );
$update2 = mysql_query( "UPDATE news_comments SET 
avatar='$site_url/members/avatars/$user_info[user_id].gif' WHERE username='$user_info[username]'" );
echo "<script>alert( 'Avatar has been successfully uploaded and updated' )</script>";
echo "<script>document.location='$PHP_SELF'</script>";
} 
}


if ( isset ( $_POST[custom_avatar] ) ) {
if ( empty ( $avatar_custom ) ) {
echo "<script>alert( 'You cannot leave the field blank' )</script>";
} else {
if ( $fp = @fopen ( $avatar_custom, 'r' ) ) {
if ( substr( $avatar_custom, -4 ) != ".gif" ) {
echo "<script>alert( 'The file must be a GIF filetype' )</script>";
} else {
$update = mysql_query( "UPDATE users SET avatar='$avatar_custom' WHERE user_id='$user_info[user_id]'" );
$update2 = mysql_query( "UPDATE news_comments SET 
avatar='$avatar_custom' WHERE username='$user_info[username]'" );
echo "<script>alert( 'Avatar has been successfully updated' )</script>";
}
fclose ( $fp );
} else {
echo "<script>alert( 'The image could not be found' )</script>";
}
}
echo "<script>document.location='$PHP_SELF'</script>";
}


if ( isset ( $_POST[edit_password] ) ) {

if ( empty ( $password_old ) ) { //password not empty check
echo "<script>alert( 'You must enter your old password' )</script>";
} else { //password is not empty

if ( md5 ( $password_old ) === "$user_info[password]" ) { //password matches old password check

if ( ( empty ( $password_new ) && empty ( $password_confirm ) ) || ( empty ( $password_new ) || empty ( $password_confirm ) ) ) {
echo "<script>alert( 'The New Password field and/or the Confirm Password field must be filled out' )</script>";
} elseif ( strlen ( $password_new ) > 15 || strlen ( $password_confirm ) > 15 ) {
echo "<script>alert( 'The New Password field must be less than 15 characters' )</script>";
} else {

if ( $password_new === $password_confirm ) { //new password and confirm password match check
$password_new = md5 ( $_POST[password_new] );
$update = mysql_query( "UPDATE users SET password='$password_new' WHERE user_id='$user_info[user_id]'" );
echo "<script>alert( 'Your Password has been successfully changed' )</script>";
} else {
echo "<script>alert( 'The Passwords you\'ve inputted do not match' )</script>";
} //end new password and confirm password match check

} 

} else { //password doesn't match old password
echo "<script>alert( 'The Password you inputted does not match the one in our database' )</script>";
} //end password matches old password check

} //end password empty check
echo "<script>document.location='$PHP_SELF?action=$action'</script>";

}


if ( isset ( $backtoeditprofile ) ) {
echo "<script>document.location='$PHP_SELF'</script>";
}

?>
<html>
<head>
<title>Edit Profile</title>
</head>

<body bgcolor="<?php echo $bgcolor ?>" background="<?php echo $bgbackground ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" height="100%" cellpadding="7" cellspacing="0" class="main"><tr><td align="center" valign="middle">

<form method="post" enctype="multipart/form-data">

<?php
if ( $action == "edit_password" ) {
?>

<table cellpadding="0" cellspacing="0" align="center">
<tr><td style="background-color: <?php echo $tableheadercolor ?>; padding: 3px" class="secondary"><b>Change Your Password</b></td></tr>
<tr><td>
<table bgcolor="<?php echo $tablebgcolor ?>" width="100%" cellpadding="5" cellspacing="0">
<tr><td>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td><?php DisplayInfo( $header="Old Password", $content="password_old", $type="password" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="New Password", $content="password_new", $type="password" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="Confirm New Password", $content="password_confirm", $type="password" ) ?></td></tr>
</table>
</td></tr>
<tr><td align="center"><input type="submit" name="edit_password" value="Change Your Password" class="submit_button"></td></tr>
<tr><td align="center"><input type="submit" name="backtoeditprofile" value="Go Back To Edit Profile" class="submit_button"></td></tr>
</table>
</td></tr></table>

<?php
} else {
?>

<table cellpadding="0" cellspacing="0"><tr>
<td valign="top">

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td style="background-color: <?php echo $tableheadercolor ?>; padding: 3px" class="secondary"><b>Avatar Information</b>
</td></tr>
<tr><td>
<table bgcolor="<?php echo $tablebgcolor ?>" width="100%" cellpadding="5" cellspacing="0">
<tr><td>
<table width="100%" cellpadding="0" cellspacing="0" class="main"><tr><td align="center">
<?php
if ( empty ( $user_info['avatar'] ) ) {
echo "<div style='width: 50px; height: 50px; border: 1px solid #4A4D4F'></div>";
} else {
echo "<img src='$user_info[avatar]' width='90' height='90'>";
}
?>
</td></tr></table>
</td></tr>
<tr><td>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td><?php DisplayInfo( $header="Current Avatar", $content="avatar", $type="textbox" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="Custom Avatar", $content="avatar_custom", $type="textbox-r" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="Upload Avatar", $content="imagefile", $type="textfile" ) ?></td></tr>
</table>
</td></tr>
<tr><td align="center"><input type="submit" name="custom_avatar" value="Custom" class="submit_button"> <input type="submit" name="upload_avatar" value="Upload" class="submit_button"></td></tr>
</table>
</td></tr></table>

<table height="7"><tr><td></td></tr></table>

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td style="background-color: <?php echo $tableheadercolor ?>; padding: 3px" class="secondary"><b>Contact Information</b>
</td></tr>
<tr><td>
<table bgcolor="<?php echo $tablebgcolor ?>" width="100%" cellpadding="5" cellspacing="0">
<tr><td>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td><fieldset class="main"><legend class="main"><b>Password</b></b></legend><a href="<?php echo "$PHP_SELF?action=edit_password" ?>">Click here change your password</fieldset></td></tr>
<tr><td><?php DisplayInfo( $header="Email Adress", $content="email_address", $type="textbox" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="Website", $content="website", $type="textbox" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="MSN Messenger", $content="msn", $type="textbox" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="AOL Screen Name", $content="aol", $type="textbox" ) ?></td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>

<table height="20"><tr><td></td></tr></table>

<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center"><input type="submit" name="edit_profile" value="Save Changes To Your Profile" class="submit_button"></td></tr></table>

<?php
}
?>

</td></tr></form></table>