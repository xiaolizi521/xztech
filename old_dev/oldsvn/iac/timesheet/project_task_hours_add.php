<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "project_task_hours_add_M.php" );
			break;
		case( "super-manager"):
			include( "project_task_hours_add_SM.php" );
			break;
		case( "employee" ):
			include( "project_task_hours_add_E.php" );
			break;
	}
?>