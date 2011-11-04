<?php
error_reporting(E_ALL);

define("CURR_LOCATION",'dfw');
define("NMAP",'/usr/bin/nmap');

$dcs = array("sat" => "sat.rackspace.com",
			 "dfw" => "dfw1.rackspace.com",
			 "dfw2" => "dfw2.corp.rackspace.com",
			 "iad" => "iad1.rackspace.com",
			 "lon" => "lon.rackspace.com",
			 "hkg" => "hkg1.rackspace.com");
			 
$rl = array(1=>"Left", 2=> "Right");

$services_dn = array(
				"http" => "HTTP/S",
				"smtp" => "SMTP",
				"pop3" => "POP3/IMAP",
				"mysql" => "MySQL",
				"pgsql" => "PostgresSQL",
				"mssql" => "Microsoft SQL",
				"ping" => "PING",
				"traceroute" => "Traceroute");
				
$locations = array(
			 "sat" => "sat.rackspace.com",
			 "dfw" => "dfw1.rackspace.com",
			 "dfw2" => "dfw2.corp.rackspace.com",
			 "iad" => "iad1.rackspace.com",
			 "lon" => "lon.rackspace.com",
			 "hkg" => "hkg1.rackspace.com");

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

function chkPerms() {

	if($_POST['hidden'] != "4b40ef307bc0fa07f19450653a8253ae"):
		die("This page must be called from a rackwatch console.");
	elseif(!preg_match("/(rwdev1|onms|onms-1|onms-2|onms-3|onms-4).[sat|dfw1|dfw2|iad1|lon|hkg1]+/i",$_SERVER['HTTP_HOST'])):
		die("This page must be called from a rackwatch console.");
	endif;
}

function setJSVars() {
	

	global $dcs;
	global $services_dn;
	
	$remote_num = (count($dcs) * 2) - 2;
	
	$y = 0;
	
	foreach($dcs as $loc => $url):
		print "\n";
		echo 'myDCs["'.$loc.'"] = "'.$url.'";';
		
		for ($x = 0; $x < 2; $x++):
			print "\n";
			echo 'alldclocations['.$y.'] = "'.$loc.'";';
			$y++;
			
		endfor;
		
	endforeach;

	for($x = 0; $x < $remote_num; $x++):
			print "\n";		
		echo 'remote_dcs['.$x.'] = "remote'. ($x + 1) .'";';
					print "\n";
		echo 'pollerheads['.$x.'] = "pollerhead'. ($x + 1) .'";';
		
	endfor;
		
	$y = 0;
	
	foreach ($services_dn as $service):
				print "\n";
		echo 'services['.$y.'] = "'.$service.'";';
		$y++;
	
	endforeach;
				print "\n";
	echo 'var local = "' . CURR_LOCATION . '";';
}

?>
