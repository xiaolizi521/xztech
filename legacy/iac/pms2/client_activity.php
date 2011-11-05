<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "client_activity_M.php" );
			break;
		case( "super-manager"):
			include( "client_activity_SM.php" );
			break;
		case( "employee" ):
			if ( $p_levelType == 'manager' || $p_levelType == 'human resources' ){
				include( "client_activity_SM.php" );	
			}else{
				include( "client_activity_E.php" );
			}
			break;
	}
?>