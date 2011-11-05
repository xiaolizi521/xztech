<?

session_start();
	
$p_level = $_SESSION['p_level'];

if( $p_level == "super-manager" || $p_level == "employee" )
	include( "header_functions.php" );
else
{
	header( "Location: home.php" );
	exit();
}

// Check to see if anything was posted
$post = $_POST;

$db->query( "SELECT * FROM files WHERE id = ".$_REQUEST['id'] );

if( $db->result['rows'] == 0 )
	header( "Location: files.php?delete_error" );

$row = mysql_fetch_assoc( $db->result['ref'] );

@unlink( "./uploads/".$row['filename'] );
$db->query( "DELETE FROM files WHERE id = ".$_REQUEST['id'] );

header( "Location: files.php?deleted" );

?>