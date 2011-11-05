
<?php

debug_print_backtrace();

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
	
		$url = "http://".$pollers[$index].".".$dcs[$thedc]."/rackwatch/process.php";
		
		$dataArray = array("hidden" => "4b40ef307bc0fa07f19450653a8253ae", "host"=>$host, "service" => $service);

		system("/var/www/html/rackwatch/cmd.php " . $host . " " . $service . " " . $url);
		
}
?>