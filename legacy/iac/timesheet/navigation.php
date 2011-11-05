<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	function getMessageCount($id){			
		$msgCount = mysql_query( "SELECT COUNT(*) AS count FROM message_recipients WHERE message_id > 0 AND employee_id = $id" );
		$row = mysql_fetch_assoc( $msgCount );
		return $row['count'];			
	}
	
	switch( $p_level )
	{
		case( "manager" ):
			include( "navigation_manager.php" );
			break;
		case( "super-manager"):
			include( "navigation_super_manager.php" );
			break;
		case( "employee" ):
			include( "navigation_employee.php" );
			break;
		case( "reports" ):
			include( "navigation_reports.php" );
			break;
	}
?>