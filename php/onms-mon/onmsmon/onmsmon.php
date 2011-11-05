#!/usr/bin/php
<?php

// Report all errors, we want to know whats going on.

error_reporting(E_ERROR);

declare(ticks = 1);

// Change these appropriately

define("HOSTNAME", "hostname");
define("PAGEADDS", "page.adam.hubscher@rackspace.com");
define("MAX_CORE_DOWNTIME", 1800);
define("FILENAME", "/var/log/opennms/daemon/xmlrpcd.log");

// For testing, set this to vcoreudev8.dev.core.rackspace.com
// Otherwise, set this to onms2core.core.rackspace.com

define("CORE_MON_HOSTNAME", 'onms2core.core.rackspace.com');

pcntl_signal(SIGTERM, "_HANDLER");

switch($argv[1]) {

	case "start":
	
		// Write to log that we are starting up. Initialize Object.
		
		$obj = new RWatch();
		break;
		
	case "stop":
		
		// Write to the log that we are stopping. Initalize shutdown sequence.
		
		$fp = fopen("/var/lib/onmsmonitor.pid", "r");
		$pid = fgets($fp, 4096);
		
		fclose($fp);
		
		if (posix_kill($pid, SIGTERM)) {
			
			exit;
		}
		
		break;
	
	default:
		
		usage();
		exit;
		break;
}

// Define the PID
$pid = posix_getpid();

// Write the PID file if a PID exists.
if($pid) {
	
	$fp = fopen("/var/lib/onmsmonitor.pid", "w+");
	fputs($fp, $pid);
	fclose($fp);
}

// Setup Signal Handler

function _handler($signo) {

	if($signo == SIGTERM) {				
		exit;
	}
}

while(true) {
	
	$obj->doFile();
	
	if($obj->coreUp()) {

		$obj->QueueProcess();

	}
	
	sleep(10);
}

class RWatch {
	
	protected $eventQueue = array();
	protected $dbidQueue = array();
	protected $coreDown = FALSE;
	protected $coreWasDown = FALSE;
	protected $coreDownStart;
	protected $coreTimeDown = 0;
	protected $alert;
	protected $log;
	protected $filepos;
	protected $newfs;
	protected $prevfs;
	protected $lines = array();
	protected $queueLimit = 1000;
	protected $queueMaxLimit = 4000;
	
	function __construct() {
		
		unset($this->coreDownStart);
		
		$this->alert = new Alert();
	
		$this->log = new Logging();
		
		$this->prevfs = $this->getFileSize();
		
	}
	
	function doFile() {
		
		
		$this->newfs = $this->getFileSize();
		
		$this->log->WriteToLog("Beginning File Processing");
		
		if ($this->newfs > $this->prevfs):
		
			$this->procFile(FALSE);
		
		elseif ($this->newfs < $this->prevfs):
		
			$this->procFile(TRUE);
			
		endif;
		
		$this->QueueSizeCheck();
	}
	
	function procFile($cycled = FALSE) {
		
		$file = fopen(FILENAME, "r");
		
		if(!$cycled) {
			
			fseek($file,$this->prevfs);
		}
		
		$this->log->WriteToLog("Reading File from line ". $this->prevfs);
		
		while (!feof($file)) {
			
			$buffer = fgets($file, 4096);
			
			$this->lines[] = $buffer;
			
			unset($buffer);
		}
		
		$this->log->WriteToLog("Finished Reading File");
		$this->prevfs = $this->newfs;
		
		$this->procData();
	}
	
	function procData() {
		
		$matches = array();
		$this->log->WriteToLog("Processing data and appending to the appropriate queues.");
		
		for ($x=0; $x < count($this->lines); $x++) {
			
			if (preg_match("/(dbid)\s+([0-9]+)\s/i",$this->lines[$x],$matches) || 
				preg_match("/eventid\s+([0-9]+)/i",$this->lines[$x],$matches)) {
				
				print_r($matches);
					
				if($matches[1] == "dbid") {
					
					$this->dbidQueue[$matches[2]] = time();
				}
				
				else {
					
					$this->eventQueue[$matches[1]] = time();
				}
			}
			
			unset($matches);
		}
		
		unset($this->lines);
		$this->lines = array();
		
		$this->log->WriteToLog("Finished processing data.");
		$this->log->WriteToLog("DBID Queue Size: " . count($this->dbidQueue));
		$this->log->WriteToLog("Event Queue Size: " . count($this->eventQueue));
		
		print_r($this->dbidQueue);
		print_r($this->eventQueue);
	}
	
