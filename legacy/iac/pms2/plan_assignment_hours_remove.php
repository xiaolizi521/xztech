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
	$plan_id = $_REQUEST['plan_id'];
	
	$db->query( "DELETE FROM plan_assignment_hours WHERE id = $id");
	
	header( "Location: plan_manage.php?id=".$_REQUEST['plan_id'] );
	
?>