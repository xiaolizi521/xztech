<?
class user {
	function register() {
	}
	/**
	* @return Username
	* @desc Performs the necessary stuff to let a user log in.
	*/
	function login($username,$password) {
		$password = md5($password);
		if (mysql_num_rows(mysql_query("SELECT * FROM `whatpulse` WHERE `user` = '$username' AND `password` = '$password'"))) {
			$_SESSION['username'] = $username;
			echo "You are now logged in.";
			$showlogin = 1;
			return $showlogin;
		}
		else {
		echo "<div class='codebox'><img src='img2/alert.png'>That username and password combination is invalid.</div>";
		}

	}
	function check($username) {
		if ($_SESSION['username']) {
	$showlogin = 1;
	echo "You are already logged in.";
		}
	return $showlogin;
	}
	function level($username) {
	$user = mysql_fetch_assoc(mysql_query("SELECT `userlvl` FROM `whatpulse` WHERE `user` = '$_SESSION[username]'"));
	return $user;
	}
}
?>