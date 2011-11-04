<?
	session_start();
		
	include( "header_functions.php" );
	
	if( $p_level != "super-manager" )
	{
		header( "Location: home.php" );
		exit();
	}

	// Page specific functions
	$id = $_REQUEST['id'];
	$project_id = $_REQUEST['project_id'];
	
	$db->query( "DELETE FROM project_task_hours WHERE task_id = $id");
	$db->query( "DELETE FROM project_task_clock WHERE task_id = $id");
	$db->query( "DELETE FROM project_task WHERE id = $id");
	
	header( "Location: project_manage.php?id=".$_REQUEST['project_id'] );
	
?>