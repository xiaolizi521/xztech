<?
include "config.php";
include "designtop.php";
?>
Ok, for this lost password page we're going to need two pieces of information. Your username and your email.
<form action='' method='post'>
Username: <input type='text' name='username'>
Email <input type='text' name='email'>
<input type='submit' name='submit' value='submit'>
</form>
<?

if ($_POST['submit']) {
		$username = htmlentities(mysql_real_escape_string($_POST['username']));
		$email = htmlentities(mysql_real_escape_string($_POST['email']));
		if ($email && $username) {
			$a = mysql_query("SELECT * FROM `whatpulse` WHERE `user` = '$username'");
		$q = mysql_fetch_assoc($a);
		$r = mysql_num_rows($a);
		if ($r) {
		if ($q['email']) {
			echo "Good! You're one of the 40% of people who luckily enough put their email in.<br>
			Dispatching an e-mail to the provided address... ";
			$time = substr(md5(mktime()),0,6);
			

$subject = 'Your new password for WSI';
$message = 'Your new password is ' . $time . '. We cannot retrieve passwords. All passwords are secure.\n\n The WSI Team';
$headers = 'From: no-reply@frozenplague.net' . "\r\n" .
   'Reply-To: no-reply@example.com' . "\r\n" .
   'X-Mailer: Leet Hax/' . phpversion();

mail($q['email'], $subject, $message, $headers);
$time = md5($time);
mysql_query("UPDATE `whatpulse` SET `password` = '$time' WHERE `user` = '$username'") or die(mysql_error());
echo "Done!";
		}
		else {
			echo "You're in trouble now. You forgot to put your email in the custom.php page. That 10 second effort could have saved you a lot of time. Oh well. <a href='http://frozenplague.net/forums/viewforum.php?f=4'>Please go here and post a new topic.</a>";
		}
		}
		else {
			echo "This username does not exist.";
		}
}
else {
echo "You didn't enter an email and/or username.";	
}
}
include "menu.php";
include "designbottom.php";
?>