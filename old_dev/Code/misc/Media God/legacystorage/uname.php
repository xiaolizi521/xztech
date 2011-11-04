<?
if ($_POST['submit']) {
$uname = $_POST['name'];
include "config.php";
$q = mysql_query("SELECT `user` , `tkc` , `tmc` FROM `whatpulse` WHERE `user` = '$uname'") or die(mysql_error());
$r = mysql_num_rows($q);
$s = mysql_fetch_row($q);
if ($r == 1) {
echo "<br>Your username can be found in the database.<img src='img/tick.png'>";
$x = 1;
}
if (!$r) {
echo "<br>Your username can NOT be found in the database.<img src='img/cross.png'>";
}
if ($x == 1) {
echo "<br>Since your username can be found, now we check to see if there is any stats for it.<br>";
$q = mysql_query("SELECT `tkc`, `tmc` FROM `whatpulse` WHERE `user` = '$uname'") or die(mysql_error());
$r = mysql_fetch_row($q);
if ($s[1]) {
echo "We see that your key count has a value that is greater than zero. Assuming you have stats entered into the table.<img src='img/tick.png'><br>";
}
else {
echo "We see that your key count is equal to zero. This may be due to you not having key counting enabled. <br>We will check the table to see if you have any mouse clicks.";
if ($s[2]) {
echo "We see that you do infact have clicks. Therefore the rest of your stats should be there.<img src='img/tick.png'><br>";
}
else {
die("We see that you do not have any mouse clicks. Therefore your stats are NOT generated. Please check your XML to see if it valid or talk to an admin of this service.<img src='img/cross.png'>");
}
}
echo "Now checking to see if your image has been generated.";
		$filename = "http://offbeat-zero.net/pulse/sig/".$uname.".png";
		$exists = @fopen($filename , "r");
if ($exists) {
echo "<br>Your file exists.<img src='img/tick.png'><br>
It can be found at: <a href='http://www.offbeat-zero.net/pulse/sig/$uname.png'>this location</a>.<br>
It looks like <img src='http://www.offbeat-zero.net/pulse/sig/$uname.png'>";
}
else {
echo "Your image does not exist yet. You might have to wait 6 hours for it to be generated.<img src='img/cross.png'>";
}
}
}
?>
<br>Please put your name in the box provided and click the button.
<form action='uname.php' method='post'>
Username:<input type='text' name='name'>
<input type='submit' value='submit' name='submit'>
</form>