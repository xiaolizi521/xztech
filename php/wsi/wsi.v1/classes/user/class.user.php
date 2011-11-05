<?php

require_once("../../config/required.php");

/*
**
** Name: User Class
** Written By: Adam Hubscher
**
** Purpose:
**
** Object to define and maximize efficiency between user and render.
** Will define methods of interaction between database, and site.
** Controls flow of information related to user.
** Permissions.
**
*/

class User {

protected $db;
private $session;
protected $username;
protected 

	function __construct () {
	
		// Construct this user's object.
		
		$this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME, USER_DB);
		
		
	}
	
	function __destruct () {
	
		// Free memory.
		
		$this->db->close();
	
	}
	
	
	function login ($username) {

		// Login to system
		// Update sessions
		// Gather all required data for future site access
		// Minimize rendering time

		$this->username = $username;
		
	}
	
	function logout () {
	
		// Clear data cache/session
		// Logout
	}

}

// Main User Update Functions

class mainUser extends User {


}

// Create a new user

class newUser extends User {

	// Form Parser, to receive data from submitted User Registration Form
	
	function __construct($parse) {
			
			if($parse) {
	
				Parent::__construct();
			
			}
	}
	
	function parseForm () {
	
		$this->formVars = $_POST;
		
		
	}
	
	// Data Verification.
	// Based on _dataType, verify data is clean and secure.
	
	function checkData () {
	
	}
	
	// Final step - Add user to DB, then login.
	
	function register() {
	
	}
	
	// Output Form for New User to input registration Data
	
	function renderForm () {
	
	}
	
	// Grab User Data from XML file.
	// If no user data found in xml, inform user of timeframe
	// Until their data may be available.
	
	function getUserData () {
	
	}

}
