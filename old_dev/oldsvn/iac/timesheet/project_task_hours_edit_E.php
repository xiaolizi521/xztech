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
	if( $_REQUEST['id'] != "" )
		$id = $_REQUEST['id'];
	else
		$id = $post['id'];

	if( $_REQUEST['project_id'] != "" )
		$project_id = $_REQUEST['project_id'];
	else
		$project_id = $post['project_id'];
		
	// Check to see if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		$db->update( "project_task_hours", array( "notes" => $post['notes'] ),	array( "id" => $id ) );
		header( "Location: project_manage.php?id=".$project_id );
	}

	$db->query( "SELECT project_task_hours.hours, 
		project_task.name as task_name,
		project_task_hours.notes, 
		project_task_hours.approved, 
		employee.name, 
		project_task.id as task_id,
		project_task_hours.id as hours_id
	FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
		 INNER JOIN employee ON project_task.employee_id = employee.id
		WHERE project_task_hours.id = $id"); 

	$row = mysql_fetch_assoc( $db->result['ref'] );	
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of Adjust Hours -->
<div>
<h1>Add Task Notes</h1>
<form action="project_task_hours_edit.php" method="post">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="project_id" value="<?= $project_id ?>">

<table cellpadding="5">
	<tr>
		<td>Task:</td>
		<td><?= $row['task_name'] ?></td>
	</tr>
	<tr>
		<td>Employee:</td>
		<td><?= $row['name'] ?></td>
	</tr>
	<tr>
		<td>Status:</td>
		<td>
			<?= approved( $row['approved'] ) ?>
		</td>
	</tr>
	<tr>
		<td valign="top">Time:</td>
		<td style="background: #EEE;">
			<?= $row['hours']; ?> hours
		</td>
	<tr>
		<td valign="top">Notes:</td>
		<td><textarea name="notes" cols="60" rows="5"><?= $row['notes'] ?></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>
<!-- End of Adjust Hours -->

<? include( "footer.php" ); ?>