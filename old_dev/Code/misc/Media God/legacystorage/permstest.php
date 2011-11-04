#!/usr/bin/php 

<?php

$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];

$db_host = 'localhost';
$db_name = 'offbea2_pulsestats';
$db_username = 'offbea2_whatpuls';
$db_password = 'pulsestats';
$connect = mysql_connect($db_host, $db_username, $db_password);
$databaseSelect = mysql_select_db($db_name);

$query = 'select user from whatpulse';

$result = mysql_query($query,$connect);

//system("chmod 0666 /home/offbea2/public_html/pulse/sig/AgentGreasy.png");

while($data = mysql_fetch_assoc($result)) {

	$file = "/home/offbea2/public_html/pulse/sig/".$data['user'].".png";
	
	system("chmod 0666 ".$file);
	
}

$etimer = explode( ' ', microtime() );
$etimer = $etimer[1] + $etimer[0];
?>