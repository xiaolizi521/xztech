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
	WHERE project_task.id = $id" ); 

	$row = mysql_fetch_assoc( $db->result['ref'] );
	$rate = $row['rate'];
	$project_id = $row['project_id'];
	$task_id = $id;
	
	// Include header TEMPLATE
	include( "header_template.php" );

?>

<div style="border-bottom: 3px solid #DDD; padding-bottom: 10px;">
	<h1>Task Information</h1>
	<div class="details">
		<p><strong>Project:</strong> <a href="project_manage.php?id= <?= $row['project_id'] ?>"><?= $row['project'] ?> </a></p>
		<p><strong>Client:</strong> <?= $row['organization'] ?></p>
		<p><strong>Task:</strong> <?= $row['name'] ?></p>
		<? if( strlen( $row['description'] ) > 0 ): ?><p><strong>Description:</strong> <?= $row['description'] ?></p><? endif; ?>
		<? if( strlen( $row['notes'] ) > 0 ): ?><p><strong>Notes:</strong> <?= $row['notes'] ?></p><? endif; ?>
		<? if( $employeeArray['permission'] != "limited" ): ?><p><strong>Rate:</strong> $<?= $row['rate']."/".$row['unit'] ?></p><? endif; ?>
		<p><strong>Status:</strong> <?= status( $row['status'] ) ?></p>
		<? if( $employeeArray['permission'] != "limited" ): ?><p><a href="project_task_edit.php?id=<?= $id ?>&project_id=<?= $row['project_id'] ?>" class="large_link">Edit Task Information &raquo;</a></p><? endif; ?>
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
	project_task_hours.id,
	project_task_hours.timestamp_start,
	project_task_hours.timestamp_end
FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
WHERE project_task.id = $id" );

$total_hours = 0;

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Time</td>
			<td>Hours</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<? $total_hours += $row['hours'] ?>
			<? $amount += $row['hours'] * $row['rate']; ?>
			<tr class="table_row">
				<td><?= date( "m/d/Y", $row['timestamp_start'] ) ?></td>
				<td><?= date( "g:i A", $row['timestamp_start'] ) ?> - <?= date( "g:i A", $row['timestamp_end'] ) ?></td>
				<td><?= $row['hours'] ?> hours</td>			
				<td><?= approved( $row['approved'] ) ?></td>
				<td class="link_button"><? if( $row['approved'] == "0") : ?><a href="project_task_hours_edit.php?id=<?= $row['id'] ?>&project_id=<?= $project_id ?>">View Details</a><? else: ?>&nbsp;<? endif; ?></td>
				</tr>
		<? endwhile; ?>
	</table>
	<p><strong><?= $total_hours ?></strong> Total Hours<? if( $employeeArray['permission'] != "limited" ): ?>( $<?= number_format( $rate * $total_hours ) ?> Earned )<? endif; ?>
<? else: ?>
<p><em>No time has been recorded for this task</em></p>
<? endif; ?>
</div>

<div style="padding-top: 5px; border-top: 3px solid #DDD;">
<? 

$db->query( "SELECT * FROM project_task_clock WHERE task_id = $id" );

if( $db->result['rows'] == 0 ): ?>

<?
$db2 = new db();
$db2->query( "SELECT project_task_clock.id, project_task_clock.task_id
FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
WHERE project_task.employee_id = ".$employeeArray['id'] );
if( $db2->result['rows'] > 0 ):
?>
<p><img src="images/startclockinactive.gif" alt="Inactive Clock"></p>
<p><strong>You are currently recording time for a different task.</strong></p>
<? else: ?>
<p><a href="project_task_clock.php?id=<?= $id ?>" alt="Clock in"><img src="images/startclock.gif" alt="Start Clock"></a></p>
<? endif; ?>

<? else: ?>
<? $row = mysql_fetch_assoc( $db->result['ref'] ); ?>
<?
	$start = $row['timestamp_start'];
	$now = time();
	echo "<p><strong>Clocked in at ".date( "g:i A", $start )." on ".date( "M. jS, Y", $start )."</strong><br>";
	echo "Current time is ".date( "g:i A", $now )." on ".date( "M. jS, Y", $start )."</p>";
	echo "<p>Elapsed time: <strong>".round( floor( ( $now - $start ) / 60 ) / 60, 2 )." hours ( or ".round( ( $now - $start) / 60, 1 )." minutes )</strong></p>";
?>
<p><a href="project_task_clock.php?id=<?= $id ?>" alt="Clock out"><img src="images/stopclock.gif" alt="Start Clock"></a></p>
<? endif; ?>
</div>

<? include( "footer.php" ); ?>