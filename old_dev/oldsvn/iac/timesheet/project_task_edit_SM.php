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
	if( strlen( $_REQUEST['id'] ) > 0 )
		$task_id = $_REQUEST['id'];
	else
		$task_id = $post['id'];
	
	if( strlen( $_REQUEST['project_id'] ) > 0 )
		$project_id = $_REQUEST['project_id'];
	else
		$project_id = $post['project_id'];
	
	// See if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		if( strlen( $post['employee_id'] ) > 0 )
			$db->update( "project_task", array( "employee_id" => $post['employee_id'], "name" => $post['name'], "description" => $post['description'], "notes" => $post['notes'], "rate" => $post['rate'], "status" => $post['status'], "rate_billable" => $post['rate_billable'], "time_limit" => $post['time_limit'], "deadline" => $post['deadline'] ), array( "id" => $post['task_id'] ) );
		else
			$db->update( "project_task", array( "name" => $post['name'], "description" => $post['description'], "notes" => $post['notes'], "rate" => $post['rate'], "status" => $post['status'], "rate_billable" => $post['rate_billable'], "time_limit" => $post['time_limit'], "deadline" => $post['deadline'] ), array( "id" => $post['task_id'] ) );
		
		header( "Location: project_manage.php?id=".$post['project_id'] );
	}
		
	$db->query( "SELECT * FROM project_task WHERE id = $task_id" );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of New Project Task -->
<div>
<h1>Edit Task Information</h1>
<form action="project_task_edit.php" method="post">
<input type="hidden" name="project_id" value="<?= $project_id ?>">
<input type="hidden" name="task_id" value="<?= $task_id ?>">
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="35" value="<?= $row['name'] ?>"></td>
	</tr>
	<tr>
		<td valign="top">Description:</td>
		<td><textarea cols="60" rows="6" name="description"><?= stripslashes( $row['description'] ) ?></textarea></td>
	</tr>
	<tr>
		<td valign="top">Notes:</td>
		<td><textarea cols="60" rows="6" name="notes"><?= stripslashes( $row['notes'] ) ?></textarea></td>
	</tr>
	<tr>
		<td>Rate:</td>
		<td>$ <input type="text" name="rate" size="6" value="<?= $row['rate'] ?>"> / <?= $row['unit'] ?></td>
	</tr>
	<tr>
		<td>Billable Rate:</td>
		<td>$ <input type="text" name="rate_billable" size="6" value="<?= $row['rate_billable'] ?>"> / <?= $row['unit'] ?></td>
	</tr>
	<tr>
		<td>Status:</td>
		<td>
			<select name="status">
				<? foreach( $status as $key => $value ): ?>
				<? if( $key == $row['status'] ): ?>
					<option value="<?= $key ?>" selected="yes"><?= $value ?></option>
				<? else: ?>
					<option value="<?= $key ?>"><?= $value ?></option>
				<? endif; ?>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Time Limit:</td>
		<td>
			<input name="time_limit" type="text" value="<?= $row['time_limit'] ?>">
		</td>
	</tr>
	<tr>
		<td>Deadline:</td>
		<td>
			<input name="deadline" type="text" value="<?= $row['deadline'] ?>">
		</td>
	</tr>
	
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
				WHERE project_task.id = $task_id" );
				
	if( $db->result['rows'] == 0 ):
		$db->query( "SELECT employee.*, project_employees.hidden FROM employee, project_employees, project WHERE employee.id = project_employees.employee_id AND project.id = project_employees.project_id AND project_employees.hidden != 1 AND project.id = $project_id" ); ?>
	<tr>
		<td>Employee:</td>
		<td>
		<select name="employee_id">
		<? while( $employee = mysql_fetch_assoc( $db->result['ref'] ) ):  ?>
			<? if( $row['employee_id'] == $employee['id'] ): ?>
				<option value="<?= $employee['id'] ?>" selected="yes"><?= $employee['name'] ?></option>
			<? else: ?>
				<option value="<?= $employee['id'] ?>"><?= $employee['name'] ?></option>
			<? endif; ?>
		<? endwhile; ?>
		</select>
		</td>
	</tr>
	<? endif; ?>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
<p><a href="project_task_remove.php?id=<?= $task_id ?>&project_id=<?= $project_id ?>" class="large_link">Remove Task &raquo;</a></p>
</div>

<!-- End of New Project Task -->

<? include( "footer.php" ); ?>