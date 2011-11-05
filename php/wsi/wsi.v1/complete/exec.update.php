<?php

include "config/required.php";
include "classes/xml/class.xml.file.php";

error_reporting(E_ALL);
ini_set("memory_limit","1024M");
$dir = "";

// Begin timer for execution time debugging
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];

// Output start time and date 
echo "Started at " . date("F j, Y, g:i a",time()) . "<br />";

// Create the UserObject

$userobj = new xmlFileObject;
// Create the XML Parser Object
$rss = new XML_Parser($userobj);

// Open the XML file.
$fp = fopen('sigs.xml.gz','r') or die('Cant open xml file');

// We have to modify the XML file to replace some of the UTF-8 Encoded characters.

while ($data = utf8_encode(fread($fp,filesize('sigs.xml.gz')))) {
	
	// Replace any special characters that cannot be handled by expat
	$data = str_replace($xmlsearch, $xmlreplace, $data);
	
	// Parse the data
	$rss->parse($data);
}

$userarray = array();
$doc = new DOMDocument();
	$doc->formatOutput = true;
	$r = $doc->createElement("whatpulse");
	$doc->appendChild($r);
$teamvar = 0;
$uservar = 0;	
for ($x=0; $x<$userobj->indexSize(); $x++) {
	
	$userarray = $userobj->returnAssocSingle();
//	print_r ($userobj->returnAssocSingle());

	
	foreach ($userarray as $key => $value) {
	
		if ($key === 'whatpulse'){ 
			//do nothing 
		}
		else if ($key === 'user' || $key === 'team') {
			
			
			if (!$uservar && $key==='user') {
			
				${$key} = $doc->createElement("user");
				$uservar = 1;
			}
			else if (!$teamvar && $key === 'team'){
				
				${$key} = $doc->createElement("team");
				$teamvar = 1;
			}
			
			else if ($key === 'user' && $uservar) {
			
				$r->appendChild($user);
				unset($user);
				$user = $doc->createElement("user");
			}
	
		}
		else if ($key==='clicks' && $teamvar) {
			
			$user->appendChild($team);
			unset($team);
			$teamvar = 0;
		}
		else {
			
			if($teamvar) {
				${$key} = $doc->createElement($key);
				${$key}->appendChild($doc->createTextNode($value));
				$team->appendChild(${$key});
			}
			else {
				
				${$key} = $doc->createElement($key);
				${$key}->appendChild($doc->createTextNode($value));
				$user->appendChild(${$key});
			}
		}
	}	
//	sleep(2);
}
echo $doc->save("newPulseXML.xml");

// Close the file stream
fclose($fp);

echo "Done!<br />";

// Stop the timer
$etimer = explode( ' ', microtime() );
$etimer = $etimer[1] + $etimer[0];

// Output the time for processing to easier guage script performance.
$time = $etimer-$stimer;
printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) );

?>