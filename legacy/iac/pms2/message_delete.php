<?

session_start();
	
$p_level = $_SESSION['p_level'];
include( "header_functions.php" );

if( strlen( $p_level ) > 0 )
	//$db->query( "DELETE FROM messages WHERE id = ".$_REQUEST['id'] );
	$db->query( "DELETE FROM message_recipients WHERE id = ".$_REQUEST['id'] );

header( "Location: messages.php" );

?>