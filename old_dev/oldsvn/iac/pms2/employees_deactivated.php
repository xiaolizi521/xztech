<?
	session_start();
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	include( "header_functions.php" );	
	if( $p_level != "super-manager" && $p_levelType != 'human resources' )
	{
		header( "Location: home.php" );
		exit();
	}
		
	// Start of page-specific actions
	$db->get( "employee", array( "active" => "0" ) );
	
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
	
	
	// Template
	include( "header_template.php" );
?>
	
<!-- Start of Employees -->
<div>
<h1>Deactivated Employees</h1>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>Name</td>
		<td>Username</td>
		<td>Email</td>
		<td>Access Level</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td class="link_button"><a href="employees_reactivate.php?id=<?= $row['id'] ?>">reactivate</a></td>
		<td><?= $row['name'] ?></td>
		<td><?= $row['username'] ?></td>
		<td><?= $row['email'] ?></td>
		<td><?= type( $row['type'] ) ?></td>
	</tr>
	<? endwhile; ?>
</table>
</div>
<!-- End of Employees -->

<div style="padding-top: 15px;">
	<p><a href="employees.php" class="large_link">Employees &raquo;</a></p>
</div>
<? include( "footer.php" ); ?>