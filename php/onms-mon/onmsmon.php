#!/usr/bin/php
<?php

// Report all errors, we want to know whats going on.

error_reporting(E_ERROR);

declare(ticks = 1);

// Change these appropriately

define("HOSTNMAE", "onms-1.dfw1");
define("PAGEADDS", "'page.adam.hubscher@rackspace.com', 'coresysadmin@rackspace.com'");
define("MAX_CORE_DOWNTIME", 1800);
define("FILENAME", "/var/log/opennms/daemon/xmlrpcd.log");

// For testing, set this to vcoreudev8.dev.core.rackspace.com
// Otherwise, set this to onms2core.core.rackspace.com

define("CORE_MON_HOSTNAME", 'vcoreudev8.dev.core.rackspace.com');

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
		
		$obj->parseQueue();
	}
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
		
		if ($this->newfs > $this->prevfs):
		
			$this->procFile(FALSE);
		
		elseif ($this->newfs < $this->prevfs):
		
			$this->procFile(TRUE);
			
		endif;
	}
	
	function procFile($cycled = FALSE) {
		
		$file = fopen(FILENAME);
		
		if(!$cycled) {
			
			fseek($file,$this->prevfs);
		}
		
		while (!feof($file)) {
			
			$buffer = fgets($file, 4096);
			
			$this->lines[] = $buffer;
			
			unset($buffer);
		}
		
		$this->prevfs = $this->newfs;
		
		$this->procData();
	}
	
	function procData() {
		
		for ($x=0; $x < count($this->lines); $x++) {
			
			if (preg_match("/(dbid)\s+([0-9]+)\s/i",$this->lines[$x],$matches) || 
				preg_match("/eventid\s+([0-9]+)/i",$this->lines[$x],$matches)) {
					
					if($matches[1] == "dbid") {
						
						$this->dbidQueue[$matches[2]] = time();
					}
					
					else {
						
						$this->eventQueue[$matches[1]] = time();
					}
				}
		}

	}
	
	function coreUp() {

		// Check if CORE is down
		
		if(!$socket = fsockopen(CORE_MON_HOSTNAME, 8000, $errno, $errstr, 10)) {
			
			if(!isset($this->coreDownStart)) {
				
				$this->coreDownStart = time();
			}
			
			$this->coreDown = TRUE;
			
			$this->alert->CoreDownCycle();
			
		}
		
		else {
			
			stream_set_blocking($socket, 1);
			
			fputs($socket, "GET / HTTP/1.1\r\n");
			fputs($socket, "Host: ". CORE_MON_HOSTNAME . "\r\n\r\n");
			
			if(preg_match("/200\sOK/i", fgets($socket, 4096))) {
				
				if($this->coreDown) {
					
					$this->coreWasDown = TRUE;
					
					$this->alert->CoreDownCycle(TRUE);
					
					$this->coreTimeDown = time() - $this->coreDownStart();
					
					$this->coreDown = FALSE;
				}
				
				fclose($socket);
				
			}
			
			else {
				
				if(!isset($this->coreDownStart)) {
					
					$this->coreDownStart = time();
				}
				
				$this->coreDown = TRUE;
				
				$this->alert->CoreDownCycle();
				
				fclose($socket);
				
			}
		}
	}
	
	function QueueProcess() {
		
		foreach ($this->dbidQueue as $id => $timestamp) {
			
			if(array_key_exists($id,$this->eventQueue)) {
				
				unset($this->eventQueue[$id]);
				unset($this->dbidQueue[$id]);
				
			}
			
			else {
				
				$this->QueueCheck($id, $timestamp);
			}
		}
	}
	
	function getFileSize() {
		
		clearstatcache();
		
		$temp = stat(FILENAME);
		
		return $temp[7];
	}

	function QueueCheck($dbid, $time) {
		
		// Need to check the current size of the Queue
		
		if (count($this->dbidQueue) >= $this->queueMaxLimit):
			
			$this->queueLimit = 4000;
			$this->alert->QueueSizeAlert(TRUE);
			
		elseif (count($this->dbidQueue) >= $this->queueLimit):
			
			$this->alert->QueueSizeAlert();
			$this->QeueLimitAdjust();
		
		elseif (count($this->dbidQueue) <= $this->queueLimit):
		
			$this->QueueLimitAdjust(FALSE);
		
		endif;
		
		// Need to check the overall time that the questioned DBID has existed
		
		$this->checkTime($dbid, $time);		
	}
	
	function QueueLimitAdjust($growing = TRUE) {
		if($growing) {
			
			switch($this->queueLimit):
			
				case '1000':
					
					$this->queueLimit = 2000;
					break;
				
				case '2000':
					
					$this->queueLimit = 2000;
					break;
				
				case '3000':
					
					$this->queueLimit = 4000;
					break;
				
				default:
				
					$this->queueLimit = 1000;
					break;
			
			endswitch;
		}
		
		else {
			
			switch($this->queueLimit):
			
				case '4000':
					
					$this->queueLimit = 3000;
					break;
				
				case '3000':
					
					$this->queueLimit = 2000;
					break;
				
				case '2000':
					
					$this->queueLimit = 1000;
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
		
		$this->alert->OpenNMSAlert();
		
		preg_match('/ok/i', system('/etc/init.d/opennms stop')) or $this->alert->RWFail(FALSE);
		
		$this->alert->OpenNMSCycle();
		
		preg_match('/ok/i',system('/etc/init.d/opennms start')) or $this->alert->RWFail(TRUE);
		
		$this->ReInit();
		
	}
	
	function ReInit() {
		
		self::__destruct();
		self::__construct();
	}
	
	function __destruct() {
		
		$array = get_class_vars(get_class($this));
		
		foreach ($array as $var => $value):

			if ($value):

				$this->{$var} = $value;
			
			elseif ($value == "alert" || $value == "log"):
				
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
	
	function __construct() {
		
		$this->subject = "Alert: OpenNMS issue (".HOSTNAME.")";
		
		$this->from = 'rackwatch@' . HOSTNAME . '.rackspace.com';
		
		$this->body = "";
	}
	
	function Page() {
			
		mail(PAGEADDS,$this->subject, $this->body, $this->from);
		self::__construct();
	}
	
	function CoreDownCycle() {
		
	}
	
	function QueueSizeAlert() {
		
		
	}
	
	function dBidTime($dbid, $time) {
		
	}
	
	function RWFail ($proc) {
		
	}
	
	function OpenNMSCycle() {

		system('mv ' . FILENAME . ' ' . FILENAME . '.seizure.'. date(DATE_ATOM).'.log');
		system('touch ' . FILENAME);
	}
	
	function OpenNMSAlert() {
		
	}

	function __destruct() {
		
		$array = get_class_vars(get_class($this));
		
		foreach ($array as $var => $value):
		
				unset($this->{$var});
				$this->{$var} = "";
	
		endforeach;
		
		unset($this);
	}
}

class Logging {
	
	function __construct() {
		
	}
	
	function WriteToLog() {
		
	}
	
	function CycleLog() {
		
	}
	
	function LogWriteReboot() {
		
	}
	
	function __destruct() {
		
		$array = get_class_vars(get_class($this));
		
		foreach ($array as $var => $value):
		
				unset($this->{$var});
				$this->{$var} = "";
	
		endforeach;
		
		unset($this);
	}
	
}