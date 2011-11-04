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

	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div>
<h1>Recent Employee Activity</h1>
<?
$days = 1.5;
$time = mktime() - 60*60*24*$days;
//$db->debug = true;
$db->query( "SELECT employee.id,
  employee.name,
	plan_assignment_hours.hours,
	plan_assignment_hours.date,
	plan_assignment.rate,
	plan_assignment.id as plan_assignment_id,
	plan_assignment.plan_id as plan_id,
	plan_assignment.name as assignment_name,
	plan_assignment_hours.id as plan_assignment_hours_id,
	plan_assignment_hours.approved,
	plan_assignment_hours.fb_import,
	plan_assignment_hours.notes,
	plan_assignment_hours.manual_entry_date,
	plan_assignment_hours.original_hours,
  client.organization,
  client.id as client_id,
  plan_classification.name as plan_name
  FROM plan_assignment
  INNER JOIN plan_assignment_hours ON plan_assignment_hours.plan_assignment_id = plan_assignment.id
  INNER JOIN employee ON employee.id = plan_assignment_hours.employee_id
  INNER JOIN client_plan ON client_plan.id = plan_assignment.plan_id
  INNER JOIN plan_classification ON client_plan.classification_id = plan_classification.id
  INNER JOIN client ON client.id = client_plan.client_id
WHERE plan_assignment_hours.approved = 0 OR plan_assignment_hours.timestamp_start > $time
ORDER BY plan_assignment_hours.approved ASC, plan_assignment_hours.timestamp_start DESC" );

$amount = 0;

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Employee</td>
			<td>Assignment</td>
			<td>Client</td>
			<td>Hours</td>
			<td>Status</td>
			<td colspan="2">&nbsp;</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<? $row['return'] = "home.php"; ?>
		<? $total_hours += $row['hours'] ?>
		<? $amount += $row['hours'] * $row['rate']; ?>
		<tr class="table_row">
			<td valign="top"><?= $row['date'] ?></td>
			<td valign="top"><?= $row['name'] ?></td>
			<td valign="top"><a href="plan_assignment.php?id=<?= $row['plan_assignment_id'] ?>"><?= $row['assignment_name'] ?></a></td>
			<td valign="top"><a href="client_manage.php?id=<?= $row['client_id'] ?>"><?= $row['organization'] ?>(<?= $row['plan_name']?>)</a></td>
			<? if( $row['manual_entry_date'] > 0 ): ?>
				<td valign="top" class="modified"><?= $row['hours'] ?> (<?= $row['original_hours']?>)</td>	
			<? else: ?>
				<td valign="top"><?= $row['hours'] ?></td>
			<? endif; ?>
			<td valign="top" nowrap><?= approved( $row['approved'] ) ?></td>
			<td valign="top" class="link_button" nowrap>
				<a href="plan_assignment_hours_edit.php?id=<?= $row['plan_assignment_hours_id'] ?>&plan_id=<?= $row['plan_id'] ?>&return=<?= base64_encode( "home.php" ) ?>">Edit</a>
				<? if( $row['approved'] == "0" ): ?> | <a href="plan_assignment_hours_approve.php?id=<?= $row['plan_assignment_hours_id'] ?>&plan_id=<?= $row['plan_id'] ?>&ret=home">Approve</a><? endif; ?>
				<? if( $row['fb_import'] == "0" ): ?> | <a href="plan_assignment_hours_import.php?hours_id=<?= $row['plan_assignment_hours_id'] ?>&return=<?= base64_encode( "home.php" ) ?>">Import</a><? endif; ?>
			</td>
		</tr>
	<? endwhile; ?>
		<tr class="table_footer">
			<td colspan="4" align="right">Total:</td>
			<td colspan="3"><?= $total_hours ?></td>
		</tr>
	</table>
<? else: ?>
<p><em>No employees have recently recorded time for projects</em></p>
<? endif; ?>
<a href="#" onclick="popUp('ajaxtimer.php');"><img src="images/timer.jpg" style="margin: 10px 0 0 0;"></a>
</div>

<div style="padding-top: 15px;">
<h1>Employee Activity</h1>
<?

// Combine traditional and AJAX timers to one result array
$activeClocksArray = array();

// -----------------------------------
// Active traditional clocks
// -----------------------------------
$db->query( "SELECT plan_assignment_clock.plan_assignment_id, 
	plan_assignment_clock.timestamp_start, 
	employee.id AS employee_id, 
	employee.name AS employee_name, 
	plan_assignment.name AS assignment_name, 
	plan_assignment.id AS assignment_id
FROM plan_assignment_clock INNER JOIN plan_assignment ON plan_assignment_clock.plan_assignment_id = plan_assignment.id
	 INNER JOIN employee ON plan_assignment_clock.employee_id = employee.id" );

$now = mktime();

if( $db->result['rows'] > 0 )
{
	while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
	{			
		$array = array( "timestamp" => $row['timestamp_start'],
		 				"employee_name" => $row['employee_name'],
						"assignment_name" => $row['assignment_name'],
						"assignment_id" => $row['assignment_id'],
						"hours" => round( floor( ( $now - $row['timestamp_start'] ) / 60 ) / 60, 2 ),
						"type" => "Standard Timer" );
		
		array_push( $activeClocksArray, $array );
	}
}

// -----------------------------------
// Active AJAX Timers
// -----------------------------------

// Delete timer entries over 5 minutes
$expiredTime = $now - (60 * 5);

$db->query( "DELETE FROM project_timer WHERE `timestamp` < $expiredTime" );

$db->query( "SELECT project_timer.task_id, project_timer.task_name, project_timer.employee_id, employee.name AS employee_name, project_timer.hours FROM project_timer LEFT JOIN employee ON employee.id = project_timer.employee_id" );

if( $db->result['rows'] > 0 )
{
	while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
	{
		if( strlen( $row['task_name'] ) == 0 )
			$row['task_name'] = "<em>No Task Selected</em>";
			
		$array = array( "timestamp" => $now - ( 3600 * $row['hours'] ),
		 				"employee_name" => $row['employee_name'],
						"task_name" => $row['task_name'],
						"task_id" => $row['task_id'],
						"hours" => $row['hours'],
						"type" => "Pop-Up Timer" );
						
		array_push( $activeClocksArray, $array );
	}
}

?>
<? if( count( $activeClocksArray ) > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Started</td>
			<td>Employee</td>
			<td>Task</td>
			<td>Hours</td>
			<td>Timer Type</td>
		</tr>
	<? foreach( $activeClocksArray as $row ): ?>
		<tr class="table_row">
			<td><?= tsDate( "m/d/Y", $row['timestamp'] ) ?></td>
			<td><?= tsDate( "g:i A", $row['timestamp'] ) ?></td>
			<td><?= $row['employee_name'] ?></td>
			<td><?= $row['task_name'] ?></td>
			<td><?= $row['hours']; ?></td>
			<td><?= $row['type'] ?></td>		
		</tr>
	<? endforeach; ?>
	</table>
<? else: ?>
<p><em>No employees are currently working on projects</em></p>
<? endif; ?>
</div>



<? include( "footer.php" ); ?>