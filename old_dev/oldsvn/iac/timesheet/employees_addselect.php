<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype']; 
	
	if( $p_level != "super-manager" && $p_levelType != 'human resources' )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header.php" );

	// Page specific actions
	$db->get( "employee", array() );
	
	function type( $str )
	{
		switch( $str )
		{
			case( "super-manager" ):
				return( "Super Manager" );
				break;
			case( "manager" ):
				return( "Manager" );
				break;
			case( "employee" ):
				return( "Employee" );
				break;
		}
	}
	
	function manager( $id )
	{
		if( $id == "0" )
			return( "&nbsp;" );
		else
			return( "Manager's Name" );
	}
?>
	
<!-- Start of Employees -->
<div>
<h1>Add Staff</h1>
<p>What kind of staff member needs created?</p>
<p><a href="employees_addsupermanager.php" class="large_link">Super-Manager</a></p>
<p><a href="employees_addemployee.php" class="large_link">Employee</a></p>
<p><a href="employees_addreports.php" class="large_link">Reports Only</a></p>
</div>
<? include( "footer.php" ); ?>