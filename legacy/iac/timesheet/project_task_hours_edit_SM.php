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
		if( $post['approved'] == 1 || $post['hours'] > 0 )
		{				
			// Get ID of logged in user
			$db->get( "employee", array( "username" => $_SESSION['username'], "password" => $_SESSION['password'] ) );
			$row = mysql_fetch_assoc( $db->result['ref'] );
			$employee_id = $row['id'];

			if( $db->result['rows'] != 1 )
				header( "Location: projects.php" );

			$where = array( "id" => $id );
			
			$db->get( "project_task_hours", array( "id" => $id ) );
			$temp = mysql_fetch_assoc( $db->result['ref'] );
			
			// If the hour total is the same of if the time has previously changed
			if( $temp['hours'] == $post['hours'] || $temp['manual_entry_date'] > 0 )
			{
				$set = array( "approved" => $post['approved'], "approved_by" => $employee_id, "hours" => $post['hours'], "notes" => $post['notes'] );
			}
			else	// The time has changed for the first time, mark it
			{
				$set = array( "approved" => $post['approved'], "approved_by" => $employee_id, "hours" => $post['hours'], "original_hours" => $temp['hours'], "manual_entry_date" => mktime(), "notes" => $post['notes'] );
			}
			
			$db->update( "project_task_hours", $set, $where );

			header( "Location: project_manage.php?id=".$_REQUEST['project_id'] );
		}
		else
		{
			$db->update( "project_task_hours", array( "hours" => $post['hours'], "notes" => $post['notes'], "approved" => "0" ),	array( "id" => $id ) );
			header( "Location: project_manage.php?id=".$project_id );
		}
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
<h1>Adjust Task Hours</h1>
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
		<td>Approved:</td>
		<td>
			<select name="approved">
			<?	if( $row['approved'] == 1 ): ?>
				<option value="1" selected="yes">Yes</option>
				<option value="0">No</option>
			<? else: ?>
				<option value="1">Yes</option>
				<option value="0" selected="yes">No</option>
			<? endif; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top">Time:</td>
		<td style="background: #EEE;">
			<input type="text" size="6" name="hours" value="<?= $row['hours'] ?>"> hours
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

<div style="padding-top: 15px;">
	<p><a href="project_task_hours_remove.php?id=<?= $id ?>&project_id=<?= $project_id ?>" class="large_link">Remove Hours from Task &raquo;</a></p>
</div>
<!-- End of Adjust Hours -->

<? include( "footer.php" ); ?>