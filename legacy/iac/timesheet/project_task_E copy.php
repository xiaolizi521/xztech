<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( $p_level != "employee" )
		header( "Location: home.php" );
	else
		include( "header_functions.php" );

	// Page specific functions
	$id = $_REQUEST['id'];

	$db->query( "SELECT project_task.name, 
		project_task.description, 
		project_task.notes, 
		project_task.status, 
		project_task.unit, 
		project_task.rate, 
		client.organization,
		employee.name as employee_name,
		employee.id as employee_id,
		client.id as client_id,
		project.name as project,
		project.id as project_id
	FROM project_task INNER JOIN employee ON project_task.employee_id = employee.id
		 INNER JOIN project ON project_task.project_id = project.id
		 INNER JOIN client ON project.client_id = client.id
	WHERE project_task.id = $id"); 

	$row = mysql_fetch_assoc( $db->result['ref'] );
	$rate = $row['rate'];
	$project_id = $row['project_id'];
	$task_id = $id;
	
	// Include header TEMPLATE
	include( "header_template.php" );

?>

<div style="border-bottom: 3px solid #DDD; padding-bottom: 10px;">
	<h1>Task Information</h1>
	<div style="padding: 0 10px 0 10px; background: #EEE; border: 2px solid #DDD;">
		<p><strong>Project:</strong> <a href="project_manage.php?id= <?= $row['project_id'] ?>"><?= $row['project'] ?> </a></p>
		<p><strong>Client:</strong> <?= $row['organization'] ?></p>
		<p><strong>Task:</strong> <?= $row['name'] ?></p>
		<? if( strlen( $row['description'] ) > 0 ): ?><p><strong>Description:</strong> <?= $row['description'] ?></p><? endif; ?>
		<? if( strlen( $row['notes'] ) > 0 ): ?><p><strong>Notes:</strong> <?= $row['notes'] ?></p><? endif; ?>
		<p><strong>Rate:</strong> $<?= $row['rate']."/".$row['unit'] ?></p>
		<p><strong>Status:</strong> <?= status( $row['status'] ) ?></p>
		<p><a href="project_task_edit.php?id=<?= $id ?>&project_id=<?= $row['project_id'] ?>" class="large_link">Edit Task Information &raquo;</a></p>
	</div>
</div>

<div style="padding-top: 15px;">
<h1>Time Sheet</h1>
<?

$db->query( "SELECT project_task_hours.hours, 
	project_task_hours.notes, 
	project_task_hours.date, 
	project_task_hours.approved, 
	project_task_hours.approved_date, 
	project_task_hours.approved_by, 
	project_task_hours.id
FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
WHERE project_task.id = $id" );

$total_hours = 0;

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Hours</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<? $total_hours += $row['hours'] ?>
			<? $amount += $row['hours'] * $row['rate']; ?>
			<tr class="table_row">
				<td><?= $row['date'] ?></td>
				<td><?= $row['hours'] ?></td>			
				<td><?= approved( $row['approved'] ) ?></td>
				<td class="link_button"><? if( $row['approved'] == "0") : ?><a href="project_task_hours_edit.php?id=<?= $row['id'] ?>&project_id=<?= $project_id ?>">Adjust</a><? else: ?>&nbsp;<? endif; ?></td>
				</tr>
		<? endwhile; ?>
	</table>
	<p><strong><?= $total_hours ?></strong> Total Hours ( $<?= number_format( $rate * $total_hours ) ?> Earned )
<? else: ?>
<p><em>No time has been added for this task</em></p>
<? endif; ?>
<p><a href="project_task_hours_add.php?id=<?= $id ?>" class="large_link">Enter Time &raquo;</a></p>
</div>

<? include( "footer.php" ); ?>