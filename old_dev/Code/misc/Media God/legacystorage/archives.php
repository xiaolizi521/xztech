<?
include "designtop.php";
include "config.php";
$_GET['date'] = htmlentities($_GET['date']);
$date = explode(" ",$_GET['date']);
switch ($date[0]) {
	case "January":
	$timeframe[0] = mktime(0, 0, 0, 1, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 1, 31, $date[1]);
	break;
	case "February":
	$timeframe[0] = mktime(0, 0, 0, 2, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 4, -31, $date[1]);
	break;
	case "March":
	$timeframe[0] = mktime(0, 0, 0, 3, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 3, 31, $date[1]);
	break;	
	case "April":
	$timeframe[0] = mktime(0, 0, 0, 4, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 4, 30, $date[1]);
	break;	
	case "May":
	$timeframe[0] = mktime(0, 0, 0, 5, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 5, 31, $date[1]);
	break;
	case "June":
	$timeframe[0] = mktime(0, 0, 0, 6, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 6, 30, $date[1]);
	break;
	case "July":
	$timeframe[0] = mktime(0, 0, 0, 7, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 7, 31, $date[1]);
	break;
	case "August":
	$timeframe[0] = mktime(0, 0, 0, 8, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 8, 31, $date[1]);
	break;
	case "September":
	$timeframe[0] = mktime(0, 0, 0, 9, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 9, 30, $date[1]);
	break;
	case "October":
	$timeframe[0] = mktime(0, 0, 0, 10, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 10, 31, $date[1]);
	break;
	case "November":
	$timeframe[0] = mktime(0, 0, 0, 11, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 11, 30, $date[1]);
	break;
	case "December":
	$timeframe[0] = mktime(0, 0, 0, 12, 1, $date[1]);
	$timeframe[1] = mktime(23, 59, 59, 12, 31, $date[1]);
	break;
	}
	if ($_GET['date']) {
	
echo "<b><h2>Showing all posts from $date[0] $date[1]</h2></b><br>";
$q = mysql_query("SELECT * FROM `news` WHERE `timestamp` >= $timeframe[0] AND `timestamp` <= $timeframe[1] ORDER BY `timestamp` DESC");
while ($a = mysql_fetch_array($q)) {
	$a['article'] = nl2br($a['article']);
echo "<h1>$a[subject]</h1>$a[article]<br><br>";
echo date("g:i a, F jS Y", $a['timestamp']);
if ($_SESSION['username']) {
if ($user['level'] == 3) {
echo "<br><a href='admin-edit.php?id=$a[id]&type=news'>edit</a> | <a href='admin-delete.php?id=$a[id]&type=news'>delete</a>";
}
}
echo "<br><a href='index.php?comments=$a[id]'>Comments (" . mysql_num_rows(mysql_query("SELECT * FROM `comments` WHERE `articleid` = '$a[id]'")) . ")</a>";
}
	}
?>
<div class='sidebox'><b>Archives</b><br>
<?

$q = mysql_query("SELECT * FROM `news` ORDER BY `timestamp` DESC");
if (mysql_num_rows($q)) {
while ($a = mysql_fetch_array($q)) {
	$arch = date("F Y",$a['timestamp']);
	$archives[$arch]++;	
}
foreach($archives as $key => $value) {
echo "<a href='archives.php?date=$key'>" . $key . " (" . $value . ")</a><br>";	
}
}
else {
	echo "There are no posts in the archive.";
}
echo "</div>";
if (!$_GET['date']) {
echo "Please click on a date inside the box to the right.";	
}
?>
</div>
<?
include "designbottom.php";
?>