	function coreUp() {
		
		$errno ="";
		$errstr = "";
		
		// Check if CORE is down
		$this->log->WriteToLog("Checking if CORE is up.");
		
		$socket = fsockopen(CORE_MON_HOSTNAME, 8000, $errno, $errstr, 10);
		
		if(!$socket) {
			
			$this->log->WriteToLog("Core was found to be down.");
			
			if(!isset($this->coreDownStart)) {
				
				$this->coreDownStart = time();
			}
			
			$this->coreDown = TRUE;
			
			$this->alert->CoreDownCycle(TRUE, $this->coreDownStart);
			
		}
		
		else {
			
			$this->log->WriteToLog("CORE Appears to be up. Attempting GET from XMLRPCD");
			
			stream_set_blocking($socket, 1);
			
			fputs($socket, "GET / HTTP/1.1\r\n");
			fputs($socket, "Host: ". CORE_MON_HOSTNAME . "\r\n\r\n");
			
			if(preg_match("/200\sOK/i", fgets($socket, 4096))) {

				$this->log->WriteToLog("CORE is responding properly to XML requests.");
				
				if($this->coreDown) {
					
					$this->coreWasDown = TRUE;
					
					$this->coreTimeDown = time() - $this->coreDownStart;
					
					$this->alert->CoreDownCycle(FALSE, $this->coreDownStart);
					
					$this->coreDown = FALSE;
					
				}
				
				fclose($socket);
				
				return TRUE;
				
			}
			
			else {
				
				$this->log->WriteToLog("CORE is not yet responding to XML requests.");
				
				if(!isset($this->coreDownStart)) {
					
					$this->coreDownStart = time();
				}
				
				$this->coreDown = TRUE;
				
				$this->alert->CoreDownCycle(TRUE, $this->coreDownStart);
				
				fclose($socket);
				
			}
		}
	}
	
	function QueueProcess() {
		
		$this->log->WriteToLog("Processing event and dbid queues for matches.");
		
		foreach ($this->dbidQueue as $id => $timestamp) {
			
			// If this has been rebooted, leave the loop dammit.
			
			if($this->Rebooted):
			
				$this->eventQueue = array();
				$this->dbidQueue = array();
				break;
			
			elseif(array_key_exists($id,$this->eventQueue)):
				
				unset($this->eventQueue[$id]);
				unset($this->dbidQueue[$id]);
			
			else:
			
				$this->QueueCheck($id, $timestamp);
				
			endif;
		}
		
		$this->log->WriteToLog("Processing Finished. Queue Counts:");
		$this->log->WriteToLog("DBID: " . count($this->dbidQueue));
		$this->log->WriteToLog("EVENT: " . count($this->eventQueue));

	}
	
	function getFileSize() {
		
		clearstatcache();
		
		$temp = stat(FILENAME);
		
		return $temp[7];
	}

	function QueueCheck($dbid, $time) {
		
		// Need to check the overall time that the questioned DBID has existed
		
		$this->checkTime($dbid, $time);		
	}
	
	function QueueSizeCheck() {
	
		// Need to check the current size of the Queue
		
		if (count($this->dbidQueue) >= $this->queueMaxLimit):
			
			$this->queueLimit = 4000;
			$this->alert->QueueSizeAlert(TRUE, count($this->dbidQueue), $this->queueMaxLimit);
			
		elseif (count($this->dbidQueue) >= $this->queueLimit):
			
			$this->alert->QueueSizeAlert(FALSE, count($this->dbidQueue), $this->queueLimit);
			$this->QueueLimitAdjust();
		
		elseif (count($this->dbidQueue) < $this->LastQueueLimit):
		
			$this->QueueLimitAdjust(FALSE);
		
		endif;
	}
	
	function QueueLimitAdjust($growing = TRUE) {
		
		
		if($growing) {
					
			switch($this->queueLimit):
			
				case '1000':
					
					$this->log->WriteToLog("DBID Queue is over its limit of " . $this->queueLimit);
					$this->log->WriteToLog("Increasing limit to next tier.");		
					
					$this->queueLimit = 2000;
					$this->LastQueueLimit = 1000;
					break;
				
				case '2000':

					$this->log->WriteToLog("DBID Queue is over its limit of " . $this->queueLimit);
					$this->log->WriteToLog("Increasing limit to next tier.");		
					$this->queueLimit = 3000;
					$this->LastQueueLimit = 2000;
					break;
				
				case '3000':

					$this->log->WriteToLog("DBID Queue is over its limit of " . $this->queueLimit);
					$this->log->WriteToLog("Increasing limit to next tier.");		
					
					$this->queueLimit = 4000;
					$this->LastQueueLimit = 3000;
					break;
				
				default:
				
					$this->queueLimit = 1000;
					break;
			
			endswitch;
		}
		
		else {
			
			switch($this->queueLimit):
			
				case '4000':

					$this->log->WriteToLog("DBID Queue is under its limit of " . $this->queueLimit);
					$this->log->WriteToLog("Decreasing limit to next tier.");
										
					$this->queueLimit = 3000;
					$this->LastQueueLimit = 4000;
					break;
				
				case '3000':
	
					$this->log->WriteToLog("DBID Queue is under its limit of " . $this->queueLimit);
					$this->log->WriteToLog("Decreasing limit to next tier.");										
					$this->queueLimit = 2000;
					$this->LastQueueLimit = 3000;
					break;
				
				case '2000':
				
					$this->log->WriteToLog("DBID Queue is under its limit of " . $this->queueLimit);
					$this->log->WriteToLog("Decreasing limit to next tier.");					
				
					$this->queueLimit = 1000;
					$this->LastQueueLimit = 2000;
					break;
					
				default:
				
					$this->queueLimit = 1000;
					break;
			
			endswitch;
		}
	}
	
