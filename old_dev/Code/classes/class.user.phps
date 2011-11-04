<?php

require_once "class.db.php";
require_once "class.session.php";

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

private $db;
private $session;

	function __construct () {
	
		// Construct this user's object.
		
		$this->db = new DB('localhost', 'whatpulse', 'FU55mwh3CzfZBFSK', 'whatpulse');
		
		
	}
	
	function __destruct () {
	
		// Free memory.
		
		$this->db->close();
	
	}
	
	
	function login () {
	
		// Login to system
		// Update sessions
		// Gather all required data for future site access
		// Minimize rendering time
	}
	
	function logout () {
	
		// Clear data cache/session
		// Logout
	}

}

// XML User Update Functions
class xmlUser extends User {

	

}

// Main User Update Functions

class mainUser extends User {


}

// Create a new user

class newUser extends User {

	function register() {
	
	}

}