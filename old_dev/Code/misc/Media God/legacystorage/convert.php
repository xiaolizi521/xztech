<?
include "config.php";
$q = mysql_query("SELECT * FROM `whatpulse`");
while ($a = mysql_fetch_array($q)) {
if (substr($a[path],-4,4) != ".png") {
mysql_query("UPDATE `whatpulse` SET `path` = 'img/banana.png' WHERE `id` = '$a[id]'");
}
}
?>