	function checkTime($dbid, $time) {
		
		if ($this->coreWasDown):
		
			// If CORE was down for 5 to 30 minutes... the time frame is skewed a bit.
			// We'll only want to wait about 10 minutes for the QUEUE to clear.
			
			if($this->coreTimeDown >= 300 && $this->coreTimeDown < 1800):
			
				if(time() - $time > 600):
				
					$this->alert->OpenNMSAlert($dbid,$time);
					$this->Restart();
					
				endif;
				
			// Else if CORE was down for longer, up to an hour and a half...
			// Lets make sure that the DBID has 30min from the time CORE was back up.
			
			elseif ($this->coreTimeDown >= 1800 && $this->coreTimeDown < 5400):
				
				if(time() - $time > 1800):
				
					$this->alert->OpenNMSAlert($dbid,$time);
					$this->Restart();
					
				endif;
				
			endif;
			
		else:
		
			if(time() - $time >= 300) {
				
				$this->alert->OpenNMSAlert($dbid,$time);
				$this->Restart();
			}
			
		endif;
	}
	
	function Restart() {
		
		$this->log->LogWriteReboot($this->dbidQueue);
		
		$this->alert->OpenNMSAlert();
		
		preg_match('/ok/i', system('/etc/init.d/opennms stop')) or $this->alert->RWFail(FALSE);
		
		$this->alert->OpenNMSCycle();
		
		preg_match('/ok/i',system('/etc/init.d/opennms start')) or $this->alert->RWFail(TRUE);
		
		$this->ReInit();
		
		$this->Rebooted = TRUE;
		
	}
	
	function ReInit() {
		
		self::__destruct();
		self::__construct();
	}
	
	function __destruct() {
		
		$array = get_class_vars(get_class($this));
		$this->dbidQueue = array();
		$this->eventQueue = array();
		foreach ($array as $var => $value):

			if ($value):

				$this->{$var} = $value;
			
			elseif (is_object($this->{$var})):
				
				$this->{$var}->__destruct();
				unset($this->{$var});
				
			else:
				
				unset($this->{$var});
				$this->{$var} = "";
			
			endif;
			
		endforeach;
	}
}

class Alert {
	
	protected $subject;
	protected $from;
	protected $body;
	protected $coreDownTimeAlert;
	
	function __construct() {
		
		$this->subject = "Alert: Rackwatch issue (".HOSTNAME.")";
		
		$this->from = 'rackwatch@' . HOSTNAME . '.rackspace.com';
		
		$this->body = "";
		
		$this->coreDownTimeAlert = 300;
	}
	
	function Page() {
			
		mail(PAGEADDS,$this->subject, $this->body, $this->from);
		
		self::__construct();
	}
	
	function CoreDownCycle($coreup = FALSE, $coreTimeDown) {
		
		$timedownmin = floor((time() - $coreTimeDown) / 60);
		
		$timeDown = time() - $coreTimeDown;
		
		if($coreup && $timeDown >= $this->coreDownTimeAlert):
			
			$this->coreDownTimesCycle();

			$this->subject = "Emergency: CORE is Experiencing a communications issue.";
			
			$this->body = "In our test of CORE's availability, we were unable to connect.\n";
			$this->body .= "This could mean that CORE is down.\n";
			$this->body .= "CORE has been down for " . $timedownmin . " minutes.\n";
			$this->body .= "Please investigate this issue immediately.\n\n";
			
			$this->body .= "If this is intentional, please ignore this email.\n";

			self::Page();

		elseif (!$coreup):
		
			$this->coreDownTimeAlert = 300;
			
			$this->subject = "[RESOLVED] Emergency: CORE is Experiencing a communications issue.";
			
			$this->body = "A connection to CORE has been successful and CORE has now responded.\n";
			$this->body .= "Please be aware that CORE was down for ";
			$this->body .= $timedownmin . " minutes.\n";
			
			self::Page();
			
		endif;
			
		
		
	}
	
