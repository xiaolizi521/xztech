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

	// Find clients with 1, those are deleted - mark that
	$db->update( "client", array( "deleted" => "1" ), array( "deleted_check" => "1" ) );
	
	// Finally, set everything back to 0
	$db->updateNoID( "client", array( "deleted_check" => "0" ) );
	
	// Get updated list of clients
	$db->query( "SELECT * FROM client ORDER BY organization" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	$deleted = 0;
?>

<!-- Start of Clients -->
<div>
<h1>Removed Clients</h1>

<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Organization</td>
		<td>Name</td>
		<td>Email</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<? if( $row['deleted'] == "0" ): ?>
	<? $active++; ?>
	<? else: ?>
	<tr class="table_row">
		<td><a href="client_manage.php?id=<?= $row['id'] ?>"><?= $row['organization'] ?></a></td>
		<td><a href="client_manage.php?id=<?= $row['id'] ?>"><?= $row['first_name']." ".$row['last_name'] ?></a></td>
		<td><?= $row['email'] ?></td>
	</tr>
	<? endif; ?>
	<? endwhile; ?>
</table>

</div>
<!-- End of Clients -->

<? if( $active > 0 ): ?>
<div style="padding-top: 15px;">
	<p><a href="clients.php" class="large_link">Active Clients (<?= $active ?>) &raquo;</a></p>
</div>
<? endif; ?>

<? include( "footer.php" ); ?>