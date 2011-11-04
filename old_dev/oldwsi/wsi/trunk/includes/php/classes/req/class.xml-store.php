<?php

define("ELEM_CDATA",1);
define("ELEM_START",0);
define("ELEM_END",0);


*/

class xmlParser {

    public $parser;
    public $tag;
    public $xmlFileObj;
	private $_sectionTeam;
	
	/*
	** Constructor for building extended capabilities into XML parsing. 				  
	** Given Example: Instantiates a storage object that will then be used to rewrite a properly formatted XML file.
	** Create Parser Object
	** Pass parser handlers to object and 
	*/    
	
    function __construct (xmlFileObject $xmlFileObj = NULL) {

		// Create the parser object
		$this->parser = xml_parser_create('UTF-8');
		
		// Set the object paramaters
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, array(&$this, 'startElement'), array(&$this, 'endElement'));
		if($xmlFileObj):
		    $this->xmlFileObj = $xmlFileObj;
		    xml_set_character_data_handler($this->parser, 'charDataFile');
        else:
            xml_set_character_data_handler($this->parser, 'charData');
        endif;
		
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
    
    
    function startElement($parser, $tag, $attributes) {
    
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
	
    function endElement($parser, $tag) {
    
        /* Possibly Depreciated. Set up for end-element success. */
		$this->end_element = TRUE;	

		/* If this is the end of the USER element (the "USER" block), end processing and update db */
		/* Updates single user details. Better for memory management and faster. */
		if ('user' == strtolower($tag)) {

			// Rewrite this area in order to properly process "end" of user object. WSI only.
		}
	
    }

	/*
	** CDATA File handler. Handles all data between tags. (<tag>THIS IS CDATA</tag>)
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $data. Desc: CDATA variable. All data is stored here as a string.
	*/
	
    function charDataFile($parser, $data) {
      
		if(!$this->end_element):

			// Team variables need to be handled differently.
			
			if($this->_sectionTeam) {
				
				// Ranking inside of the team is displayed as a string, e.g "1 of 20"
				// Must split it to set data in database properly.
				if( $this->tag == 'rank'):
					
					$bar = explode(" of ",$data);
					$this->xmlFileObj->set_var(ELEM_CDATA,'teamrank',str_replace(",","",$bar[0]));
					$this->xmlFileObj->set_var(ELEM_CDATA,'teammembers',str_replace(",","",$bar[1]));
				
				else if ( $this->tag === 'team'):
				
					$this->xmlFileObj->set_var(ELEM_CDATA,$this->tag,$data);
				
				// Otherwise, set it up. extra part of variable is "team" plus tag name.
				else:
					
					/* Remove any commas from numbers > 999. */
					if($this->tag != "teamname") {
						
						$data = str_replace(",","",$data);
					}
			
					$foo = 'team';
					$foo .= $this->tag;
					$this->xmlFileObj->set_var(ELEM_CDATA,$foo,$data);
					
				endif;
			}
		
			// Otherwise, set up the current tag with data inside of the object.
			else {
				
				/* Remove any commas from numbers > 999. */
				if($this->tag != "name" && $this->tag != "country"):
					
					$data = str_replace(",","",$data);
					
                endif;
						
				$this->xmlFileObj->set_var(ELEM_CDATA,$this->tag,$data);
			}
				
		endif;
	}
	
	function charData() {
	    
	}


    /*
    ** Destructor. Example shown is to close the currently open DB connection.
    */
    
    function __destruct() {
    
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
			
			$this->elementArray[$this->_index] = $_element;
			$this->elementArrayData[$this->_index] = $data;
			$this->_index++;
		}
		
	
	}
	
	function indexSize () {
	
		return $this->_index;
	
	}
	
	/* Return Element Index from currentIndex for external usage. */
	
	function returnElemIndex ($iterate = true) {
			
		$temp =	$this->elementArray[$this->_currentIndex];
		
		if ($iterate) {
			
			$this->_currentIndex++;
		}
		
		return $temp;

	}
	
	/* Return Element Data from currentIndex for external usage. */
	
	function returnElemData ($iterate = true) {
	
		$temp =	$this->elementArrayData[$this->_currentIndex];
		
		if ($iterate) {
			
			$this->_currentIndex++;
		}
		
		return $temp;

	}
	
	/* Return associative array of all data for external usage. */
	
	function returnAssocAll () {
	
		foreach($this->elementArray as $key => $index) {
		
			foreach($this->elementArrayData as $key2 => $data) {
				
				$tempArray[$index] = $data;
				
			}
		}
		
		return $data;
	}
	
	/* Return Associative Array of currentIndex for external usage. */
	
	function returnAssocSingle ($iterate = true) {
	
		$temp[$this->elementArray[$this->_currentIndex]] = $this->elementArrayData[$this->_currentIndex];
		
		if($iterate) {
		
			$this->_currentIndex++;
		}
		
		return $temp;
	
	}

}

?>