#!/usr/bin/php

<?php

/*
** NMAP Tool for Rackwatch Testing
** This tool can only be called from a rackwatch ping page.
** Calling this page directly will result in failure.
**
*/

include_once("rackwatch/includes.php");

$host = $argv[1];
$service = $argv[2];
$url = $argv[3];
		
	$dataArray = array("hidden" => "4b40ef307bc0fa07f19450653a8253ae", "host"=>$host, "service" => $service);
		
	print(sendPost($url, $dataArray));


//end

function sendPost($url, $data){

	$ch = curl_init();    // initialize curl handle 
	curl_setopt($ch, CURLOPT_URL,$url); // set url to post to 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
	curl_setopt($ch, CURLOPT_PROXY,"");
	curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields 


	$result = curl_exec($ch); // run the whole process 
	
	curl_close($ch);
	
	return $result;
}

?>