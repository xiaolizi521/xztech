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
	$plan_id = $_REQUEST['id'];
	$emp_id = $_REQUEST['emp_id'];
	$plan_assignment_id = $_REQUEST['assign_id'];
	
	// Get list of tasks assigned to employee
	//$db->get( "project_task", array( "employee_id" => $id, "project_id" => $project_id ) );
	
	$db2 = new db();	
	//$db2->debug = true;
	$db2->update( "plan_assignment_employees", array( "hidden" => $_REQUEST['hidden'] ), array( "employee_id" => $emp_id, "plan_assignment_id" => $plan_assignment_id ) );
	
	header( "Location: plan_manage.php?id=".$plan_id );
	
?>