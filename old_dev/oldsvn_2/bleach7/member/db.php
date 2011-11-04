<?php 
/* Includes */
require_once ( 'class.php' );

/* Include the database classes and exceptions */
require_once( "../classes/db/class.db.php" );
require_once( "../config/exceptions.php" );

/* Database Constants */
define( "DB_HOST", "triton.x-zen.cx" );
define( "DB_USER", "bleach7" );
define( "DB_PASS", "WrETr75aHuHE4ef8" );
define( "DB_NAME", "b7_bleach7" );

//ob_start();
/*
$sql_location = 'triton.x-zen.cx'; 		//location of MySQL || Was "localhost"
$sql_username = 'bleach7'; 				//username || Was "remote"
$sql_password = 'WrETr75aHuHE4ef8'; 	//password || Was "af32@Q#%#@FSDFDS%#@"
$sql_database = 'b7_bleach7'; 			// database name || Was "interaction"
*/

try 
{
	$database = new DB( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	
	if ( $database->connect_error )
		throw new Exception( 'Error connecting to the database' );
} 
catch ( Exception $exception )
	echo $exception->getMessage();
	//redirect to the downpage ( indexdowntime.php )



if ( isset( $_COOKIE['user_id'] ) && isset( $_COOKIE['password'] ) )
{
	$query 	= sprintf( "SELECT * FROM `users` WHERE `user_id`=%s AND `password`='%s'", 
					$_COOKIE['user_id'],
					$_COOKIE['password'] );

	$result = $database->query( $query );
	
	if ( $result->num_rows )
	{
		$user_info = $result->fetch_array();
		
		$user_B7 = new B7_User( $user_info );
	}
}




//$dbh = mysql_connect ( $sql_location, $sql_username, $sql_password ) or die ( include ( 'indexdowntime.php' ) );
//mysql_select_db ( $sql_database ) or die ( 'SELECT error: ' . mysql_error() );  
/*
if ( isset ( $_COOKIE['user_id'] ) && isset ( $_COOKIE['password'] ) ) {
	$result_userinfo = mysql_query ( 'SELECT * FROM `users` WHERE `user_id`=\'' . mysql_real_escape_string ( $_COOKIE['user_id'] ) . '\' AND `password`=\'' . mysql_real_escape_string ( $_COOKIE['password'] ) . '\'' );
	if ( mysql_num_rows ( $result_userinfo ) > 0 ) {
		$user_info = mysql_fetch_array ( $result_userinfo );
		$user_B7 = new B7_User ( $user_info );
	}
}*/





?> 










