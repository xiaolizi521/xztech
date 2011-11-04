<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];

	if( $p_level != "super-manager" 
		&& $p_levelType != 'manager'
		&& $p_levelType != 'human resources' )
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
	
	$db2->update( "project_employees", array( "hidden" => $_REQUEST['hidden'] ), array( "employee_id" => $id, "project_id" => $project_id ) );
	
	header( "Location: project_manage.php?id=".$_REQUEST['project_id'] );
	
?>