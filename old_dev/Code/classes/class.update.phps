<?php

require_once "class.db.php"; // Replaces DB
require_once "class.user.php"; // Replaces wpUser

/* Class: Whatpulse Images XML (wpulseXML) */
/*
** Description:
** 
** Whatpulse Images Service XML File Parser 
** Parses XML data from Whatpulse service for updating
** Database efficiently and in an organized OOP fashion
**
** Last Updated: February 1st, 2007
** Version: 1.0
** Changes:
**
** Updated comments, organized spacing, changed class structure
**
**
*/

/* Class Structure */
class wpulseXML {

	/* Variable Setups */
	var $parser;
	var $tag;
	var $item;
	var $end_element;
	var $count = 1;
	var $user;
	var $teamelement = false;
	var $update = TRUE;
	protected $db;
	
	// Connect to the database and set up the object properties
	function __construct () {
		$this->user_count=0;
		/* Try DB Connection */
		try {

			$this->db = new DB('localhost', 'whatpulse', 'FU55mwh3CzfZBFSK', 'whatpulse');
		}
		
		/* Catch Errors on Failure */
		catch(ConnectException $exception) {

			echo "Connection Error\n";
			var_dump($exception->getMessage());
		}

		catch(Exception $exception) {

			echo "Other Script Error\n";
			var_dump($exception->getMessage());
		}
	}
	
	// Parser function
	function parse($data) {
		
		// Create the parser object
		$this->parser = xml_parser_create('UTF-8');
		
			// Set the object paramaters
			xml_set_object($this->parser, $this);
			xml_set_element_handler($this->parser, array(&$this, 'start_element'), array(&$this, 'end_element'));
			xml_set_character_data_handler($this->parser, 'character_data');
		
		// Parse the data
		xml_parse($this->parser, $data);
		
		// Free the object
		xml_parser_free($this->parser);
	}
	
	/* Start Element Handler */
	/* Takes specific expat properties */
	/* $parser - Parser Object */
	/* $tag - Element Name */
	/* $attributes - Any Tag Attributes */
	
	function start_element($parser, $tag, $attributes) {
	//	echo "At Start Element $tag <br />";
		/* Possibly Depreciated. Set up for non-end element failure. */
		$this->end_element = FALSE;
		$this->tag = strtolower($tag);
		
		//printf("%s\n",$this->tag);
		/* Protect against empty tag name */

		/* Create USER object for updating at end of processing */		
		if ('user' == $this->tag) {
			$this->item = new wpulseUSER($this->db);
		}
		/* To allow for proper TEAM data processing */
		/* Set boolean team value to true. */
		if ('team' == $this->tag && is_object($this->item)) {
		
			$this->item->team = TRUE;
		}
		
	}
	
	/* End Element Handler */
	/* Takes specific expat properties */
	/* $parser - Parser Object */
	/* $tag - Element Name */	
	function end_element($parser, $tag) {
		
		/* Possibly Depreciated. Set up for end-element success. */
		$this->end_element = TRUE;	
	//	echo "At end element $tag <br />";
		/* If this is the end of the USER element (the "USER" block), end processing and update db */
		/* Updates single user details. Better for memory management and faster. */
		if ('user' == strtolower($tag)) {
			//echo "Updating User: " . $this->item->name . "Of Rank: " . $this->item->rank . "<br />";
			$this->item->update();
			
			if($this->db->affected_rows > 0) { $this->user_count = $this->user_count + 1; }
			
			unset($this->item);
		}
	//	elseif ('user'== strtolower($tag) && !$this->update) { $this->update = TRUE; }
	
	}
	
	/* Character Data Handler */
	/* Takes specific expat properties */
	/* $parser - Parser Object */
	/* $data - Data found inside of tag, stored as string. */
	function character_data($parser, $data) {
		//echo $this->tag . "<br />";
		//echo "At cdata ".html_entity_decode($data)." <br />";
		
		if(!$this->end_element) {

			// Check for an empty object, if its empty - no need to set up any variables.
			if (is_object($this->item)) {
				
				//if(strtolower($this->tag) == "name" && !$this->teamelement) {
				
				//echo "NAME TAG! <br />";
				/* Escape the username with mysql-capable string escaping. */
				//$username = $this->db->escape_string($data);
				
				/* Check if user exists. */
				//try {
				
				//	$result = $this->db->query("SELECT * FROM `whatpulse` where `user` = '" . $username . "'");
					// If user doesnt exist, return 0. *** MODIFY ***
				
				//}
				
				/* Catch any query errors or other errors. */
				/*catch(QueryException $exception) {
					
					echo "Query Error\n";
					var_dump($exception->getMessage());
				}
				
				catch(Exception $exception) {
					
					echo "Other Script Error\n";
					var_dump($exception->getMessage());
				}
				
			//	echo "num_rows was " . $result->num_rows . " for . ". $username ."<br />";
				
					if ($result->num_rows === 0) {
					 	
					 	unset($this->item);
					 	$this->update = FALSE;
						
						}
					
					else { 
		

						$this->update = TRUE;				
					}
				
				$result->close();
				}*/
				// Team variables need to be handled differently.
				if($this->item->team) {
					
					// Ranking inside of the team is displayed as a string, e.g "1 of 20"
					// Must split it to set data in database properly.
					if( $this->tag == 'rank') {
						
						$bar = explode(" of ",$data);
						$this->item->teamrank = str_replace(",","",$bar[0]);
						$this->item->teammembers = str_replace(",","",$bar[1]);
					}
					
					// Otherwise, set it up. extra part of variable is "team" plus tag name.
					else {
						
						/* Remove any commas from numbers > 999. */
						if($this->tag != "teamname") {
							
							$data = str_replace(",","",$data);
						}
				
						$foo = 'team';
						$foo .= $this->tag;
						$this->item->{$foo} = $data;
					}
				}
			
				// Otherwise, set up the current tag with data inside of the object.
				else {
					
					/* Remove any commas from numbers > 999. */
					if($this->tag != "name" && $this->tag != "country") {
						
						$data = str_replace(",","",$data);
					}
							
					$this->item->{$this->tag} = $data;
				}
			}
		}
	}
	
	// Close the database connection. (Destructor)
	function __destruct() {
	
		echo "<br /><br /><strong>We updated ".$this->user_count." users.</strong></br></br>";
		$this->db->close();
	}
}
?>
