<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "files_M.php" );
			break;
		case( "super-manager"):
			include( "files_SM.php" );
			break;
		case( "employee" ):			
			switch ( $p_levelType ){
				case ( 'timekeeper' ):
				case ( 'manager' ):
				case ( 'human resources' ):
				case ( 'system administrator' ):
					include( "files_SM.php" );
					break;
					
				default:
					include( "files_E.php" );
					break;	 
			}
			break;
			
 		default:
 			header( "Location: index.php" );
 			break;
	}
?>