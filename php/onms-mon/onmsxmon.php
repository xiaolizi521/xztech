#!/usr/bin/php
<?php
error_reporting(E_ERROR);
define("LogDebug",1);
define("LogInfo",0);
define("LogError",2);

declare(ticks = 1);

$hostname = "onms-1.dfw1";



pcntl_signal(SIGTERM, "_handler");

switch($argv[1]) {

	case "start":
		
		// Write to log that we are starting up.
		
		
		/* Init Variables */
		$matchCount = array();
		$counter = 0;
		$nomatch = 0;
		
		//$alert = new onmsreboot('page.adam.hubscher@rackspace.com, coresysadmin@rackspace.com','coresysadmin@rackspace.com');
		
		if($argv[2] <= 2 && $argv[2] >= 0) {
	
			$alert = new onmsreboot('corerwalert@rackspace.com','corerwalert@rackspace.com',$argv[2], $hostname);
		}
		
		else if (isset($argv[2]) && !is_int($argv[2])) {
			
			usage();
			exit;
		}
		
		else {
			
			$alert = new onmsreboot('corerwalert@rackspace.com','corerwalert@rackspace.com',LogInfo, $hostname);
		}
		
		$alert->PrepWriteLog("Starting the PHP Based XMLRPCD Monitoring Daemon\n", LogError);
		
		$rebooted = 0;
		break;
	
	case "stop":
		
		$fp = fopen("/var/lib/onmsmonitor.pid","r");
		
		$pid = fgets($fp,4096);
		
		fclose($fp);
		
		if(posix_kill($pid,SIGTERM)) {
			
			exit;
		}
		break;
	
	case "help":
	
		usage();
		exit;
		break;
		
	default:
	
		usage();
		exit;
		break;
}



$pid = posix_getpid();
if ($pid) {
        $fp = fopen("/var/lib/onmsmonitor.pid","w+");
        fputs($fp,$pid);
        fclose($fp);
}

// setup signal handlers



/*

While (forever) Monitor Log File for Modification.
If File Grows, read from last position.
If new File, read from beginning of file and reset position.
Catch DBIDs into array. If no eventID, check timestamp.
If timestamp is 60 seconds or longer, restart xmlrpcd.
If timestamp is 59 seconds or shorter, no restart yet (CYA).

*/

