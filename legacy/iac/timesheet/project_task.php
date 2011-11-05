<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "project_task_M.php" );
			break;
		case( "super-manager"):
			include( "project_task_SM.php" );
			break;
		case( "employee" ):
			if ( $p_levelType == 'manager' || $p_levelType == 'human resources' ){
				include( "project_task_SM.php" );
			}else{
				include( "project_task_E.php" );
			}
			break;
	}
?>