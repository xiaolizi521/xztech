<?
	session_start();
		
	// take out when working
	//header( "Location: index.php" );
	
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "home_manager.php" );
			break;
		case( "super-manager"):
			include( "home_super_manager.php" );
			break;
		case( "employee" ):
			if ($p_levelType == 'timekeeper' || $p_levelType == 'manager'){
				include( "home_employee_bookeeper.php" );	
			}else{
				include( "home_employee.php" );
			}
			break;
		case( "reports" ):
			include( "reports.php" );
			break;
		default:
			header( "Location: index.php" );
			break;
	}
?>