while (true) {
	
	if ($rebooted[0] && time() - $rebooted[1] >= 600) { $rebooted = array(); }
	
	if(!$alert->coreDown()) {
		
		if($alert->CoreWasDown && $alert->CoreDBIDLongTime < 360 ) {
					$alert->PrepWriteLog("Core was down. Changing Alert Time.\n", LogDebug);
					$alert->CoreDBIDLongTime = $alert->CoreTimeDown + 360;
					$dbidcurrcount = count($alert->dbid);					
					$safeDBIDCount = floor($dbidcurrcount / 10);
					$alert->PrepWriteLog("Alert time changed to " . $alert->CoreDBIDLongTime . "\n", LogDebug);
		}
						
		$alert->checkFile();
		$alert->checkEventQueue();
		
		$alert->PrepWriteLog(count($alert->dbid) . " DBIDs were found. " . count($alert->eventid) . " EventIDs were found.\n",LogDebug);
		$alert->PrepWriteLog("Processing Matches...\n",LogDebug);
		
		foreach ($alert->dbid as $id => $timestamp) {
		
			// If an eventid matches a dbid...
			if(!empty($alert->eventid[$id])) {
			
				// Remove the eventid from the array.
				unset($alert->eventid[$id]);
				
				// Remove the dbid from the array.
				unset($alert->dbid[$id]);
				$alert->EventQueue--;
				
				$rebooted = array();
				
			}
			
			// If the timestamp for dbid is beyond 60 seconds old...
			else if (time() - $alert->dbid[$id] >= $alert->DBIDLongTime) { 
				
				
					if ($alert->CoreWasDown && $alert->eventQueueProcessing) {
					
						if(count($alert->dbid) > $safeDBIDcount) {
						
							$dontreboot = 1;
						}
						else if(time() - $alert->CoreTimeUp >= 1080 && $alert->dbid[$id] >= $alert->CoreDBIDLongTime) {
								$alert->CoreWasDown = 0;
								$dontreboot = 0;							
						}
						else if($alert->eventQueueProcessing) {
						
							$dontreboot = 1;
						}
						
						else { $dontreboot = 1; }
						
					
					}
					 
				
					
				// And Core is NOT down.
				if(!$alert->coreDown() && !$alert->CoreWasDown && !$dontreboot) {
					$alert->WriteDBIDTable();
					$alert->PrepWriteLog("Rebooting OpenNMS after no EventQueue response.\n",LogError);
					
					// Reboot OpenNMS
					if($alert->rebootOpenNMS()) {
						
						if(!$rebooted[0]) {
														
							$alert->page();
							$rebooted[0] = 1;
							$rebooted[1] = time();
						//	exit;
						}
						
						else if (time() - $rebooted[1] >= 300) {
						
							$alert->page();
							$rebooted[0] = 1;
							$rebooted[1] = time();
						//	exit;
						}
						
						else {
							
							$alert->PrepWriteLog("OpenNMS rebooted less than 5 minutes ago. Preventing excessive reboot.\n",LogError);	
						}
					
					}
					
				}
				
				else if ((!$alert->CoreWasDown && (time() - $alert->dbid[$id] >= $alert->FirstAlertDBIDTime) || (time() - $alert->dbid[$id] >= $alert->MaxAlertDBIDTime)) || ($alert->CoreWasDown && (time() - $alert->dbid[$id] >= $alert->CoreDBIDLongTime))) {
					
					//$alert->alert();
					$time = (time() - $alert->dbid[$id])/60;
					
					$alert->page("*** Oldest DBID is " . floor($time) . "Minutes Old!", "The oldest DBID has surpassed the set limits for DBID Age. Please investigate Rackwatch Issues IMMEDIATELY.");
				
				}
				
				
				// Otherwise, the DBID still has time to be processed.
				else {
					
					// Add alert to array to be sent in one email later.
					$alert->addAlert($id, time() - $timestamp);
				}
			}
		}
		$alert->PrepWriteLog("Processing Completed. There are currently " . count($alert->dbid) . " DBIDs left on the stack.\n", LogDebug);
		
		sleep(10);
	}
	
	else {
		$alert->PrepWriteLog("CORE appears to have stopped responding. Continuing to check until up or otherwise told not to.\n", LogError);
		
		while ($alert->coreDown()) {
			
			if($alert->EventQueueLevelReached) {
		
				$alert->QueueInterval = $alert->QueueInterval != 4000 ? $alert->QueueInterval += 1000 : $alert->QueueOverloaded = true;
				$alert->EventQueueLevelReached = 0;
				
			}
			
			if ($alert->EventQueue != 0 && $alert->EventQueue >= $alert->QueueInterval && !$alert->QueueOverloaded) { 
				
				$alert->EventQueueLevelReached = 1;
				
				$alert->page("CORE Not Responding", "Core has not responded to OpenNMS since " . date(DATE_ATOM,$alert->CoreDownStart) . "and the EventQueue is currently at " . $alert->EventQueue . " entries!");
			}
			
			else if ($alert->EventQueue != 0 && $alert->EventQueue >= $alert->QueueOverload && $alert->QueueOverloaded) {
			
				
				$alert->page("Rackwatch Queue Overloaded!", "The Rackwatch Queue has overloaded at 5000 entries. Immediate action is required.");
				
			}
					
			else {	
				
				$alert->EventQueueLevelReached = 0;
				
				$alert->checkFile();
			}
			
			$alert->PrepWriteLog(" CORE DOWN SINCE " . date(DATE_ATOM,$alert->CoreDownStart) . "\n", LogError);
			$alert->PrepWriteLog(" CURRENTLY THERE ARE $alert->EventQueue EVENTS ON THE QUEUE.\n", LogError);
				
			sleep(5);				
		}
	}
}

// Creating an object for better paging.
class onmsreboot {

