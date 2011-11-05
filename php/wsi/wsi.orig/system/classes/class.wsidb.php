<?php
/*

Whatpulse Images Project - Database Interaction

Creator: Adam Hubscher A.K.A AgentGreasy/OffbeatAdam
Version: 1.00-13.9.2006

*/

/*

Synopsis:

With the release of MySQL 4.1 and above, a new interface was developed
for PHP 5.0. This interface, which was made to provide a faster and more
streamlined as well as OOP interface for the nearly 20 year old predecessor
was deemed ext/mysqli. Because of its inclusion on the WSI server - it is
optimal to code for such a extension.

*/

/* Begin DB Class Now */

/* First we will define the Exception classes to properly catch and throw errors.
This will allow us to better catch errors that the script runs into. */

class ConnException extends Exception {}
class QueryException extends Exception {}

/* Proper quote escaping function */

function add_single_quotes($arg)
{
 	/* Single quote and escape single quotes and backslashes */
 	return "'" . addcslashes($arg, "'\\") . "'";
}

/*

Parent: PHP mysqli MySQL Improved
Child: wsiDB Whatpulse Signature Images DB

*/

class wsiDB extends mysqli {
	
	function __construct()
	{
	 	/* Pass all arguments sent to constructor to the parent constructor */
		 $args = func_get_args();
		 eval("parent::__construct(" . join(',',array_map('add_single_quotes', $args)) . ");");
		
		/* Throw an error if the connection fails */
		if(mysqli_connect_error()) {
			echo mysqli_connect_error();
		 	#throw new ConnectException(mysqli_connect_error(),mysqli_connect_errno());
		}
	}
	
	function query($query)
	{
	 	/*Perform a SQL Query utilizing the parent Query function*/
		$result = parent::query($query);
		
		/*Throw an error if the query fails.*/
	 	if(mysqli_error($this)) {
	 	 	throw new QueryException(mysqli_error($this),mysqli_errno($this));
	 	}
	 	
	 	return $result;
	}
}
?>
