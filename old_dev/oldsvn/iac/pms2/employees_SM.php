<? $page = "super-manager"; ?>
<? include( "header.php" ); ?>

<?

	$db->query( "SELECT * FROM employee WHERE active = 1" );
	
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
			case( "reports" ):
				return( "Reports Only" );
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
<h1>Employees</h1>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Name</td>
		<td>Username</td>
		<td>Email</td>
		<td>Type</td>
		<td>Permissions</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><a href="employees_edit.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
		<td><?= $row['username'] ?></td>
		<td><?= $row['email'] ?></td>
		<td><?= type( $row['type'] ) ?></td>
		<td><?= $row['permission'] ?></td>
		<td class="link_button"><a href="employees_edit.php?id=<?= $row['id'] ?>">Edit Employee</a></td>
	</tr>
	<? endwhile; ?>
</table>
</div>
<!-- End of Employees -->

<div style="padding-top: 15px;">
	<p><a href="employees_deactivated.php" class="large_link">Deactivated Employees</a> | <a href="employees_addselect.php" class="large_link">Add Employee &raquo;</a></p>
</div>
<? include( "footer.php" ); ?>