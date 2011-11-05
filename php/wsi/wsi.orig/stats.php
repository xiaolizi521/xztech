<?
session_start();
include "config.php";
include "designtop.php";
$page = "Statisics";
?>
<h3>WSI Statistics</h3>
<?
$q = mysql_query("SELECT * FROM `logging` ORDER BY `count` DESC");
$s = mysql_fetch_assoc(mysql_query("SELECT * FROM `logging` WHERE `page` = 'Signature'"));

echo "<b>Page</b> - Times 'served'<br><br>";
while ($a = mysql_fetch_assoc($q)) {
echo "<b>$a[page]</b> - $a[count]<br>";
$r += $a['count'];
}
$sig = round($s['count'] / $r * 100,2);
echo "<br>In all we have served $r page requests for this site. Signature viewings make up <b>$sig%</b> of total page views.<br><br>";
$q = mysql_query("SHOW TABLE STATUS") or die(mysql_error());
while ($a = mysql_fetch_array($q)) {
if ($a['Name'] == "logging") {
foreach ($a as $key => $value) {
$table[$key] = $value;	
}
}
}
$time = strtotime($table['Create_time']);
$now = mktime();
$diff = $now - $time;
$per = round($s[count] / $diff,2);
echo "This works out to be $per signature views every second!<br>";
echo "Table created on: " . $table['Create_time'];

include "menu.php";
echo "</div>";
include "designbottom.php";

?>