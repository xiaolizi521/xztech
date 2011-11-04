<?php
$db_host = 'localhost';
$db_name = conquerh_whatpulse;
$db_username = 'conquerh_whatpul';
$db_password = 'signatures';
$connect = mysql_connect($db_host, $db_username, $db_password);
$databaseSelect = mysql_select_db($db_name);
?>