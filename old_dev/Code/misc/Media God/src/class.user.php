<?php

class Main {
 
 	function __construct() {
 	 
 	}

	function __destruct() {
	 
	}

	function send_mail() {
	
	}

	function get_perms() {
	 
	}

	function pass_generate() {
	 
	}

}


class User extends Main {
 
	function __construct() {
	 
	}
	 
	function __destruct() {
	 
	}

	function login() {
	
	}

	function logout() {
	
	}

	function retrieve_password() {
	 
	}

	function change_password() {
	 
	}
}

class User_Tools extends User {
 

	function __construct() {
	 
	}

	function __destruct() {
	 
	}

	function view_profile() {
	 
	}

	function edit_profile() {
	 
	}

	function send_message() {
	 
	}

	function view_message() {
	 
	}

	// Raid Specific
	
	function raid_register() {
	 
	}

	function raid_unregister() {
	 
	}

	function send_raid_update() {
	 
	}

}

class Admin_Tools extends User {
 
	function __construct() {
	 
	}

	function __destruct() {
	 
	}

	/* all admin functions go here... could be alot of them. */
}

