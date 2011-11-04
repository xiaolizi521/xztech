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
	$id = $_REQUEST['id'];
	$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status, project.description, project.deadline FROM project, client WHERE project.client_id = client.id AND project.id = $id" );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div style="border-bottom: 3px solid #DDD; padding-bottom: 10px;"><h1>Project Information</h1>
	<div class="details">
		<p><strong>Project:</strong> <?= $row['name'] ?></p>
	<p><strong>Description:</strong> <?= stripslashes( str_replace( "\n", "<br>", $row['description'] ) ) ?></p>
		<p><strong>Client:</strong> <?= $row['organization'] ?></p>
		<p><strong>Status:</strong> <?= status( $row['status'] ) ?></p>
	</div>
</div>

<div style="padding-top: 15px;">
<h1>Current Tasks</h1>
<?
$db->query( "SELECT project_task.*, employee.name as employee_name FROM employee, project_task WHERE project_task.employee_id = employee.id AND project_task.project_id = $id AND status != 'completed' AND employee.username = '".$_SESSION['username']."'" );
?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>&nbsp;</td>
			<td>Employee</td>
			<td>Name</td>
			<td>Rate</td>
			<td>Time Limit</td>
			<td>Deadline</td>
			<td>Status</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<tr class="table_row">
			<td class="link_button"><a href="project_task.php?id=<?= $row['id'] ?>">Details</a></td>
			<td><?= $row['employee_name'] ?></td>
			<td><?= $row['name'] ?></td>
			<td>$<?= $row['rate'] ?>/<?= $row['unit'] ?></td>
			<td><? if( $row['time_limit'] > 0 ) echo $row['time_limit']; else echo "-" ?></td>
			<td><? if( strlen( $row['deadline'] ) > 0 ) echo $row['deadline']; else echo "-" ?></td>
			<td><?= status( $row['status'] ) ?></td>
		</tr>
	<? endwhile; ?>
	</table>
<? else: ?>
<p><em>No tasks have been added for this project</em></p>
<? endif; ?>
</div>

<?
//$db->query( "SELECT * FROM messages
//	LEFT OUTER JOIN project_task ON messages.task_id = project_task.id
//	LEFT OUTER JOIN project ON project_task.project_id = project.id
//	INNER JOIN project_employees ON messages.project_id = project_employees.project_id
//WHERE project_employees.employee_id = ".$employeeArray['id']." AND project_employees.project_id = $id
//ORDER BY messages.date DESC" );
$db->query( "SELECT * FROM messages
		LEFT OUTER JOIN project ON messages.project_id = project.id
		LEFT OUTER JOIN message_recipients ON messages.id = message_recipients.message_id
		WHERE message_recipients.employee_id = ".$employeeArray['id']."		
		ORDER BY messages.date DESC" );
?>
<div style="padding-top: 15px;">
<h1>Messages</h1>
<? if( isset( $_REQUEST['success'] ) ): ?>
<p class="color_blue">Message sent successfully</p>
<? endif; ?>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>From</td>
		<td>Task</td>
		<td>Sent</td>
		<td>Attachment</td>
		<td>&nbsp;</td>
	</tr>
	<? for( $i = 0; $i < $db->result['rows']; $i++ ): ?>
	<? $row = mysql_fetch_assoc_join( $db->result['ref'] ); ?>
	<!-- <? var_dump( $row ) ?> -->
	<tr>
		<td nowrap><img src="images/misc_icons/email-<?= $row['messages.priority'] ?>.gif"></td>
		<td nowrap><strong><?= $row['messages.sent_by_name'] ?></strong></td>
		<td><a href="project_task.php?id=<?= $row['project_task.id'] ?>"><?= $row['project_task.name'] ?></a></td>
		<td nowrap><?= date( "g:i A", $row['messages.date'] ) ?> on <?= date( "m/d/y", $row['messages.date'] ) ?></td>
		<td nowrap><? if( strlen( $row['messages.filename'] ) > 0 ): ?> <a href="./uploads/<?= $row['messages.filename']?>"><img src="images/misc_icons/file.gif"> Download</a><? endif; ?> &nbsp;</td>
		<td nowrap><a href="message_delete.php?id=<?= $row['messages.id'] ?>"><img src="images/misc_icons/email-delete.gif"> Delete</a></td>
	</tr>
	<tr class="email_end">
		<td>&nbsp;</td>
		<td colspan="6"><?= stripslashes( $row['messages.message'] ) ?>&nbsp;</td>
	</tr>
	<? endfor; ?>
</table>
<? else: ?>
<p><em>There are no messages</em></p>
<? endif; ?>
<p><a href="message_new.php" class="large_link">Compose Message &raquo;</a></p>
</div>
	
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
	 INNER JOIN project_employees ON files.project_id = project_employees.project_id
WHERE files.size > 10 AND project_employees.employee_id = ".$employeeArray['id']." AND project.id = ".$id."
ORDER BY files.date DESC" );
?>
<div style="padding-top: 15px;">
<h1>Files</h1>
<? if( isset( $_REQUEST['success'] ) ): ?>
<p class="color_blue">File uploaded successfully</p>
<? endif; ?>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table" width="100%">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>Filename</td>
		<td>Name</td>
		<td>Created</td>
		<td>Owner</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><img src="images/file_icons/<?= fileTypeImage( $row['mime'] ) ?>"></td>
		<td><a href="./uploads/<?= $row['filename']?>"><?= $row['filename'] ?></a></td>
		<td><a href="./uploads/<?= $row['filename']?>"><?= $row['name'] ?>&nbsp;</a></td>
		<td nowrap><?= date( "m/d/y", $row['date'] ) ?> at <?= date( "g:i:s A", $row['date'] ) ?></td>
		<td><?= $row['uploaded_by_name'] ?>&nbsp;</td>
		<td nowrap><a href="file_delete.php?id=<?= $row['id'] ?>"><img src="images/misc_icons/email-delete.gif"> Delete</a></td>
		<td nowrap><a href="file_details.php?id=<?= $row['id'] ?>" class="lbOn"><img src="images/misc_icons/details.gif"> Details</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There are no files</em></p>
<? endif; ?>
<p><a href="file_new.php" class="large_link">Upload File &raquo;</a></p>
</div>

<div style="padding-top: 15px;">
<h1>Completed Tasks</h1>
<?
$db->query( "SELECT project_task.*, employee.name as employee_name FROM employee, project_task WHERE project_task.employee_id = employee.id AND project_task.project_id = $id AND status = 'completed' AND employee.id = ".$employeeArray['id'] );
?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>&nbsp;</td>
			<td>Employee</td>
			<td>Name</td>
			<td>Description</td>
			<td>Rate</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<tr class="table_row">
			<td class="link_button"><a href="project_task.php?id=<?= $row['id'] ?>">Details</a></td>
			<td><?= $row['employee_name'] ?></td>
			<td><?= $row['name'] ?></td>
			<td><?= strlen( $row['description'] ) > 0 ? substr( $row['description'], 0, 20 )."..." : "&nbsp;" ?></td>			
			<td>$<?= $row['rate'] ?>/<?= $row['unit'] ?></td>
		</tr>
	<? endwhile; ?>
	</table>
<? else: ?>
<p><em>No tasks have been completed for this project</em></p>
<? endif; ?>
</div>

<? include( "footer.php" ); ?>