<?php

include("config.php");

$query = 'select user from whatpulse';

$result = mysql_query($query, $connect);

while($data = mysql_fetch_assoc($result)){
	$file = "/home/offbea2/public_html/pulse/sig/".$data['user'].".png";
	$file = preg_replace("/\s/","\\ ",$file);
	echo "System: " . system("chmod 0666 ".addcslashes($file,"*,=,|,),(")) . "<br />";
	echo "Changed perms of " . $data['user'] . "<br />";
	
}

?>