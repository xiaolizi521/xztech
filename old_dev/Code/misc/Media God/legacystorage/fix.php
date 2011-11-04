<?php
include "config.php";
$q = mysql_query("SELECT * FROM `whatpulse`") or die(mysql_error());
while ($a = mysql_fetch_array($q)) {
mysql_query("UPDATE `whatpulse` SET `path` = 'img/banana.png' WHERE `id` = '$a[id]'") or die(mysql_error());
}
?>