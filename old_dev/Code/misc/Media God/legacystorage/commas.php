<?
include "config.php";
$q = mysql_query("SELECT * FROM `whatpulse` WHERE `user` = 'RadarListener'");
while ($a = mysql_fetch_array($q)) {
echo number_format($a['tkc']);	
}