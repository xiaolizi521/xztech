<?

/* Include the database classes and exceptions */
require_once( "/home/b7test/public_html/classes/db/class.db.php" );
require_once( "/home/b7test/public_html/config/exceptions.php" );

/* Database Constants */
define( "DB_HOST", "triton.x-zen.cx" );
define( "DB_USER", "bleach7" );
define( "DB_PASS", "WrETr75aHuHE4ef8" );
define( "DB_NAME", "b7_bleach7" );

try 
{
	$database = new DB( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	
	if ( $database->connect_error )
		throw new Exception( 'Error connecting to the database' );
} 
catch ( Exception $exception )
{
	echo $exception->getMessage();
}
	
?>
	
