<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( $p_level != "super-manager" )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page specific functions
	$id = $_REQUEST['id'];
	$project_id = $_REQUEST['project_id'];
	
	// Get list of tasks assigned to employee
	$db->get( "project_task", array( "employee_id" => $id, "project_id" => $project_id ) );
	$db2 = new db();
	
	// Remove taks hours related to tasks related to employee
	while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
	{
		// $db2->delete( "project_task_hours", array( "project_task_id" => $row['id'] ) );
		$db2->query( "DELETE FROM project_task_hours WHERE project_task_id = ".$row['id'] );
		
	}

	// Remove tasks related to employee
	//$db->delete( "project_task", array( "employee_id" => $id, "project_id" => $project_id ) );
	$db2->query( "DELETE FROM project_task WHERE employee_id = ".$id." AND project_id = ".$project_id );
		
	// Remove employee's relationship with project
	//$db->delete( "project_employees", array( "employee_id" => $id, "project_id" => $project_id ) );
	$db->query( "DELETE FROM project_employees WHERE employee_id = $id AND project_id = $project_id" );
		
	header( "Location: project_manage.php?id=".$_REQUEST['project_id'] );
	
?>