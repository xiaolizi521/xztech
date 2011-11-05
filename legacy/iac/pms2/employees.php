<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype']; 
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "employees_M.php" );
			break;
		case( "super-manager"):
			include( "employees_SM.php" );
			break;
		case( "employee" ):
			if ( $p_levelType == 'human resources' ){
				include( "employees_SM.php" );				
			}else{
				header( "Location: home.php" );				
			}
			break;
 		default:
 			header( "Location: index.php" );
 			break;
	}
?>