<?php

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
class XML extends Controller {
	
	// Connect to the database and set up the object properties
	function __construct () {
		
		$this->db = parent::db;
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
		
	}
	
	/* End Element Handler */
	/* Takes specific expat properties */
	/* $parser - Parser Object */
	/* $tag - Element Name */	
	function end_element($parser, $tag) {
		
	}
	
	/* Character Data Handler */
	/* Takes specific expat properties */
	/* $parser - Parser Object */
	/* $data - Data found inside of tag, stored as string. */
	function character_data($parser, $data) {

	}
	
	// Close the database connection. (Destructor)
	function __destruct() {

	}
}
?>
