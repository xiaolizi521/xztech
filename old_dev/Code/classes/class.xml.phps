<?php

require_once "class.db.php";
require_once "class.user.php";


/*
** XML Parser Class. Depends on the PHP XML extension (expat/SAX).
** Last Updated: January 20th, 2007
** Created By: Adam Hubscher <AKA: OffbeatAdam>
** Modificaion explicitly denied.
** You can contact me at <OffbeatAdam AT gmail DOT com>
*/

class XML_Parser {
    var $parser;
    var $tag;
    protected $DB;

	/*
	** Constructor for building extended capabilities into XML parsing. 				  
	** Given Example: Open Database Connection for storing parsed XML data into database.
	*/    
	
    function __construct () {
		
		// Try opening a new DB connection. Exception is handled by class.   
        $this->db = new DB('localhost', 'whatpulse', 'FU55mwh3CzfZBFSK', 'whatpulse');
    }
    
    // Prior to parsing, a parser must be created.
    // Parser is created as child object to parser.
    // This allows memory freeing to occurr at destructor execution.
    // This function must exist.
    
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
	
    /*
	** Parser specific function. Handles any Starter Element (<element>)
    ** Also handles starter element attributes within tag.
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $tag. Desc: Holds TAG name in string.
    */
    
    
    function start_element($parser, $tag, $attributes) {
    
    	/* Possibly Depreciated. Set up for non-end element failure. */
    	
		$this->end_element = FALSE;
		
		$this->tag = strtolower($tag);

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

	/*
	** Same functionality of start_element. Handles any end element.
	** End elements do not have attributes, so they are not considered.
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $tag. Desc: Holds TAG name in string.
	*/
	
    function end_element($parser, $tag) {
    
        /* Possibly Depreciated. Set up for end-element success. */
		$this->end_element = TRUE;	

		/* If this is the end of the USER element (the "USER" block), end processing and update db */
		/* Updates single user details. Better for memory management and faster. */
		if ('user' == strtolower($tag)) {

			$this->item->update();			
			if($this->db->affected_rows > 0) { $this->user_count = $this->user_count + 1; }
			unset($this->item);
		}
	
    }

	/*
	** CDATA handler. Handles all data between tags. (<tag>THIS IS CDATA</tag>)
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $data. Desc: CDATA variable. All data is stored here as a string.
	*/
	
    function character_data($parser, $data) {
      
		if(!$this->end_element) {

			// Check for an empty object, if its empty - no need to set up any variables.
			if (is_object($this->item)) {
				
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
    
    /*
    ** Destructor. Example shown is to close the currently open DB connection.
    */
    
    function __destruct() {
    
    	echo "<br /><br /><strong>We updated ".$this->user_count." users.</strong></br></br>";
        $this->DB->close() // Close DB connection.
    }
}

?>
