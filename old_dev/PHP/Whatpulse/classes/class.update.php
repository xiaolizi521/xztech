<?php

include "class.wsidb.php";
include "class.wpuser.php";

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
	protected $db;
	
	// Connect to the database and set up the object properties
	function __construct () {
		
		/* Try DB Connection */
		try {

			$this->db = new wsiDB('localhost', 'offbeatz_wsi', 'pulsestats', 'offbeatz_pulsestats');
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
		$tag = strtolower($tag);
		/* Protect against empty tag name */
			$this->tag = $tag;

		/* Create USER object for updating at end of processing */		
		if ('user' == strtolower($tag)) {
			
			$this->item = new wpulseUSER($this->db);
		}
		/* To allow for proper TEAM data processing */
		/* Set boolean team value to true. */
		if ('team' == $tag) {
			
			$this->item->team = TRUE;
			$this->teamelement= TRUE;
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
		if ('user' == strtolower($tag) && $this->update) {
		
			$this->item->update();
			unset($this->item);
		}
		
		if ('team' == strtolower($tag)) { $this->teamelement=FALSE; }
	
	}
	
	/* Character Data Handler */
	/* Takes specific expat properties */
	/* $parser - Parser Object */
	/* $data - Data found inside of tag, stored as string. */
	function character_data($parser, $data) {
		//echo $this->tag . "<br />";
		//echo "At cdata ".html_entity_decode($data)." <br />";
		
		if(!$this->end_element) {
		
			if(strtolower($this->tag) == "name" && !$this->teamelement) {
				//echo "NAME TAG! <br />";
				/* Escape the username with mysql-capable string escaping. */
				$username = $this->db->escape_string($data);
				
				/* Check if user exists. */
				try {
				
					$result = $this->db->query("SELECT * FROM `whatpulse` where `user` = '" . $username . "'");
					// If user doesnt exist, return 0. *** MODIFY ***
				
				}
				
				/* Catch any query errors or other errors. */
				catch(QueryException $exception) {
					
					echo "Query Error\n";
					var_dump($exception->getMessage());
				}
				
				catch(Exception $exception) {
					
					echo "Other Script Error\n";
					var_dump($exception->getMessage());
				}
				
					if (!$result->num_rows) {
					 
						$this->update = FALSE;
						
						}
					
					else { 
		

						$this->update = TRUE;				
					}
				
				$result->close();
			}

			// Check for an empty object, if its empty - no need to set up any variables.
			if (is_object($this->item) && $this->update) {
				
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
		$this->db->close();
	}
}
?>