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

	if( $_REQUEST['plan_id'] != "" )
		$plan_id = $_REQUEST['plan_id'];
	else
		$plan_id = $post['plan_id'];
		
	$ret = $_REQUEST['ret'];
		
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
				header( "Location: client_activity.php" );

			$where = array( "id" => $id );
			
			$db->get( "plan_assignment_hours", array( "id" => $id ) );
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
			
			$db->update( "plan_assignment_hours", $set, $where );

		}
		else
		{
			$db->update( "plan_assignment_hours", array( "hours" => $post['hours'], "notes" => $post['notes'], "approved" => "0" ),	array( "id" => $id ) );
			//header( "Location: plan_manage.php?id=".$plan_id );
		}
		
		$ret = $post['ret'];
		if( $ret == "home" )
			header( "Location: home.php" );
		else
			header( "Location: plan_manage.php?id=".$post['plan_id'] );
	}
	
	//$db->debug = true;
	$db->query( "SELECT plan_assignment_hours.hours, 
		plan_assignment.name as assignment_name,
		plan_assignment_hours.notes, 
		plan_assignment_hours.approved, 
		employee.name, 
		plan_assignment.id as assignment_id,
		plan_assignment_hours.id as hours_id
	FROM plan_assignment_hours 
	INNER JOIN plan_assignment ON plan_assignment_hours.plan_assignment_id = plan_assignment.id
		INNER JOIN plan_assignment_employees ON plan_assignment_employees.plan_assignment_id = plan_assignment.id AND plan_assignment_employees.employee_id = plan_assignment_hours.employee_id
		 INNER JOIN employee ON plan_assignment_hours.employee_id = employee.id
		WHERE plan_assignment_hours.id = $id"); 

	$row = mysql_fetch_assoc( $db->result['ref'] );	
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of Adjust Hours -->
<div>
<h1>Adjust Assignment Hours</h1>
<form action="plan_assignment_hours_edit.php" method="post">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="plan_id" value="<?= $plan_id ?>">
<input type="hidden" name="ret" value="<?= $ret ?>">

<table cellpadding="5">
	<tr>
		<td>Task:</td>
		<td><?= $row['assignment_name'] ?></td>
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
	<p><a href="plan_assignment_hours_remove.php?id=<?= $id ?>&plan_id=<?= $plan_id ?>" class="large_link">Remove Hours from Assignment &raquo;</a></p>
</div>
<!-- End of Adjust Hours -->

<? include( "footer.php" ); ?>