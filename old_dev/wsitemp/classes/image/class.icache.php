<?php

// Required Information in all scripts - Constants, Sessions etc.
require_once("../../config/required.php");

// Required Classes for Potential Use
require_once("class.image.php");


/*
 * Image Cache Handler
 * 
 * Whatpulse Signature Images
 * 
 * Author: Adam Hubscher AKA OffbeatAdam AKA AgentGreasy
 * 
 * Version: 1.0
 * 
 * Purpose: Provide functions to handle the image cache and its relations.
 * 
 * Usage: See various functions.
 * 
 */

class iCache {

	/*
	 * Constructor
	 * 
	 * Builds Database Object
	 */
	function __construct($db) {
		
		if($db) {
			
			$this->db = $db;
		}
		
		else {
			
			$this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		}
	}
	
	/*
	 * Update XML
	 * 
	 * Provides functionality to external services to update the XML settings of an image.
	 * 
	 * Usage: iCache::uXML($uid,$xml) where
	 * 
	 * UID is the ID for the user in the DB
	 * 
	 * XML is the DOM formed XML with appropriate field definitions. 
	 * 
	 * Returns TRUE when update is successful, otherwise false.
	 */
	
	final static public function uXML($uid,$xml) {
		
		/*
		 * This function will be primarily called externally.
		 * 
		 * Check for DB variable existance, and if needed, create it.
		 */
		if (!$this->db) {
			
			$this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			$unset = true;
		}
		
		// Prepare query based on XML UPDATE statement
		$this->stmt = $this->db->prepare(UPDATE_XML);
		
		// Set the types for the variables
		$types = getVarType($xml, $uid);	
		
		// Bind the paramaters to the query
		$this->stmt->bind_param($types, $xml, $uid);
		
		// Execute the query
		$this->stmt->execute();
		
		// All we care about as a result, is the # of rows affected
		$this->rows = $this->stmt->affected_rows();
		
		// Close the prepared query, only needed once.
		$this->stmt->close();
		
		// If we set the DB variable due to direct call, we should close it properly.
		if($unset) { $this->db->close(); }
		
		// Return TRUE if successful
		if($this->rows == 1) {
			
			return 1;
		}
		
		// Otherwise... return false.
		
		else {
			
			return 0;
		}
		
	}
	
	/*
	 * Check Status
	 * 
	 * Provides functionality to external services to verify status of cached image.
	 * 
	 * Usage: iCache::checkStatus($uid) where $uid is the ID of the user in question.
	 * 
	 * Returns: TRUE when cache is young, FALSE when update required.
	 */
	
	final protected function checkStatus($uid) {
		
		/* Prepare statement from constant IMAGE_CACHE_STATUS */
		$this->stmt = $this->db->prepare(IMAGE_CACHE_STATUS);
		
		/*Bind UID int to prepared statement.*/
		$this->stmt->bind_param("i", $uid);
		
		/*Execute the query.*/
		$this->stmt->execute();
		
		/*Bind the result set to the $lastupdated variable.*/
		$this->stmt->bind_result($this->lastupdated);
		
		/*Fetch the result set*/
		$this->stmt->fetch();
		
		/*
		 * If the image is greater than 3 hours old... update.
		 * 
		 * Otherwise, ignore.
		 */
		
		if(time() - $this->lastupdated > 10800) {
			
			return FALSE;
		}
		
		else {
			
			return TRUE;
		}
	}

	final protected function iStore($new = false, $uid, $imagedata) {
		
		if (!$new) {

			// Prepare statatement to update image cache with new binary information
			$this->stmt = $this->db->prepare(IMAGE_STORE);
		
		}
		
		else {
			
			
			$this->stmt = $this->db->prepare(IMAGE_NEW_STORE);
		}
		
		/*Bind UID int to prepared statement.*/
		$this->stmt->bind_param("bii", $imagedata, time(), $uid);
		
		/*Execute the query.*/
		$this->stmt->execute();
		
		// Since we are not selecting information, all that we care about is success.
		$this->rows = $this->stmt->affected_rows();
		
		// Close the statement
		$this->stmt->close();
		
		// Return TRUE if successful
		if($this->rows == 1) {
			
			return 1;
		}
		
		// Otherwise... return false.
		
		else {
			
			return 0;
		}
		
	}
	
	final protected function iRecall($uid) {
		
		/* Prepare statement from constant IMAGE_CACHE_STATUS */
		$this->stmt = $this->db->prepare(IMAGE_RECALL);
		
		/*Bind UID int to prepared statement.*/
		$this->stmt->bind_param("i", $uid);
		
		/*Execute the query.*/
		$this->stmt->execute();
		
		/*Bind the result set to the $lastupdated variable.*/
		$this->stmt->bind_result($this->data);
		
		/*Fetch the result set*/
		$this->stmt->fetch();
		
		// Close the statement
		$this->stmt->close();
		
		return $this->data;
	}
	
	function __destruct() {
		
		$this->db->close();
		
		unset($this);
	}
}
?>