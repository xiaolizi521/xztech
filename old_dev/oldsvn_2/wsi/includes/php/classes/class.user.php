<?php

class User {
	
	// Constructor calls "getInfo" to populate class values
	/*
	** This Class has the functionality of providing the user information in an easily generatable interface.
	**
	** The purpose of this class is to produce a pseudo-struct rendition of the user information.
	**
	** Usage:
	**
	** $foo = new User(BY_TYPE, USER_ID_DATA);
	**
	** BY_TYPE is of the following Constant/Global Static value: UUID, ID, or Name.
	**
	** ID is the ID of the row in the database.
	** UUID is the unique identifier each user is associated with.
	** Name is the username. This is the least preferred method.
	**
	** The default option is by UUID.
	** Note: Not all names are UTF8 standard, searching by them can be problematic.
	** It can still be done though, if direly needed.
	*/
	
	function __construct($by = UUID, $foo) {
	
		$this->db = new DB(DB_HOST,DB_USER,DB_PASS,DB_NAME);
		
			
		switch($by):
			
			case(UUID):
			
				$this->getInfoByUUID($foo);
				break;
			
			case(ID):
			
				$this->getInfoByID($foo);
				break;
			
			case(Name):
				
				$this->getInfoByName($foo);
				break;
			
			default:
			
				$this->getInfoByUUID($foo);
				break;
				
		endswitch;
	}
	
	
	// Get Info sets all of the appropriate variables for information pertaining to the user itself.
	
	/*
	
	Info Required:
	
	UUID
	Name
	Key Count
	Click Count
	Click Distance (if available in the XML)
	Team
	Team Clicks
	Team Keys
	Rank
	Team Rank
	Country
	
	*/
	
	function getInfoByName ($var) {
	
		$query = "select uuid,name,country,rank,keys,clicks,tkeys,tclicks,tname from users where username = '" . $var . "'";
		
		$result = $this->db->query($query);
		
		$data = $this->result->fetch_assoc();
		
		foreach($data as $key => $value):
		
			$this->{$key} = $value;
			
		endforeach;
		
	}
	
	function getInfoById ($var) {

		$query = "select uuid,name,country,rank,keys,clicks,tkeys,tclicks,tname from users where id = '" . $var . "'";
		
		$result = $this->db->query($query);
		
		$data = $this->result->fetch_assoc();
		
		foreach($data as $key => $value):
		
			$this->{$key} = $value;
			
		endforeach;
		
	}
	
	function getInfoByUUID ($var) {

		$query = "select uuid,name,country,rank,keys,clicks,tkeys,tclicks,tname from users where uuid = '" . $var . "'";
		
		$result = $this->db->query($query);
		
		$data = $this->result->fetch_assoc();
		
		foreach($data as $key => $value):
		
			$this->{$key} = $value;
			
		endforeach;
		
	}

	static 	function _genID($string){

		$string = substr(md5($string), 0, -16);

		return $string;
	}	
}

?>