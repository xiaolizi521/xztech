<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];

	if( $p_level != "super-manager" 
		&& $p_levelType != 'manager'
		&& $p_levelType != 'human resources' )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page specific functions
	$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status, project_categories.color_light, project_categories.color_text, project_categories.name as category_name FROM project, client LEFT JOIN project_categories ON project_categories.id = project.category WHERE project.client_id = client.id AND project.status != 'completed' ORDER BY project_categories.priority DESC, project.name ASC" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div>
<h1>Active Projects</h1>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>Project</td>
		<td>Client</td>
		<td>Status</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td <?= categoryStyle( $row['color_light'] ) ?>><span style="color: #<?= $row['color_text'] ?>; font-weight: bold; font-size: 0.7em;"><?= $row['category_name'] ?></span>&nbsp;</td>
		<td <?= categoryStyle( $row['color_light'] ) ?>><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['name'] ?></a></td>
		<td <?= categoryStyle( $row['color_light'] ) ?>><a href="client_manage.php?id=<?= $row['client_id'] ?>"><?= $row['organization'] ?></a></td>
		<td nowrap <?= categoryStyle( $row['color_light'] ) ?>><?= status( $row['status'] ) ?></td>
		<td nowrap class="link_button" <?= categoryStyle( $row['color_light'] ) ?>><a href="project_manage.php?id=<?= $row['project_id'] ?>">View Details</a> | <a href="project_edit.php?id=<?= $row['project_id'] ?>&ret=projects">Edit Project</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There are no active projects</em></p>
<? endif; ?>
<p><a href="project_completed.php" class="large_link">Completed Projects</a> | <a href="project_new.php" class="large_link">New Project</a></p>
</div>

<? include( "footer.php" ); ?>