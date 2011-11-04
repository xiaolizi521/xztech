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
	
	$db->query( "DELETE FROM project_task_hours WHERE id = $id");
	
	header( "Location: project_manage.php?id=".$_REQUEST['project_id'] );
	
?>