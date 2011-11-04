<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "projects_M.php" );
			break;
		case( "super-manager"):
			include( "projects_SM.php" );
			break;
		case( "employee" ):
			if ( $p_levelType == 'manager' || $p_levelType == 'human resources' ){
				include( "projects_SM.php" );	
			}else{
				include( "projects_E.php" );
			}
			break;
	}
?>