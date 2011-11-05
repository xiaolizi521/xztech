<?
	session_start();
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	include( "header_functions.php" );	

	if( $p_level != "super-manager" 		
		&& $p_levelType != 'human resources' )
	{
		header( "Location: home.php" );
		exit();
	}		

	// Start of page-specific actions
	$db->update( "employee", array( "active" => "0" ), array( "id" => $_REQUEST['id'] ) );
	
	// Redirect
	header( "Location: employees.php" );
?>