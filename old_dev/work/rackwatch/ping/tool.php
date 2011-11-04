<?php

if($_POST['hidden'] != "4b40ef307bc0fa07f19450653a8253ae"):
	die("This page must be called from a rackwatch console.");
elseif(!preg_match("/(rwdev1|onms-1|onms-2).[sat|dfw1|iad1|lon]+/i",$_SERVER['HTTP_HOST'])):
	die("This page must be called from a rackwatch console.");
endif;

$services = array(
			'http' => array(80,443,8080,8443), #all alternates + ssl
			'smtp' => array(25,465), 		   #smtp and smtps
			'pop3' => array(110,995,143,993),		   #pop3 and pop3s
			'imap' => array(143,993),          #imap and imaps
			'ssh' => 22,
			'ftp' => 21,
			'telnet' => 23,
			'pgsql' => 5432,
			'mysql' => 3306,
			'mssql' => 1433,
			'dns' => 53);
			
$pollers = array("onms-1","onms-2");

$host = $_POST['host'];
$service = $_POST['service'];

if($_POST['dc']) {


	$thedc = $_POST['dc'];
	$index = $_POST['poller'];	
	
	$dcs = array("sat" => "sat.rackspace.com",
				 "dfw" => "dfw1.rackspace.com",
				 "iad" => "iad1.rackspace.com",
				 "lon" => "lon.rackspace.com");
	
		$url = "http://".$pollers[$index].".".$dcs[$thedc]."/processcmd.php";
		
		$dataArray = array("hidden" => "4b40ef307bc0fa07f19450653a8253ae", "host"=>$host, "service" => $service, "dc" => $thedc, "poller" => $index);
		
		while ($incr < strlen($host)) {
                $match = preg_match('/[.0-9a-zA-Z-]/',$host[$incr]);
                if (!($match)) {
                        $invalid_host = $host;
                        break;
                }
                $incr++;
        }
		
		if ($invalid_host) {

			print "You entered an invalid target: ($invalid_host)  Please use only IP Addresses or host names.\n";

		} 
		
		else {

			print(sendPost($url, $dataArray));
		}

}

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