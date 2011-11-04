<?php

require_once("../../config/required.php");

// Session Class
// Usage:
// Requires the following MySQL database setup:
/*
CREATE TABLE  `sessions` (
`id` VARCHAR( 32 ) NOT NULL ,
`access` INT( 18 ) UNSIGNED NULL ,
`data` TEXT NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM COMMENT =  'Sessions Database'
*/
// Begin sessions:
// $session = new Session();
// End session: Sessions are normally closed automatically.
// Perform this if attempting to speed up load time for multiple frames. 
// unset($session);

class Session {

	private $_sess;
	public $maxTime;
	private $db;
	
	public function __construct() {
	
		// (host,user,passwd,database)
		$this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		$this->maxTime['expire'] = time();
		$this->maxTime['gc'] = get_cfg_var('session.gc_maxlifetime');
		
		$cookie_timeout = 60 * 60 * 6;
		$cookie_path = "/";
		
		session_set_cookie_params($cookie_timeout, $cookie_path);
		
		session_set_save_handler (
					array($this,'_open'),
					array($this,'_close'),
					array($this,'_read'),
					array($this,'_write'),
					array($this,'_destroy'),
					array($this,'_clean'));
					
		register_shutdown_function('session_write_close');
		session_start();
	}
	
	// Open Session
	public function _open() {
		
		return true;
	}
	
	
	// Close Session
	
	public function _close() {
	
		$this->_clean($this->maxTime['gc']);
		$this->db->close();
		return true;
	}
	
	
	// Read Session Data
	
	public function _read($id) {
	
		$query = "SELECT 'Data' FROM 'sessions' WHERE ID = '".$id."'";
		
		$result = $this->db->query($query);
		
		$data = $result->fetch_assoc();
		
		$num_rows = (bool) $result->num_rows >= 1;
		
		return $num_rows ? $data : "";
		
	}
	
	// Write session data
	
	public function _write($id, $data) {
	
		$query = "REPALCE INTO 'sessions' VALUES ('".$id."','".$this->Time['Expire']."','".$data."','".time()."')";
		
		return $this->db->query($query);
	}
	
	
	// Delete Session Data
	
	public function _destroy($id) {
		
		$_SESSION=array();
		
		$query = "DELETE FROM 'sessions' WHERE ID = '".$id."'";
		
		return $this->db->query($query);
	}
	
	// Garbage Collect
	
	public function _clean($max) {
	
		$old = ($this->maxTime['access'] - $max);
		
		$query = "DELETE FROM 'sessions' WHERE 'Expire' < '".$old."'";
		
		return $this->db->query($query);
	}
	
	public function __destruct() {
	
		session_write_close();
	
	}
}

?>
