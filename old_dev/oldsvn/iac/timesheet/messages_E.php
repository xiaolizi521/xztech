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
	//$db->query( "SELECT * FROM messages
	//	LEFT OUTER JOIN project_task ON messages.task_id = project_task.id
	//	LEFT OUTER JOIN project ON project_task.project_id = project.id
	//	INNER JOIN project_employees ON messages.project_id = project_employees.project_id
	//WHERE project_employees.employee_id = ".$employeeArray['id']."
	//ORDER BY messages.date DESC" );
	
	//$db->query( "SELECT * FROM messages
	//	LEFT OUTER JOIN project_task ON messages.task_id = project_task.id
	//	LEFT OUTER JOIN project ON project_task.project_id = project.id
	//	LEFT OUTER JOIN message_recipients ON messages.id = message_recipients.message_id
	//	INNER JOIN project_employees ON messages.project_id = project_employees.project_id
	//	WHERE project_employees.employee_id = ".$employeeArray['id']."
	//	AND message_recipients.employee_id = ".$employeeArray['id']."
	//	ORDER BY messages.date DESC" );
	
	$db->query( "SELECT * FROM messages
		LEFT OUTER JOIN project ON messages.project_id = project.id
		LEFT OUTER JOIN message_recipients ON messages.id = message_recipients.message_id
		WHERE message_recipients.employee_id = ".$employeeArray['id']."		
		ORDER BY messages.date DESC" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>

<div>
<h1>Messages</h1>
<? if( isset( $_REQUEST['success'] ) ): ?>
<p class="color_blue">Message sent successfully</p>
<? endif; ?>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>From</td>
		<td>Project</td>
		
		<td>Sent</td>
		<td>Attachment</td>
		<td>&nbsp;</td>
	</tr>
	<? for( $i = 0; $i < $db->result['rows']; $i++ ): ?>
	<? $row = mysql_fetch_assoc_join( $db->result['ref'] ); ?>
	
	<tr>
		<td nowrap><img src="images/misc_icons/email-<?= $row['messages.priority'] ?>.gif"></td>
		<td nowrap><strong><?= $row['messages.sent_by_name'] ?></strong></td>
		<td>
			<? if ( $row['messages.project_id'] > 0 ) { ?>
				<a href="project_manage.php?id=<?= $row['project.id'] ?>"><?= $row['project.name'] ?></a>
			<? }else{?>
				No Project Selected
			<? }?>
		</td>
		<td nowrap><?= date( "g:i A", $row['messages.date'] ) ?> on <?= date( "m/d/y", $row['messages.date'] ) ?></td>
		<td nowrap><? if( $row['messages.file_id'] > 0 ): ?> <a href="./uploads/<?= $row['messages.filename']?>"><img src="images/misc_icons/file.gif"> Download</a><? endif; ?> &nbsp;</td>
		<td nowrap><a href="message_delete.php?id=<?= $row['message_recipients.id'] ?>"><img src="images/misc_icons/email-delete.gif"> Delete</a></td>
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


<? include( "footer.php" ); ?>