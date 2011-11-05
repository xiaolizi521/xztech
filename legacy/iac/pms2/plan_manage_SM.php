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
	$id = $_REQUEST['id'];
	//$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status, project.description, project.invoice, project.deadline FROM project, client WHERE project.client_id = client.id AND project.id = $id" );
	$sql = "SELECT a.*, c.name as plan_name, b.organization FROM client_plan a
			INNER JOIN client b ON a.client_id = b.id
			INNER JOIN plan_classification c ON a.classification_id = c.id
			INNER JOIN rate_classification d ON a.rate_classification_id = d.id
			where a.id = $id";
	$db->query( $sql );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>
<script language=""javascript">
	function displayHiddenEmployees(){
		document.all['hidden_emps'].style.display = 'block';
	}
</script>

<div style="border-bottom: 3px solid #DDD; padding-bottom: 10px;"><h1>Plan Information</h1>
	<div class="details">
		<p><strong>Plan:</strong> <?= $row['plan_name'] ?></p>
		<p><strong>Client:</strong> <?= $row['organization'] ?></p>
		<p><strong>Status:</strong> <?= status( $row['status'] ) ?></p>
		
		<? if( strpos($row['plan_name'], 'RTN') > 0 ): ?>
			<p><strong>Retained Hours:</strong> <?= $row['retained_hours'] ?></p>
		<? endif; ?>
		
		<? if( strpos($row['plan_name'], 'PRPD') > 0 ): ?>
			<p><strong>Purchased Hours:</strong> <?= $row['purchased_hours'] ?></p>
		<? endif; ?>
		
		<? if( $row['invoice'] > 0 ): ?>
			<p><strong>Invoice:</strong> <a href="invoice_view.php?id=<?= $row['invoice'] ?>">#<?= $row['invoice'] ?></a></p>
		<? elseif( $row['status'] == "completed" ): ?>
			<p><strong>Invoice: </strong> <a href="invoice_preview.php?id=<?= $row['project_id'] ?>">Create Invoice</a>
		<? endif; ?>

		<p><a href="client_plan_edit.php?id=<?= $id ?>" class="large_link">Edit Plan Information &raquo;</a></p>
	</div>
</div>

<? if( !( $row['invoice'] > 0) ): ?>
<div style="padding-top: 15px;">
<h1>Current Assignments</h1>
<?
//$db->debug = true;
$db->query( "SELECT plan_assignment.* FROM plan_assignment WHERE plan_assignment.plan_id = $id AND status != 'completed'" );
?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Name</td>
			<td>Description</td>
			<td>Hourly Rate</td>
			<td>Time Limit</td>
			<td>Deadline</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<tr class="table_row">
			<td><a href="plan_assignment.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
			<td><?= strlen( $row['description'] ) > 0 ? substr( $row['description'], 0, 20 )."..." : "&nbsp;" ?></td>			
			<td>$<?= $row['rate'] ?> ($<?= $row['rate_billable'] ?>)</td>
			<td><? if( $row['time_limit'] > 0 ) echo $row['time_limit']; else echo "-" ?></td>
			<td><? if( strlen( $row['deadline'] ) > 0 ) echo $row['deadline']; else echo "-" ?></td>
			<td><?= status( $row['status'] ) ?></td>
			<td class="link_button"><a href="plan_assignment.php?id=<?= $row['id'] ?>">View Details</a></td>
		</tr>
		
		<!-- Check for employee assignments -->
		<tr class="table_row">
			<td>&nbsp;</td>
			<td colspan="6">
			<table cellpadding="0" cellspacing="0" border="0" class="data_table" width="100%">
			<? //echo "SELECT a.*, b.* FROM plan_assignment_employees a, employee b WHERE a.employee_id = b.id AND a.plan_id = ".$row['id']; ?>
			<? $result = mysql_query("SELECT a.plan_assignment_id, b.name, b.position, b.id as emp_id FROM plan_assignment_employees a, employee b WHERE a.employee_id = b.id AND a.hidden != 1 AND a.plan_assignment_id = ".$row['id']); ?>
			<? while ( $employee = mysql_fetch_assoc($result) ) { ?>
				<tr class="table_row">
					<td><a href="employees_edit.php?id=<?= $employee['emp_id'] ?>"><?= $employee['name'] ?></a></td>
					<td><? if ( $employee['position'] == "" ) echo "&nbsp;"; else echo $employee['position']; ?></td>
					<td class="link_button"><a href="plan_assignment_employee_hide.php?id=<?= $id ?>&hidden=1&emp_id=<?= $employee['emp_id']?>&assign_id=<?= $employee['plan_assignment_id']?>">Hide</a></td>
				</tr>
			<? }?>
			<? if ( mysql_num_rows( $result ) == 0 ) {?>
				<tr class="table_row">
					<td><em>No Employees Assigned</em></td>
				</tr>
			<? }?>
			</table>			 	
			</td>
			
		</tr>
	<? endwhile; ?>
	</table>
<? else: ?>
<p><em>No current assignments have been added for this project</em></p>
<? endif; ?>
<p><a href="plan_assignment_add.php?id=<?= $id ?>" class="large_link">Add Assignment</a></p>
</div>

