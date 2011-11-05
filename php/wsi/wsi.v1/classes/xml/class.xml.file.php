<?php

//require_once "class.db.php";
//require_once "class.user.php";
require_once("../../config/required.php");

define("ELEM_CDATA",1);
define("ELEM_START",0);
define("ELEM_END",0);
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
	
    function __construct (xmlFileObject $userObject) {

		// Create the parser object
		$this->parser = xml_parser_create('UTF-8');
		
		// Set the object paramaters
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, array(&$this, 'start_element'), array(&$this, 'end_element'));
		xml_set_character_data_handler($this->parser, 'character_data');
		$this->userObject = $userObject;
		
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
		else {
			
			$this->_sectionTeam = false;
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

				// Team variables need to be handled differently.
				
				if($this->_sectionTeam) {
					
					// Ranking inside of the team is displayed as a string, e.g "1 of 20"
					// Must split it to set data in database properly.
					if( $this->tag == 'rank') {
						
						$bar = explode(" of ",$data);
						$this->userObject->set_var(ELEM_CDATA,'teamrank',str_replace(",","",$bar[0]));
						$this->userObject->set_var(ELEM_CDATA,'teammembers',str_replace(",","",$bar[1]));
					
					}
					else if ( $this->tag === 'team') {
					
						$this->userObject->set_var(ELEM_CDATA,$this->tag,$data);
					}
					
					// Otherwise, set it up. extra part of variable is "team" plus tag name.
					else {
						
						/* Remove any commas from numbers > 999. */
						if($this->tag != "teamname") {
							
							$data = str_replace(",","",$data);
						}
				
						$foo = 'team';
						$foo .= $this->tag;
						$this->userObject->set_var(ELEM_CDATA,$foo,$data);
					}
				}
			
				// Otherwise, set up the current tag with data inside of the object.
				else {
					
					/* Remove any commas from numbers > 999. */
					if($this->tag != "name" && $this->tag != "country") {
						
						$data = str_replace(",","",$data);
					}
							
					$this->userObject->set_var(ELEM_CDATA,$this->tag,$data);
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

class xmlFileObject extends XML_Parser {
	
	private $elementArray;
	private $elementArrayData;
	private $arrayIndex;
	private $_index;
	private $_currentIndex;
	
	function __construct () {
	
		$this->_index = 0;
		$this->_currentIndex = 0;
	}
	
	/* Set appropriate variable for Element Name.
	** If element type is end element, do nothing.
	*/
	
	function set_var ($_elementType, $_element, $data) {
	
		if ($_elementType) {
			
			$this->elementArray[] = $_element;
			$this->elementArrayData[] = $data;
			
		}
		
	
	}
	
	function indexSize () {
	
		return $this->_index;
	
	}
	
	/* Return Element Index from currentIndex for external usage. */
	
	function returnElemIndex ($iterate = true) {
			
		$temp =	$this->elementArray[$this->_currentIndex];
		
		if ($iterate) {
			
			$this->iterate();
		}
		
		return $temp;

	}
	
	/* Return Element Data from currentIndex for external usage. */
	
	function returnElemData ($iterate = true) {
	
		$temp =	$this->elementArrayData[$this->_currentIndex];
		
		if ($iterate) {
			
			$this->iterate();
		}
		
		return $temp;

	}
	
	/* Return associative array of all data for external usage. */
	
	function returnAssocAll () {
	
		foreach($this->elementArray as $index) {
		
			foreach($this->elementArrayData as $data) {
				
				$tempArray[$index] = $data;
				
			}
		}
		
		return $tempArray;
	}
	
	/* Return Associative Array of currentIndex for external usage. */
	
	function returnAssocSingle ($iterate = true) {
	
		$temp[$this->elementArray[$this->_currentIndex]] = $this->elementArrayData[$this->_currentIndex];
		
		if($iterate) {
		
			$this->iterate();
		}
		
		return $temp;
	
	}
	
	function iterate() {
		
		$this->_currentIndex = $this->currentIndex + 1;
	}

}

?>