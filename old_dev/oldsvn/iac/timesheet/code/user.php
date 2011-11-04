<?php

/**
 * Author:   	Cory Becker
 * Date:   	 	March 24, 2007
 * Website:	 	www.corybecker.com
 *
 * Desc:			Handles all session / user management functions
 */

class user
{
	// ------------------------------------------------------------
	// Class fields
	// ------------------------------------------------------------
	var $db;
	var $user_table = "employee";
	var $debug = false;
	var $array;
	
	// ------------------------------------------------------------
	// Constructors
	// ------------------------------------------------------------
	function __construct()
	{
		if( $this->debug ) debug( "Auth", "Constructed" );
		$this->db = new db(); 
	}
	
	function user()
	{
		$this->__construct();
	}
	
	
	// ------------------------------------------------------------
	// Core session management methods
	// ------------------------------------------------------------
	function checkUser( $username, $password )
	{
		if( $this->debug ) debug( "checkUser()", "Username: $username  Password: $password" );
		
		$query = array( "username" => $username, "password" => $password, "active" => "1");
		$this->db->get( $this->user_table, $query );
				
		//return $db->result["rows"] == 1; -- too bad it's not Java.
		if( $this->debug ) debug( "checkUser()", "Found ".$this->db->result["rows"]." Results" );
		
		if( $this->db->result["rows"] > 0 )
		{
			$this->array = mysql_fetch_assoc( $this->db->result['ref'] );
			return true;
		}
		else
			return false;
	}
	
	
	// ------------------------------------------------------------
	// Get permissions
	// ------------------------------------------------------------
	function getPermissionLevel( $username )
	{
		$query = array( "username" => $username );
		$array = $this->db->get( $this->user_table, $query );
		
		$key = mysql_fetch_assoc( $this->db->result['ref'] );
		
		return $key['type'];
	}
	
	// ------------------------------------------------------------
	// Get permission type (full, limited, bookeeper)
	// ------------------------------------------------------------
	function getPermissionType( $username )
	{
		$query = array( "username" => $username );
		$array = $this->db->get( $this->user_table, $query );
		
		$key = mysql_fetch_assoc( $this->db->result['ref'] );
		
		return $key['permission'];
	}
	
	// ------------------------------------------------------------
	// Core user management methods
	// ------------------------------------------------------------
	function add( $username, $password )
	{
		// Check to make sure that user does not exist
		$array = array( "username" => $username );
		$this->db->get( $this->user_table, $array );
		
		if( $this->db->result['rows'] == 0 )
		{
			$array = array( "username" => $username, "password" => md5($password) );
			$this->db->add( $this->user_table, $array );
			return true;
		}
		else
			return false;
	}
	
	function changePassword( $username, $password )
	{
		$array = array( "username" => $username );
		$this->db->get( $this->user_table, $array );
		
		if( $this->db->result['rows'] == 1 )
		{
			$array = array( "username" => $username, "password" => md5($password) );
			$where = array( "username" => $username );
			$this->db->update( $this->user_table, $array, $where );
		}
	}

	function remove( $username )
	{
		$where = array( "username" => $username );
		$this->db->delete( $this->user_table, $where );
	}	
}

?>