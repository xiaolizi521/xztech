<HTML>
<HEAD>
</HEAD>
<BODY>
<pre>
<?php

define("NMAP","/usr/bin/nmap");
/*
** NMAP Tool for Rackwatch Testing
** This tool can only be called from a rackwatch ping page.
** Calling this page directly will result in failure.
**
*/

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

	
	if($service == 'ping'):
		echo "<h4>Pinging host: " . $host . "</h4><hr>";
		$cmd = "ping -n -c 5";
	
	elseif($service == 'traceroute'):
		echo "<h4>Traceroute to host: " . $host . "</h4><hr>";
		$cmd = "traceroute -I -n";
			
	else:	
		echo "<h4>NMAP for service " . $service . " to host: " . $host . "</h4><hr>";
		$cmd = NMAP. " -p ";
				
		if(!empty($services[$service])):
		
			if(is_array($services[$service])):
			
				for($x=0; $x < count($services[$service]); $x++):
				
					if($x+1 == count($services[$service])):
						
						$cmd .= $services[$service][$x];
						
					else:
						
						$cmd .= $services[$service][$x] . ",";
						
					endif;
				
				endfor;
			
			else:
			
				$cmd .= $services[$service];
				
			endif;
		
		endif;
		
	endif;
	
	$cmd .= " " . $host;
	system($cmd);

?>
</pre>
</BODY>
</HTML>