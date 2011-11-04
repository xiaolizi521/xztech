
<?php

debug_print_backtrace();

include_once("rackwatch/includes.php");
chkPerms();

$host = $_POST['host'];
$service = $_POST['service'];

if($_POST['dc']) {

	$thedc = $_POST['dc'];
	$index = $_POST['poller'];	
	
	$dcs = array("sat" => "sat.rackspace.com",
				 "dfw" => "dfw1.rackspace.com",
				 "dfw2" => "dfw1.corp.rackspace.com",
				 "iad" => "iad1.rackspace.com",
				 "lon" => "lon.rackspace.com",
				 "hkg" => "hkg1.rackspace.com");
	
		$url = "http://".$pollers[$index].".".$dcs[$thedc]."/rackwatch/process.php";
		
		$dataArray = array("hidden" => "4b40ef307bc0fa07f19450653a8253ae", "host"=>$host, "service" => $service);

		system("/var/www/html/rackwatch/cmd.php " . $host . " " . $service . " " . $url);
		
}
?>
