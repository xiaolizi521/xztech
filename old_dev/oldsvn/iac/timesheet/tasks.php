<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "tasks_M.php" );
			break;
		case( "super-manager"):
			include( "tasks_SM.php" );
			break;
		case( "employee" ):
			include( "tasks_E.php" );
			break;
	}
?>