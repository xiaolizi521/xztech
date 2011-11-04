<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "messages_M.php" );
			break;
		case( "super-manager"):
			include( "messages_SM.php" );
			break;
		case( "employee" ):
			include( "messages_E.php" );
			break;
 		default:
 			header( "Location: index.php" );
 			break;
	}
?>