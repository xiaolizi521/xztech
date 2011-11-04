<?php

include "class.db.php";


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
		
		// Try opening a new DB connection     
        try {
            $this->DB = new DB('hostname', 'username', 'password', 'db');
        }
        
        // Catch any errors thrown if connection fails.
        catch(ConnectException $exception) {
           echo "Connection Error\n";
           var_dump($exception->getMessage());
        }
        
        catch(Exception $exception) {
           echo "Other Script Error\n";
           var_dump($exception->getMessage());
        }
    }
    
    /*
	** Parser specific function. Handles any Starter Element (<element>)
    ** Also handles starter element attributes within tag.
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $tag. Desc: Holds TAG name in string.
    */
    
    function start_element($parser, $tag, $attributes) {
     
    }

	/*
	** Same functionality of start_element. Handles any end element.
	** End elements do not have attributes, so they are not considered.
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $tag. Desc: Holds TAG name in string.
	*/
	
    function end_element($parser, $tag) {
        
    }

	/*
	** CDATA handler. Handles all data between tags. (<tag>THIS IS CDATA</tag>)
	** Var: $parser. Desc: Parser object, required by eXpat construction.
	** Var: $data. Desc: CDATA variable. All data is stored here as a string.
	*/
	
    function character_data($parser, $data) {
        
    }
    
    /*
    ** Destructor. Example shown is to close the currently open DB connection.
    */
    
    function __destruct() {
        $this->DB->close() // Close DB connection.
    }
}

?>
