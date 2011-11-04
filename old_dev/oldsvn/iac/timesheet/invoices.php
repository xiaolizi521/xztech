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
	$db->query( "SELECT client.organization, 
		project.invoice, 
		project.description, 
		project.name, 
		project.client_id,
		project.id
	FROM project INNER JOIN client ON project.client_id = client.id
	WHERE project.invoice != ''" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div>
<h1>Invoices</h1>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Invoice</td>
		<td>Project</td>
		<td>Client</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><a href="invoice_view.php?id=<?= $row['invoice'] ?>">#<?= $row['invoice'] ?></a></td>
		<td><a href="project_manage.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
		<td><a href="client_manage.php?id=<?= $row['client_id'] ?>"><?= $row['organization'] ?></a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>No invoices have been created</em></p>
<? endif; ?>
</div>

<? include( "footer.php" ); ?>