<?php

/* 
** XML User Update Functions
** XML Update Requires:
** class.xml.php
** Parser
** XML Signatures file
** XML Update Scripting [from old exec.update.php]
** A calling object or script that utilizes class to maximum effectiveness.
**
*/

/*
** Method Functionality ** ** Prototype **
xmlUser::updateSingle($username); -> 
create parser (); ->
begin parsing ->
return data ->
handle data into xmlUserObject ->
process xmlUserObject ->
unset xmlUserObject ->
return 0;

xmlUser::updateAll(); ->
create parser (); ->
begin parsing ->
return data ->
handle data into new xmlUserObject ->
process xmlUserObject ->
unset xmlUserObject ->
return 0 on EOF, continue if not.

xmlUser::viewSingleUserUpdate() -> process updateSingle up to point [xmlUserObject::process()]
store data in session ->
output data ->
print form -> [submit] <- update with displayed information [report] -> report wrong information
Finished.
*/

class xmlUser extends User {
	
	private $db;
	private $fp;
	private $parser;
	private $timer;
	
	function __construct () {
	
		/* Define DB for this class if parent DB connection not available */

		if (!Parent::db) {
			$this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		}
		
		$this->parser = new XML_Parser();
		
		// Continue Constructor
		
	}
	
	static function updateSingle ($username) {
	
	}
	
	function updateAll () {
	
	}
	
	function viewSingleUserUpdate ($username) {
	
	}
	
}

class xmlUserObject extends XML_Parser {
	
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
			
			$this->elementArray[$arrayIndex] = $_element;
			$this->elementArrayData[$arrayIndex] = $data;
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