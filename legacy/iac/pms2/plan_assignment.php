<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "plan_assignment_M.php" );
			break;
		case( "super-manager"):
			include( "plan_assignment_SM.php" );
			break;
		case( "employee" ):
			if ( $p_levelType == 'manager' || $p_levelType == 'human resources' ){
				include( "plan_assignment_SM.php" );
			}else{
				include( "plan_assignment_E.php" );
			}
			break;
	}
?>