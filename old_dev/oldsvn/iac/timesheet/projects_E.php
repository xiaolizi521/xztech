<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( $p_level != "employee" )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page specific functions
	$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status FROM project, client WHERE project.client_id = client.id" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div>
<h1>My Projects</h1>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Project</td>
		<td>Client</td>
		<td>Status</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['name'] ?></a></td>
		<td><?= $row['organization'] ?></td>
		<td><?= status( $row['status'] ) ?></td>
		<td class="link_button"><a href="project_manage.php?id=<?= $row['project_id'] ?>">manage</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>No projects have been created</em></p>
<? endif; ?>
<p><a href="projects_completed.php" class="large_link">Completed Projects</a> | <a href="project_new.php" class="large_link">New Project</a></p>
</div>

<? include( "footer.php" ); ?>