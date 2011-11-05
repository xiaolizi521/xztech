<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	if( $p_level != "super-manager" 
		&& $p_levelType != 'manager'
		&& $p_levelType != 'human resources')
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page specific functions
	$id = $_REQUEST['id'];
	
	// Remove employees related to project
	$db->query( "DELETE FROM project_employees WHERE project_id = $id" );
	
	// Get list of project tasks
	$db->query( "SELECT * FROM project_task WHERE project_id = $id" );
	
	$db2 = new db();
	
	while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
	{
		// Remove project hours
		$db2->query( "DELETE FROM project_task_hours WHERE project_task_id = ".$row['id'] );
	}
	
	$db2->query( "DELETE FROM project_task WHERE project_id = $id" );
	
	$db->query( "DELETE FROM project WHERE id = $id" );
	
	$db->query( "DELETE FROM messages WHERE project_id = $id" );
	
	header( "Location: projects.php" );
	
?>