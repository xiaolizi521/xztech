<?php

/* 
** MySQL Abstraction Class
** Allows object creation for DB interaction on more advanced level.
** Objective of this class: Bring exceptions into MySQL errors to provide
** Better error verification and diagnosis.
**
** Last Updated: January 20th, 2007.
** Duplication explicitly denied.
** Contact me at <OffbeatAdam AT gmail DOT com>
**
** REQUIREMENTS:
** PHP5 with MySQLi support compiled and configured
** MySQL 4.1 for MySQLi interaction.
*/

/*
** Exception generation for Connection and Query Exceptions.
** TODO: Seperate into other file.
*/

class ConnException extends Exception {} // Exception for Connection Errors
class QueryException extends Exception {} // Exception for Query Errors

/*
** Function to provide more success in variables passed to Class.
** Properly escapes all strings going to the MySQLi functions.
*/

function add_single_quotes($arg)
{
 	/* Single quote and escape single quotes and backslashes */
 	return "'" . addcslashes($arg, "'\\") . "'";
}

/*
** Main Class. Child to main MySQLi class from PHP.
** Usage:
** $var = new DB('hostname', 'username', 'password', 'db');
** All functions are the same as original MySQLi class.
**
** Reminder: To make use of the exceptions, you must connect/query in a TRY block
** and CATCH the type of error.
** 
** Connection Example:
**
**	// Try opening a new DB connection     
**		try {
**			$var = new DB('hostname', 'username', 'password', 'db');
**		}
**   
**		// Catch any errors thrown if connection fails.
**
**		catch(ConnectException $exception) {
**			echo "Connection Error\n";
**			var_dump($exception->getMessage());
**		}
**
**		// Catch any other errors that may have occured.
**      
**  	catch(Exception $exception) {
**     		echo "Other Script Error\n";
**			var_dump($exception->getMessage());
**		}
**
** Query Example:
**
**		try {
**			$result = $var->query("SELECT * FROM table");
**		}
**
**		// Catch any errors thrown if query fails.
**
**		catch(QueryException $exception) {
**			echo "Query Error\n";
**			var_dump($exception->getMessage());
**		}
**
**		// Catch any other errors that may have occured.
**
**		catch Exception $exception) {
** 			echo "Other Script Error\n";
**			var_dump($exception->getMessage());
**		}
*/

class DB extends mysqli {
	
	function __construct()
	{
	 	/* Pass all arguments sent to constructor to the parent constructor */
		 $args = func_get_args();
		 eval("parent::__construct(" . join(',',array_map('add_single_quotes', $args)) . ");");
		
		/* Throw an error if the connection fails */
		if(mysqli_connect_error()) {
		 	throw new ConnectException(mysqli_connect_error(),mysqli_connect_errno());
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
