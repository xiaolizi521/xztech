<?
session_start();
include "config.php";
include "designtop.php";

require_once "functions/user.php";
include "functions/tidy.php";
if ($_POST['submit']) {
	$login = user::check($_SESSION['username']);
	$_POST = tidy::post($_POST);
	if (!$login) {
	$login = user::login($_POST['username'],$_POST['password']);
	}
}

if (!$login) {
?>
<form action='' method='post'>
<b>Usernames are CaSe SeNsItIvE</b><br>
Username: <input type='text' name='username'><br>
Password: <input type='password' name='password'><br>
<input type='submit' name='submit' value='submit'>
</form>
<?
}


include "menu.php";
include "designbottom.php";
?>