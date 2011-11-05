<?php
/*
** Exception generation for Connection and Query Exceptions.
*/

class ConnException extends Exception {} // Exception for Connection Errors
class QueryException extends Exception {} // Exception for Query Errors

/*
** Exceptions for Prepared Statements
*/

class stmtExecuteException extends Exception {}
class stmtPrepareException extends Exception {}
class stmtBindException extends Exception{}
class stmtCloseException extends Exception{}

?>