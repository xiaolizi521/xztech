<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "project_task_hours_edit_M.php" );
			break;
		case( "super-manager"):
			include( "project_task_hours_edit_SM.php" );
			break;
		case( "employee" ):
			include( "project_task_hours_edit_E.php" );
			break;
	}
?>