	// Construct appropriate recipient variables.
	function __construct($page,$alert,$loglevel,$hostname) {
	
		$this->filesize = stat("/var/log/opennms/daemon/xmlrpcd.log");
		$this->currPos = $this->filesize[7];
		$this->dbid = array();
		$this->eventid = array();
		$this->EventQueue = 0;
		$this->pageRecipients = $page;
		$this->alertRecipients = $alert;
		$this->CoreDownStart = 0;
		$this->QueueInterval = 1000;
		$this->QueueOverload = 5000;
		$this->FirstAlertDBIDTime = 3000;
		$this->MaxAlertDBIDTime = 1800;
		$this->DBIDLongTime = 60;
		$this->MainLog = "/var/log/opennms/monitor.".date("Ymd").".log";
		$this->MainLogPrevDate = date("Ymd");
		$this->DbidLog = "/var/log/opennms/dbids.".date("Ymd-H.m.s").".log";
		$this->LogLevel = $loglevel;
		$this->hostname = $hostname;
	}
	
	function reinit() {
		
		$this->filesize = stat("/var/log/opennms/daemon/xmlrpcd.log");
		$this->currPos = $this->filesize[7];
		$this->dbid = array();
		$this->eventid = array();
		$this->EventQueue = 0;
		$this->QueueInterval = 1000;
		$this->QueueOverload = 5000;
		$this->FirstAlertDBIDTime = 3000;
		$this->MaxAlertDBIDTime = 1800;
		$this->DBIDLongTime = 60;
		$this->MainLog = "/var/log/opennms/monitor.".date("Ymd").".log";
		$this->MainLogPrevDate = date("Ymd");
		$this->DbidLog = "/var/log/opennms/dbids.".date("Ymd-H.m.s").".log";
		$this->PrepWriteLog("OpenNMS Was Restarted. Setting up Environment again.\n", LOG_INFO);
	}
	
	function __destruct() {
	
		$this->PrepWriteLog("STOP received, shutting down daemon...\n", LogError);
	 
	}
	
	function appendLog($msg) {
	
		$this->logEntry[time()] = $msg;
	}
	
	// Create an array of alerts for the email alerting system.
	function addAlert($var,$sec) {
		
		$this->alertArray[$var] = $sec;
	}
	
