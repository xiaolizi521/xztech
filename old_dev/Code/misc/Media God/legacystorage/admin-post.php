<?
session_start();
include "config.php";
include "functions/user.php";
include "functions/tidy.php";
include "functions/news.php";
include "designtop.php";
$level = user::level($_SESSION['username']);
if ($level['userlvl'] != 3) {
	header("Location:login.php");
}
else {
if ($_POST['submit']) {
	$_POST = tidy::post($_POST);
news::post($_POST['subject'],$_POST['article']);
}
}


?>
<form action='' method='post'>
Subject: <input type='text' name='subject' size='40'><br>
Article: <textarea cols='75' rows='20' name='article'></textarea><br>
<input type='submit' name='submit' value='submit'>
</form>
<?
include "menu.php";
include "designbottom.php";
?>