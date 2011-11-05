<?
include "config.php";
$q = mysql_query("SELECT * FROM `backgrounds`");
while ($a = mysql_fetch_assoc($q)) {
if (!filesize($a['path'])) {
echo $a['path'] . "<br>";
}
}
echo date(1150680903);
?>