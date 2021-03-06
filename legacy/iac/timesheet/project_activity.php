<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];

	if( $p_level != "super-manager" 
		&& $p_levelType != "manager")
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page specific functions
	$id = $_REQUEST['id'];
	$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status, project.description FROM project, client WHERE project.client_id = client.id AND project.id = $id" );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div style="border-bottom: 3px solid #DDD; padding-bottom: 10px;"><h1>Project Information</h1>
	<div style="padding: 0 10px 0 10px; background: #EEE; border: 2px solid #DDD;">
		<p><strong>Project:</strong> <a href="project_manage.php?id=<?= $id ?>"><?= $row['name'] ?> (<?= $row['description'] ?>)</a></p>
		<p><strong>Client:</strong> <?= $row['organization'] ?></p>
		<p><strong>Status:</strong> <?= status( $row['status'] ) ?></p>
		<p><a href="project_edit.php?id=<?= $id ?>" class="large_link">Edit Project Information &raquo;</a></p>
	</div>
</div>


<div style="padding-top: 15px;">
<h1>Employee Activity</h1>
<?
$db->query( "SELECT employee.id, 
	employee.name, 
	project_task_hours.hours, 
	project_task_hours.date,
	project_task.rate,
	project_task.id as task_id,
	project_task.name as task_name,
	project_task_hours.id as project_task_hours_id,
	project_task_hours.approved
FROM project_task INNER JOIN employee ON project_task.employee_id = employee.id
	 INNER JOIN project_task_hours ON project_task_hours.project_task_id = project_task.id
WHERE project_task.project_id = $id
ORDER BY project_task_hours.approved ASC" );

$amount = 0;

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Employee</td>
			<td>Task</td>
			<td>Hours</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<? $total_hours += $row['hours'] ?>
		<? $amount += $row['hours'] * $row['rate']; ?>
		<tr class="table_row">
			<td><?= $row['date'] ?></td>
			<td><?= $row['name'] ?></td>
			<td><a href="project_task.php?id=<?= $row['task_id'] ?>"><?= $row['task_name'] ?></a></td>
			<td><?= $row['hours'] ?></td>			
			<td><?= approved( $row['approved'] ) ?></td>
			<td class="link_button"><a href="project_task_hours_edit.php?id=<?= $row['project_task_hours_id'] ?>&project_id=<?= $id ?>">Adjust</a>
			<? if( $row['approved'] == "0" ): ?> | <a href="project_task_hours_approve.php?id=<?= $row['project_task_hours_id'] ?>&project_id=<?= $id ?>">Approve Hours</a><? endif; ?></td>
		</tr>
	<? endwhile; ?>
	</table>
	<p><strong><?= $total_hours ?></strong> Total Hours ( $<?= number_format( $amount ) ?> Earned )
	<p><a href="project_manage.php?id=<?= $id ?>" class="large_link">&laquo; Back to Project</a></p>
<? else: ?>
<p><em>No employees have recorded time for this project</em></p>
<? endif; ?>
</div>

<? include( "footer.php" ); ?>