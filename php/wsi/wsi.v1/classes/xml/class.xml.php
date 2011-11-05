<?php

require_once "../user/class.user.php";
require_once("../../config/required.php");

/*
** XML Parser Class. Depends on the PHP XML extension (expat/SAX).
** Last Updated: March 5th, 2008
** Created By: Adam Hubscher <AKA: OffbeatAdam>
** Modificaion explicitly denied.
** You can contact me at <OffbeatAdam AT gmail DOT com>
**
** TODO: 
** Modify the instances of XML_Parser::item.
** Modify structure.
** Is user object... needed? Is XML_Parser::item... needed?
**
*/

class XML_Parser {

    public $parser;
    public $tag;
    public $userObject;
	private $_sectionTeam;
	
	/*
	** Constructor for building extended capabilities into XML parsing. 				  
	** Given Example: Open Database Connection for storing parsed XML data into database. {WHY?}
	** Create Parser Object
	** Pass parser handlers to object and 
	*/    
	
    function __construct () {

		// Create the parser object
		$this->parser = xml_parser_create('UTF-8');
		
		// Set the object paramaters
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, array(&$this, 'start_element'), array(&$this, 'end_element'));
		xml_set_character_data_handler($this->parser, 'character_data');
		$this->userObject = new xmlUserObject;
		
    }
    
    // Prior to parsing, a parser must be created.
    // Parser is created as child object to parser.
    // This allows memory freeing to occurr at destructor execution.
    // This function must exist.
    
    function parse($data) {
		
		// Parse the data
		xml_parse($this->parser, $data);
		
	}
	
    /*
	** Parser specific function. Handles any Starter Element (<element>)
    ** Also handles starter element attributes within tag.
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $tag. Desc: Holds TAG name in string.
    */
    
    
    function start_element($parser, $tag, $attributes) {
    
    	/* 
    	** Protect against CDATA ending up in element end (NON XML RFC PROTECTION) 
    	** Example: </ELEMENT data="THISISWRONG">
    	** Was causing issues with uneducated creation of XML files for "indexing"
    	** Required for safety of data integrity.
    	**
    	*/
    	
		$this->end_element = false;
		
		/* Uppercase or lowercase, tag shouldn't matter. XML rfc says tag is lower case. So be it. =) */
		/* We want to be able to know what tag we are on in CDATA. This allows for proper processing. */
		/* Set this to lower case version of tag. */
		
		$this->tag = strtolower($tag);

		/* Whatpulse Only: Teams pose issue due to malformed XML. Boolean protects this. */
		/* We will need to pre-prep the variable for teams in the CDATA before passing to object data handler. */
		/* To allow for proper TEAM data processing */
		/* Set boolean team value to true. */
		if ('team' == $this->tag) {
		
			$this->_sectionTeam = true;
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

			$this->userobjwhat->commit();
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
    
    	// Rewrite the Destructor. Destroy the parser.

		xml_parser_free($this->parser);
		
    }
}

?>
