#!/usr/bin/php -q
<?php

/* Open NMS Monitoring Daemon
** 
** Author: Adam Hubscher
** Contact: adam.hubscher@rackspace.com, x501-5411
** 
** Purpose: Act as a daemon to open the script and make sure that it is open.
** This code should be minimal to prevent its death at future moments.
** This script should always be running. If this is running, it will maintain that the monitor is running.
**
**
*/

while(true) {
	
	
	// Check to see if script is running.
	// If it is not running, create a child process, and run it in the child process.
	// This should work.
		
	if(!checkPid()):
		
		// Fork a child process
		
		$pid = pcntl_fork();
		
		// If the PID returns as -1, the fork failed.
		if($pid == -1):
		
			die('Could not fork');
		
		// If the PID is > 0 ("TRUE"), the current process is a parent.
		
		elseif($pid):
				
				// If the process is still running, sleep for 30 seconds before checking again.
				if(checkPid()): 
					
					sleep(30);
					
				endif;				
					
		else:
		
			// As the child, we want to run the monitoring script.
			// On error, this will exit. On exit, script will see it is no longer running.
			// When it is no longer running, it will be started again.
			
			exec("/opt/opennms/onmsmon.php start");
			
		endif;
			
	endif;

}

// We will need to check to see if the process is running.

function checkPid() {
		
	// Open up the PID file created by the opennms monitoring script
	
	$fp = fopen("/var/lib/onmsmonitor.pid", "r");
	
	// If you can't open the file, then the application isn't running.
	if(!$fp):
		
		// Couldn't open the PID file. It must not be running.
		return false;
		
	// If the file was opened, we need to check if the file is there or not.	
	else:
		
		// The current PID is read from the file
		$currpid = fgets($fp, 4096);
		
		// Close the file pointer
		fclose($fp);
		
		// The command we are running to see if the process is active.
		$cmd = "ps $currpid";
		
		// Execute the command and store the result in `$output`
		exec($cmd, $output, $result);
		
		// Check the number of lines outputted. If the lines are greater than 2, process is running.
		if(count($output) >= 2):
			
			// Return true, process is active.
			return true;
		
		else:
		
			// Return false, the process is dead.
			return false;
		
		endif;
		
	endif;
	
	
}

?>