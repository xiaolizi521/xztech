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
	if( $_REQUEST['id'] != "" )
		$id = $_REQUEST['id'];
	else
		$id = $post['id'];
	
	// Get ID of logged in user
	$db->get( "employee", array( "username" => $_SESSION['username'], "password" => $_SESSION['password'] ) );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	$employee_id = $row['id'];
	
	if( $db->result['rows'] != 1 )
		header( "Location: projects.php" );
		
	$where = array( "id" => $id );
	$set = array( "approved" => 1, "approved_by" => $employee_id );
	
	$db->update( "project_task_hours", $set, $where );
	
	$ret = $_REQUEST['ret'];
	if( $ret == "home" )
		header( "Location: home.php" );
	else
		header( "Location: project_manage.php?id=".$_REQUEST['project_id'] );
	
?>