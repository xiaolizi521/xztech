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
	$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status, project.invoice FROM project, client WHERE project.client_id = client.id AND project.status = 'completed'" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div>
<h1>Completed Projects</h1>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Project</td>
		<td>Client</td>
		<td>Invoice</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['name'] ?></a></td>
		<td><a href="client_manage.php?id=<?= $row['client_id'] ?>"><?= $row['organization'] ?></a></td>
		<td class="link_button">
			<? if( $row['invoice'] > 0 ): ?>
			<a href="invoice_view.php?id=<?= $row['invoice'] ?>">#<?= $row['invoice'] ?></a>
			<? else: ?>
			<a href="invoice_preview.php?id=<?= $row['project_id'] ?>">Create Invoice</a>
			<? endif; ?>
		</td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>No projects have been completed</em></p>
<? endif; ?>
<p><a href="projects.php" class="large_link">Active Projects</a></p>
</div>

<? include( "footer.php" ); ?>