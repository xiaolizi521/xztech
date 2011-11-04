<?
include "config.php";
include "designtop.php";
include "updatesingle.php";
include "functions/tidy.php";

if ($_POST['submit']) {
	$_POST = tidy::post($_POST);
	if (!$_POST['username'] || !$_POST['password']) {
		die("Both the username and password fields need to have something in them. The password field needs to be six characters or longer.");
	}
	elseif ($_POST['password'] == $_POST['password2']) {
	
		mysql_connect($db_host,$db_user,$db_pass) or die('connection failed to database');
		mysql_select_db($db_name) or die('connection failed to database');
		$_POST['username'] = mysql_real_escape_string(htmlentities($_POST['username']));
		$query = "SELECT * FROM `whatpulse` WHERE `user` = '$_POST[username]'";
		$result = mysql_query($query);
		
	if (mysql_num_rows($result) > 0) {
	  	
	  	die("This username has already been registered. If you have forgotten your password, please visit <a href='lostpassword.php'>Lost-Password Contact Page</a> to retreive your password.");
	}
	
	$password = md5($_POST['password']);
	$email = $_POST['email'];
	$currusername = $_POST['username'];
	mysql_query("INSERT INTO `whatpulse` (`user`,`password`,`email`) VALUES ('$currusername', '$password', '$email')") or die(mysql_error());
	
	if(updatesingle())
	{
		echo "You have successfully registered!";
		header('Location: http://pulse.offbeat-zero.net');
	}

}
else {
	echo "The passwords you entered did not match.";
}
}

?>
<h2>Register</h2>
<div align='center'>
<form action='' method='post'>

Username: <input type='text' name='username'><br>
Password:<input type='password' name='password'><br>
Confirm Password: <input type='password' name='password2'><br>
Email: <input type='text' name='email'><br>
<input type='submit' name='submit' value='submit'>
</form>
</div>
<?
include "menu.php";
include "designbottom.php";
?>
</body>
</html>

