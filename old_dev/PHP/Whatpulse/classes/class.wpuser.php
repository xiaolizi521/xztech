<?php

/* Whatpulse Images User handler */

/*
** Description:
** Handles data from XML parsing and updates the database on a per-user basis
** 
** Last Updated: February 1st, 2007
** Version: 1.0
**
** Changes:
** 
** Cleaned up code and added comments.
**
*/

class wpulseUSER extends wpulseXML {

	/* Neeeded variables for storing data */
	public $name;
	public $country;
	public $rank;
	public $clicks;
	public $keys;
	public $username;
	public $teamname;
	public $teamclicks;
	public $teamkeys;
	public $teamrank;
	public $teammembers;
	public $team;

	/* Need to move the wsiDB object to a variable within the object. */
	function __construct ($wsiDB) {
		
		$this->wsiDB = $wsiDB;
	}

	/* Function to update the database. Only performed when a user block has been parsed. */
	function update() {
		
		/* Make sure that the username is properly escaped */
		$this->name = mysql_escape_string($this->name);
		
		/* Set up the query */
		$query = "UPDATE `whatpulse` SET "
					. "`tkc` = '" . $this->keys
					. "', `tmc` = '" . $this->clicks
					. "', `rank` = '" . $this->rank
					. "', `country` = '" . mysql_escape_string($this->country) . "' ";
		
		/* Team updating has extra variables that must be processed as well. */
		if ($this->team) {
	
			$query .= ", `tname` ='" . mysql_escape_string($this->teamname)
						. "', `tkeys` ='" . $this->teamkeys
						. "', `tclicks` ='" . $this->teamclicks
						. "', `trank` ='" . $this->teamrank
						. "', `tmembers` = '" . $this->teammembers ."' ";
		}
		
		$query .= "where user = '" . $this->name . "'";	
		echo "Attempging to update user: " . $this->name . " <br />";
		/* Attempt the update query. */
		try {
			
			$result = $this->wsiDB->query($query);
		}
		
		/* Catch any query errors or other errors that may occur. */
		catch(QueryException $exception) {
		
			echo "Query Error\n";
			var_dump($exception->getMessage());
		}
		
		catch(Exception $exception) {
		
			echo "Other Script Error\n";
			var_dump($exception->getMessage());
		}	
	}

	function __destruct() {
	
	}
}

?>