	function rebootOpenNMS($rebooted) {
	
			$logfile = '/var/log/opennms/daemon/xmlrpcd.log';
			$newlogfile = '/var/log/opennms/daemon/xmlrpcd.seizure.'.date(DATE_ATOM).'.log';
			$backlog = 'mv ' . $logfile . ' ' . $newlogfile;
			$touch = 'touch ' . $logfile;
			// Execute reboot command for opennms.
			
			preg_match('/ok/i', system('/etc/init.d/opennms stop')) or 
				$this->page("OpenNMS Failed to Stop!", "A problem was detected and action was attempted, however OpenNMS failed to respond properly. Investigate immediately.");
				
			system($backlog);
			system($touch);
			system('touch /var/log/opennms/daemon/xmlrpcd.log');
	
			preg_match('/ok/i', system('/etc/init.d/opennms start')) or
				$this->page("OpenNMS Failed to Start!", "A problem was detected and action was attempted, however OpenNMS failed to respond properly. Investigate immediately.");
			$this->reinit();	
			return 1;
	}
	
	
	function checkLog() {

		// Loop through the lines array to search for eventIDs and dbIDs.
		for ($x = 0; $x < count($this->lines); $x++) {
			
			// If eventid or dbid is matched...		
			if (preg_match("/(dbid)\s+([0-9]+)\s/i",$this->lines[$x],$matches) || preg_match("/eventid\s+([0-9]+)/i",$this->lines[$x],$matches)) { 
	
				// Set the DBID array if its a DBID
				if ($matches[1] == "dbid") {
				
					$this->dbid[$matches[2]]=time();
					$this->EventQueue++;	
				}
				
				// Otherwise it must be an eventid.
				else {
					
					if($this->CoreWasDown && time() - $this->CoreTimeUp < 60 ) { $this->eventQueueProcessing = 1;}
					$this->eventid[$matches[1]] = time();
				}
			}
	
		}
				
	}
	
	
	function checkFile() {
	
		$this->lines = array();
		
		// Clear stat cache for watching filesize.
		clearstatcache();
		
		// This is the parse counter (debug)
		// $counter++;
	
		// Set the new filesize/current filesize
		$currfs=stat("/var/log/opennms/daemon/xmlrpcd.log");
		
		// If the filesize is not equal to the current (new) filesize
			
		if($this->filesize[7] != $currfs[7]) {
			
			$this->PrepWriteLog("Processing XMLRPCD Log as it has changed.\n", LogDebug);
				
			// Open the file
			$xmlrpcd = fopen("/var/log/opennms/daemon/xmlrpcd.log","r");
			
			// If filesize is greater than new filesize, the log has been cycled, start from line 1.
			if ($this->filesize[7] > $currfs[7]) {
				
				$this->PrepWriteLog("Logfile has been cycled. Starting from the beginning.\n",LogDebug);
				
				// Reset the filesize
				$this->filesize = stat("/var/log/opennms/daemon/xmlrpcd.log");
	
				// Loop through the entire file from the beginning of the file.
				while(!feof($xmlrpcd)) {
					
					// Grab a line
					$buffer = fgets($xmlrpcd,4096);
					
					// Prep the Lines array
					$this->lines[] = $buffer;
					
					// Unset the buffer variable
					unset($buffer);
				}
				
			}
			
			// Otherwise, the file is to be parsed from the previous line.
			else {
				
				$this->PrepWriteLog("Logfile has not been cycled. Starting from line $this->currPos\n",LogDebug);
				
				// Reset the filesize
				$this->filesize = stat("/var/log/opennms/daemon/xmlrpcd.log");			
				
				$this->PrepWriteLog("Seekign to line $this->currPos\n",LogDebug);
				// Seek to the appropriate position in the file
				fseek($xmlrpcd,$this->currPos);
				
				$this->PrepWriteLog("Reading File...\n",LogDebug);
				
				// Loop through the rest of the file from the previous position.
				while (!feof ($xmlrpcd)) {
				
					// Buffer in 1 line.
					$buff = fgets($xmlrpcd, 4096);
					
					// Append to the lines array.
					$this->lines[] = $buff;
					
					// Unset the buffer variable to free memory.
					unset($buff);
					
				}
				
				$this->PrepWriteLog("Done Reading File.\n",LogDebug);
			}
			
			// Reset the current position in the file pointer for next parsing.
			$this->currPos = ftell($xmlrpcd);
			
			// Close the file pointer.
			fclose($xmlrpcd);
			
			// Unset the file pointer variable to save on memory.
			unset($xmlprcd);
			
			$this->checkLog();
			
	}
}	
	function coreDown() {
	
		
		if(!$socket = fsockopen('vcoreudev8.dev.core.rackspace.com', 8000, $errno, $errstr, 10)) { 
				
				if($this->CoreDownStart > time() || !$this->firstDown) { 
					
					$this->CoreDownStart = time();
					$this->firstDown = 1;
					$this->CoreWasDown = 1;
				}
				
				else if (time() - $this->CoreDownStart > 300) { $this->page("Rackwatch Alert: CORE is not responding!", "CORE has not responded to Rackwatch in 5 minutes.\n Investigation should be performed. If this is a scheduled outage, this page can be safely ignored.");}
				
				else if (time() - $this->CoreDownStart > 1800) { $this->page("Rackwatch Alert: CORE is not responding!", "CORE has not responded to Rackwatch in 30 minutes.\n Investigation should be performed immediately. If this is a scheduled outage, this page can be safely ignored.");}
				
			return 1; 
		
		}
	
		stream_set_blocking($socket, 1);
		
		fputs($socket, "GET / HTTP/1.1\r\n");
		fputs($socket, "Host: vcoreudev8.dev.core.rackspace.com\r\n\r\n");
		
		if(preg_match("/200\sOK/i", fgets($socket, 4096))) { 
		
			if($this->CoreWasDown) { 
				
				$this->CoreTimeUp = time();
				$this->CoreTimeDown = $this->CoreTimeUp - $this->CoreDownStart;
				$this->firstDown = 0;
			}
			
			fclose($socket); 
			
			return 0; 
			
		}
		
		else { 
		
			fclose($socket);  
			
			if($this->CoreDownStart > time() || !$this->firstDown) { 
					
					$this->CoreDownStart = time();
					$this->firstDown = 1;
					$this->CoreWasDown = 1;
				}
				
				else if (time() - $this->CoreDownStart > 300) { $this->page("Rackwatch Alert: CORE is not responding!", "CORE has not responded to Rackwatch in 5 minutes.\n Investigation should be performed. If this is a scheduled outage, this page can be safely ignored.");}
				
				else if (time() - $this->CoreDownStart > 1800) { $this->page("Rackwatch Alert: CORE is not responding!", "CORE has not responded to Rackwatch in 30 minutes.\n Investigation should be performed immediately. If this is a scheduled outage, this page can be safely ignored.");}
			
			return 1;
		}
		
	}
	
