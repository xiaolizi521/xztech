<?
class news {
function get() {
$q = mysql_query("SELECT * FROM `news` ORDER BY `id` DESC");
if (!mysql_num_rows($q)) {
	echo "There is currently no news in the database.";
}
else {
while ($a = mysql_fetch_array($q)) {
	$x++;
	$a['date'] = date("g:i a, F jS Y", $a['date']);
	$a['article'] = stripslashes(nl2br($a['article']));
	echo "<h3>$a[subject]</h3>$a[article]<br><br><i>$a[user] - $a[date]</i><br><br><br>";
}
}
}
function post($subject,$article) {
	$level = user::level($_SESSION['username']);
	
	if ($level['userlvl'] == 1) {
		$time = mktime();
		mysql_query("INSERT INTO `news` (`subject`,`article`,`date`,`user`) VALUES ('$_POST[subject]','$_POST[article]','$time','$_SESSION[username]')");
		if (!mysql_error()) {
		echo "<div class='codebox'><b>News successfully entered.</b></div>";
		}
		else {
			echo "<div class='codebox'><img src='img2/alert.png'>There was a problem inserting the news post:<br> " . mysql_error() . "</div>";
		}
	}
	else {
		echo "<div class='codebox'><img src='img2/alert.png'>You are not allowed to do that.</div>";
}
}
}
?>