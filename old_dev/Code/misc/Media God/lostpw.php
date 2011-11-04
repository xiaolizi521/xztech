<?
session_start();
include "config.php";
include "header.php";
include "logonav.php";
$page = "lostpw.php";
include "access.php";
if ($_SESSION['name']) {
echo "You are already logged in. If you wish to change your password please go to the <a href='profile.php'>profile page</a>";
}
else {
if ($_POST['submit']) {
if ($_POST['email']) {
htmlspecialchars($_POST['email']);
$q = mysql_query("SELECT `email` FROM `whatpulse` WHERE `email` = '$_POST[email]'");
$s = mysql_fetch_assoc($q);
define(EMAIL_FOUND,mysql_num_rows($q));
if (EMAIL_FOUND) {
$new = substr(md5(mktime()),0,6);
$newe = md5($new);
$to      = $_POST['email'];
$subject = 'New password for WSI.';
$message = 'Your new password for WSI is $new. As a reminder, your username is $s[user]. Please use this password from now on.';
$headers = 'From: passwordrecovery@offbeat-zero.net' . "\r\n" .
   'Reply-To: no-reply@lol.com' . "\r\n" .
   'X-Mailer: Hax/' . phpversion();

mail($to, $subject, $message, $headers);
mysql_query("UPDATE `whatpulse` SET `password` = '$newe' WHERE `email` = '$_POST[email]'");

}
else {
echo "That email was not found in the database.";
}
}
elseif ($_POST['name']) {
htmlspecialchars($_POST['name']);
}
}
echo "Please enter either your username or email address to get your email address sent to your email address.<br>

<form action='' method='post'>
Name: <input type='text' name='name' size='30'><br>
<input type='submit' value='submit' name='submit'><br><br>
OR<br><br>
Email: <input type='text' name='email' size='40'><br>
<input type='submit' value='submit' name='submit'></form>";
}
?>