	function checkEventQueue() {
	
		if ($this->EventQueue != 0 && $this->QueueInterval > 1000 && $this->EventQueue < $this->QueueInterval) {
				
				$this->ResetQueueInterval();
			}
	
	}
	// Prepare and send the page when rebooting occurs.
	function page($subject = false, $body = false) {
		
		if (!$subject && !$body ) {
				
			$body = "NOTICE: " . $this->hostname . " HAS AN ISSUE\n\n";
			$body .= "We have found an error with the XML RPC Daemon.\n";
			$body .= "Primary Action will be taken.\n";
			$body .= "We have taken the primary action and restarted opennms.\n";
			$body .= "Please verify that OpenNMS is speaking to CORE";
			$body .= "properly and that there are no further issues.\n\n";
			$subject = "****ALERT****: OpenNMS FAILURE! SYSTEM REBOOTED!";
		}
		
		else {
			
			$body .= "\n\nMACHINE: " . $this->hostname ." \n\n";
		}
		// Send the email
		$this->email($body,$this->pageRecipients,$subject);
	}
	
	// Alert that there are unmatched entries.
	/*function alert() {
		
		$body = "We have parsed the xmlrpc file and found that the following DBIDs have not matched yet. \n";
		
		// Go through the alert array and add to the body message.
		foreach($this->alertArray as $dbid => $seconds) {
		
			$body .= "DBID: " . $dbid . "For Time: " . $seconds/60 . "minutes\n";
			
		}
		
		$body .= "Please be aware that there may be an issue and a reboot may occur soon.\n";
		$subject = "Alert: OpenNMS may be experiencing an issue communicating with CORE.\n";
		
		// Send the email.
		if(count($this->alertArray) > 10) {
					
			$this->email($body,$this->alertRecipients,$subject);
	}*/
	
	function email($body,$to,$sub) {
		
		// Send the mail based on variables from previous functions.
		mail($to, $sub, $body, 'From: systems@rackspace.com');
	}
	
	function ResetQueueInterval() {
		
		$NewInterval = $this->QueueInterval-(1000*(floor($this->QueueInterval/$this->EventQueue)-1));
		
		$this->QueueInterval = $NewInterval < 1000 ? $NewInterval : 1000;
	}
	
	function PrepWriteLog($msg, $level) {
		
		$this->CycleLog();
		
		$mesg = "[" . date(DATE_ATOM) . "]: ";
		$mesg .= $msg;
		
		$r = $level >= $this->LogLevel ? $this->WriteToLog($mesg) : 0;
		
		return $r;
	}
	
	function WriteToLog($mesg) {
		
		$fp = fopen($this->MainLog, "a");
		fwrite($fp,$mesg);
		fclose($fp);
		return 1;
	}
	
	function WriteDBIDTable() {
		
		$fp = fopen($this->DbidLog, "a");
		foreach($this->dbid as $dbid => $time) {
			$line = $dbid . "\n";
			fwrite($fp,$line);
		}
		fclose($fp);
		$this->dbid = array();
		$this->EventQueue = 0;
		$this->eventid = array();
		
	}
	
	function CycleLog() {
		
		$this->MainLog = $this->MainLogPrevDate != date("Ymd") ? "/var/log/opennms/monitor.".date("Ymd").".log" : $this->MainLog;
		
	}

}

function _handler($signo) {

	if($signo == SIGTERM) {				
		exit;
	}
}

function usage() {
	
		print "OpenNMS XMLRPCD Daemon v.01 Usage\n";
		print "=================================\n\n";
		print "Usage:\n\n";
		print "onmsxmon [options] [loglevel]\n\n";
		print "Options:\n\n";
		print "help - Display this help.\n";
		print "start - start the daemon.\n";
		print "stop - stop the daemon.\n\n";
		print "Log Levels: \n\n";
		print "0 - Info - Log Everything\n";
		print "1 - Debug - Log Debug Information\n";
		print "2 - Error - Log Critical Information Only\n";
		exit;
		
	}
?>