<?
//$db->query( "SELECT * FROM messages
//	LEFT OUTER JOIN project_task ON messages.task_id = project_task.id
//	LEFT OUTER JOIN project ON project_task.project_id = project.id
//	WHERE project.id = $id
//	ORDER BY messages.date DESC" );
$db->query( "SELECT * FROM messages
		LEFT OUTER JOIN project ON messages.project_id = project.id
		LEFT OUTER JOIN message_recipients ON messages.id = message_recipients.message_id
		WHERE message_recipients.employee_id = ".$employeeArray['id']."		
		ORDER BY messages.date DESC" );
?>



<?
	$db->query( "SELECT project.name as project_name,
		files.filename, 
		files.mime, 
		files.name, 
		files.date, 
		files.id, 
		files.audio_length, 
		files.project_id,
		files.uploaded_by_name
	FROM files INNER JOIN project ON files.project_id = project.id
	WHERE files.size > 10 AND project.id = $id
	ORDER BY files.date DESC" );
	
?>

<div style="padding-top: 10px;">
<h1>Files</h1>
<? if( isset( $_REQUEST['success'] ) ): ?>
<p class="color_blue">File uploaded successfully</p>
<? endif; ?>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table" width="100%">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>File</td>
		<td>Name</td>
		<td>Project</td>
		<td>Created</td>
		<td>Owner</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><img src="images/file_icons/<?= fileTypeImage( $row['mime'] ) ?>"></td>
		<td><a href="./uploads/<?= $row['filename']?>"><?= $row['filename'] ?></a></td>
		<td><a href="./uploads/<?= $row['filename']?>"><?= $row['name'] ?>&nbsp;</a></td>
		<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['project_name'] ?></a></td>
		<td nowrap><?= date( "m/d/y", $row['date'] ) ?> at <?= date( "g:i:s A", $row['date'] ) ?></td>
		<td><?= $row['uploaded_by_name'] ?>&nbsp;</td>
		<td nowrap><a href="file_delete.php?id=<?= $row['id'] ?>"><img src="images/misc_icons/email-delete.gif"> Delete</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There are no files</em></p>
<? endif; ?>
<p><a href="file_new.php" class="large_link">Upload File &raquo;</a></p>
</div>


<div style="padding-top: 15px;">
<h1>Recent Employee Activity</h1>
<?
//$db->debug = true;
$db->query( "SELECT plan_assignment_hours.hours,
	plan_assignment_hours.date,
	plan_assignment.rate,
	plan_assignment.name as assignment_name,
	plan_assignment_hours.id as plan_assignment_hours_id,
	plan_assignment_hours.approved,
  	plan_assignment_hours.employee_id,
  	employee.name
	FROM plan_assignment_hours
  	INNER JOIN plan_assignment ON plan_assignment_hours.plan_assignment_id = plan_assignment.id
  	INNER JOIN employee ON plan_assignment_hours.employee_id = employee.id
	WHERE plan_assignment.plan_id = $id AND plan_assignment_hours.approved = 0" );

$amount = 0;

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Employee</td>
			<td>Assignment</td>
			<td>Hours</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<? $total_hours += $row['hours'] ?>
		<? $amount += $row['hours'] * $row['rate']; ?>
		<? if( $row['approved'] == "0" ): ?>
		<tr class="table_row">
			<td><?= $row['date'] ?></td>
			<td><?= $row['name'] ?></td>
			<td><?= $row['assignment_name'] ?></td>
			<td><?= $row['hours'] ?></td>			
			<td><?= approved( $row['approved'] ) ?></td>
			<? if ( $p_levelType != 'human resources' ){ ?>
			<td class="link_button"><a href="plan_assignment_hours_edit.php?id=<?= $row['plan_assignment_hours_id'] ?>&plan_id=<?= $id ?>">Adjust</a> | <a href="plan_assignment_hours_approve.php?id=<?= $row['plan_assignment_hours_id'] ?>&plan_id=<?= $id ?>">Approve Hours</a></td>
			<? }?>
		</tr>
		<? endif; ?>
	<? endwhile; ?>
	</table>
	<p><strong><?= $total_hours ?></strong> Total Hours ( $<?= number_format( $amount ) ?> Earned )
	<p><a href="plan_activity.php?id=<?= $id ?>" class="large_link">View All Activity &raquo;</a></p>
<? else: ?>
<p><em>No employees have unapproved time for this plan</em></p>
<p><a href="plan_activity.php?id=<?= $id ?>" class="large_link">View All Activity &raquo;</a></p>
<? endif; ?>
</div>

<div style="padding-top: 15px;">
<h1>Active Employees</h1>
<?
$db->query( "SELECT project_task_clock.task_id, 
	project_task_clock.timestamp_start, 
	employee.id AS employee_id, 
	employee.name AS employee_name, 
	project_task.name AS task_name, 
	project_task.id AS task_id
FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
	 INNER JOIN employee ON project_task.employee_id = employee.id
	WHERE project_task.project_id = $id" );

$now = time();
	
?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Started</td>
			<td>Employee</td>
			<td>Task</td>
			<td>Hours</td>
			<td>&nbsp;</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<tr class="table_row">
			<td><?= tsDate( "m/d/Y", $row['timestamp_start'] ) ?></td>
			<td><?= tsDate( "g:i A", $row['timestamp_start'] ) ?></td>
			<td><?= $row['employee_name'] ?></td>
			<td><?= $row['task_name'] ?></td>
			<td><?= round( floor( ( $now - $row['timestamp_start'] ) / 60 ) / 60, 2 ); ?></td>			
			<td class="link_button"><a href="project_task_clock.php?id=<?= $row['task_id']; ?>&ret=pm&pid=<?= $id ?>">Stop Clock</a></td>
		</tr>
	<? endwhile; ?>
	</table>
<? else: ?>
<p><em>No employees are actively working on this project</em></p>
<? endif; ?>
</div>






<? else: ?>



<? endif; ?>


<? include( "footer.php" ); ?>