<?php
/* exec.update.php V 1.1
** Last Updated: January 31st, 2007 by Adam AKA OffbeatAdam AKA AgentGreasy
** Changes Made:
** Created "config.vars.php"
** Moved multiple str_replace to str_replace based on array
** Stored Array in config.vars.php
** Reduced to 1 str_replace
** Removed obsolete comments
** Added Documentation
**/

error_reporting(E_ALL);

$dir = "/home/offbeatz/public_html/pulse/system/";

require $dir.'classes/class.update.php';
require $dir.'include/config.xmlvars.php';

// Begin timer for execution time debugging
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];

// Output start time and date 
echo "Started at " . date("F j, Y, g:i a",time()) . "<br />";

// Create the XML Parser Object
$rss = new wpulseXML;

// Open the XML file.
$fp = fopen('/home/offbeatz/public_html/pulse/system/sigs.xml.gz',r) or 
die('Cant open xml file');
$x = 0;
// We have to modify the XML file to replace some of the UTF-8 Encoded characters.
while ($data = utf8_encode(fread($fp, 
filesize('/home/offbeatz/public_html/pulse/system/sigs.xml.gz')))) {
	
	// Replace any special characters that cannot be handled by expat
	$data = str_replace($xmlsearch, $xmlreplace, $data);
	
	// Parse the data
	$rss->parse($data);
	//++$x;
	//echo $x . "<br />";
}

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
