<?
include "config.php";
$q = mysql_query("SELECT * FROM `whatpulse2`");
while ($a = mysql_fetch_array($q)) {
mysql_query("UPDATE `whatpulse2` SET `font` = 'tahoma.ttf', `path` = 'img/banana.png' WHERE `id` = '$a[id]'");
}