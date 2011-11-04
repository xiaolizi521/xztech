<HTML>
<HEAD>
</HEAD>
<BODY>
<pre>
<?php
include_once("includes.php");

/*
** NMAP Tool for Rackwatch Testing
** This tool can only be called from a rackwatch ping page.
** Calling this page directly will result in failure.
**
*/

chkPerms();

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