	function coreDownTimesCycle() {

		switch($this->coreDownTimeAlert):
		
			case '300':

				$this->coreDownTimeAlert = 600;
				$this->Log->WriteToLog("CORE Downtime Limit is now:" . $this->coreDownTimeAlert . ".");
				break;
							
			case '600':
				
				$this->coreDownTimeAlert = 900;
				$this->Log->WriteToLog("CORE Downtime Limit is now:" . $this->coreDownTimeAlert . ".");
				break;
			
			case '900':
				
				$this->coreDownTimeAlert = 1800;
				$this->Log->WriteToLog("CORE Downtime Limit is now:" . $this->coreDownTimeAlert . ".");
				break;

			case '1800':
				
				$this->coreDownTimeAlert = 3600;
				$this->Log->WriteToLog("CORE Downtime Limit is now:" . $this->coreDownTimeAlert . ".");
				break;
				
		endswitch;
		
	}
	
	function QueueSizeAlert($emergency, $dbidcount, $limit = 0) {
		
		$emergencysubj = "[EMERGENCY] DBID Queue has reached its maximum size!";
		
		$urgentsubj = "[URGENT] DBID Queue has surpassed its current threshold of ". $limit ."!";
		
		$this->subject = $emergency ? $emergencysubj : $urgentsubj;
		
		$this->body = "OpenNMS has an event queue that is getting too large.\n";
		$this->body .= "Please investigate immediately.\n\n";
		
		$this->body .= "Current Queue Size: " . $dbidcount . " events.\n";
		$this->body .= "Current limit threshold: ". $limit . " events.\n";
			
		self::Page();
	}
	
	function OpenNMSAlert($dbid, $time) {
		
		$time = floor($time / 60);
		
		$this->body = "Rackwatch appears to be experiencing an issue with XML.\n";
		$this->body .= "The current DBID [".$dbid."] has been idle for " . $time . " minutes.\n";
		$this->body .= "The default action will be taken. Please investigate immediately.";
		
		self::Page();
	}
	
	function RWFail ($proc) {
		
		$this->subject = "Emergency: Rackwatch has failed to restart properly!";
		$this->body = "An attempt to restart Rackwatch has failed.\n";
		$this->body .= "It failed while attempting to ";
		$this->body .=  $proc ? "start" : "stop" . " the application.\n\n";
		$this->body .= "Please investigate this IMMEDIATELY as rackwatch has failed.\n";
		$this->body .= "This script will now close. Please restart it as soon as Rackwatch is up.";
		
		self::Page();
	}
	
	function OpenNMSCycle() {

		system('mv ' . FILENAME . ' ' . FILENAME . '.seizure.'. date(DATE_ATOM).'.log');
		system('touch ' . FILENAME);
	}

	function __destruct() {
		
		/*$array = get_object_vars($this);
		
		foreach ($array as $var):
		
				unset($this->{$var});
				$this->{$var} = "";
	
		endforeach;
		
		unset($this);*/
	}
}

class Logging {
	
	protected $main;
	protected $dbid;
	
	function __construct() {
		
		$this->cycleLog();
		 
	}
	
	function WriteToLog($mesg) {
		
		$this->cycleLog();
		
		$mesg = "[" . date(DATE_ATOM) ."] " . $mesg . "\n";
		
		$fp = fopen($this->main, 'a');
		fwrite($fp,$mesg);
		fclose($fp);
	}
	
	function CycleLog() {
		
		$this->main = '/var/log/opennms/monitor.'.date("Ymd").'.log';
		$this->dbid = '/var/log/opennms/dbids.'.date("Ymd-H.m.s").'.log';
	}
	
	function LogWriteReboot($dbids = array()) {
		
		$this->cycleLog();
		
		$this->WriteToLog("Event queue is no longer processing. Restarting Rackwatch.");
		
		$fp = fopen($this->dbid, 'a');
		
		foreach($dbids as $dbid) {
			
			fwrite($fp, $dbid . "\n");
		}
		
		fclose($fp);
	}
	
	function __destruct() {
		
		/*$array = get_object_vars($this);
		
		foreach ($array as $var):
		
				unset($this->{$var});
				$this->{$var} = "";
	
		endforeach;
		
		unset($this);*/
	}
	
}

function usage() {
	
		print "OpenNMS XMLRPCD Daemon v.01 Usage\n";
		print "=================================\n\n";
		print "Usage:\n\n";
		print "onmsxmon [options]\n\n";
		print "Options:\n\n";
		print "help - Display this help.\n";
		print "start - start the daemon.\n";
		print "stop - stop the daemon.\n\n";
		exit;
	}
?>