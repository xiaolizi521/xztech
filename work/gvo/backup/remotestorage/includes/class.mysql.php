<?php

/* 
** MySQL Abstraction Class
** Allows object creation for DB interaction on more advanced level.
** Objective of this class: Bring exceptions into MySQL errors to provide
** Better error verification and diagnosis.
**
** Last Updated: June 26th, 2007.
** Last Changes: Moved Exception Try/Catch into class, to clean up interaction code.
** Duplication explicitly denied.
** Contact me at <OffbeatAdam AT gmail DOT com>
**
** REQUIREMENTS:
** PHP5 with MySQLi support compiled and configured
** MySQL 4.1 for MySQLi interaction.
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
** Function to provide more success in variables passed to class.
** Properly looks at the scalar type of var.
** Returns char type for mysqli_prepare.
**
*/

function getVarType() {

    $type = "";
    $args = func_get_args();
    
    for ($x = 0; $x < count($args); $x++):

        // If integer, return i
        if (is_int($args[$x])):
            $type .= "i";

        // If string, return s
        elseif (is_string($args[$x])):
            $type .= "s";

        // If numerical double, return d
        elseif (is_double($args[$x])):
            $type .= "d";
        
        // Mysqli->prepare will not allow anything but the above three, except for blob. For blob, manually set.
        else:
            die ("Invalid non-scalar value");

        endif;

    endfor;
            
    return $type;
        
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
** 
** $test = new DB('hostname','username','password','dbname');
**
** Query Example:
**
** $query = "select * from users";
**
** Data Example:
**
** $result = $test->query($query);
**
** print_r($result->fetch_assoc());
**
**
*/

class DB extends mysqli {
    
    function __construct() {

        /* Try to connect, throw exception on error */
        try {
    
            /* Pass all arguments sent to constructor to the parent constructor */
            $args = func_get_args();
            eval("parent::__construct(" . join(',',array_map('add_single_quotes', $args)) . ");");
            
            /* Throw an error if the connection fails */
            if(mysqli_connect_error()):
                throw new ConnException(mysqli_connect_error(),mysqli_connect_errno());
            endif;
        }
        
        /*Catch a connection error.*/
        catch (ConnException $exception) {
        
            printf("Connection Error Occurred.\n");
            printf("The error reported was:\n");
            echo $exception->getMessage();
        }
        
        /*Catch any other error*/
        catch (Exception $exception) {
        
            printf("Other Error Occurred.\n");
            printf("The error reported was:\n");
            echo $exception->getMessage();
        }
    }
    
    function query($query) {
        
        /*Attempt the query. Throw exception on error or failure.*/
        try {

            /*Perform a SQL Query utilizing the parent Query function*/
            $result = parent::query($query);
            
            /*Throw an error if the query fails.*/
            if(mysqli_error($this)):
                throw new QueryException(mysqli_error($this),mysqli_errno($this));
            endif;
        }
        
        /*Catch any query exception*/
        catch (QueryException $exception) {
            
            printf("Query Error Occurred.\n");
            printf("The error reported was:\n");
            echo $exception->getMessage();
        }
        
        /*Catch any other exception*/
        catch (Exception $exception) {
        
            printf("Other Error Occurred.\n");
            printf("The error reported was:\n");
            echo $exception->getMessage();
        }
        
        return $result;
    }

    function prepare($query) {
        
        $this->stmt = new DBStmt($this, $query);

        return $this->stmt;
    }
}

?>
