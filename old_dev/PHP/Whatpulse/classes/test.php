<?php

include "class.wsidb.php";

try {

	$db = new wsiDB('localhost','offbeatz_wsi','pulsestats','offbeatz_pulsestats');
}

/* Catch Errors on Failure */
catch(ConnectException $exception) {

	echo "Connection Error\n";
	var_dump($exception->getMessage());
}

catch(Exception $exception) {

	echo "Other Script Error\n";
	var_dump($exception->getMessage());
}

try {
				
$result = $db->query("SELECT * FROM `whatpulse` where `user` = 'AgentGreasy'");
// If user doesnt exist, return 0. *** MODIFY ***

}

/* Catch any query errors or other errors. */
catch(QueryException $exception) {

echo "Query Error\n";
var_dump($exception->getMessage());
}

catch(Exception $exception) {

echo "Other Script Error\n";
var_dump($exception->getMessage());
}

if (!$result->num_rows) {

echo "NO";

}

else { 


echo "YES";			
}

$result->close();

?>