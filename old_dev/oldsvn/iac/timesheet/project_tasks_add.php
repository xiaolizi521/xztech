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
		
	// See if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		$db->add( "project_task", array( "project_id" => $post['project_id'], "employee_id" => $post['employee_id'], "rate" => $post['rate'], "unit" => $post['unit'], "name" => $post['name'], "description" => $post['description'], "notes" => $post['notes'], "status" => $post['status'], "rate_billable" => $post['rate_billable'], "time_limit" => $post['time_limit'] ) );
		
		header( "Location: project_manage.php?id=".$post['project_id'] );
	}
	
	// Page specific functions
	$id = $_REQUEST['id'];
	$db->query( "SELECT employee.* FROM employee, project_employees WHERE employee.id = project_employees.employee_id AND project_employees.hidden != 1 AND project_employees.project_id = $id" );

	// Include header TEMPLATE
	include( "header_template.php" );
?>

<script type="text/javascript">
<!--
function changeRateNew()
{
	var sel = document.getElementById("employee_id");
	var d = sel.options[sel.selectedIndex].value;
	
	var employees = new Array();
	<? if( $db->result['rows'] > 0 ): ?>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	employees[ <?= $row['id'] ?> ] = "<?= $row['rate'] ?>";
	<? endwhile; ?>
	<? endif; ?>
	<? mysql_data_seek( $db->result['ref'], 0 ); ?>

	document.task.rate.value = employees[ d ];
}
// -->
</script>

<!-- Start of New Project Task -->
<div>
<h1>Add Project Task</h1>
<? if( $db->result['rows'] == 0 ): ?>
<p class="color_red">An employee must be assigned to this project before tasks can be added</p>
<? endif; ?>
<form action="project_tasks_add.php" method="post" name="task">
<input type="hidden" name="project_id" value="<?= $_REQUEST['id'] ?>">
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="35"></td>
	</tr>
	<tr>
		<td valign="top">Description:</td>
		<td><textarea name="description" cols="60" rows="2"></textarea></td>
	</tr>
	<tr>
		<td valign="top">Notes:</td>
		<td><textarea name="notes" cols="60" rows="10"></textarea></td>
	</tr>
	<tr>
		<td>Contractor Rate:</td>
		<td>$ <input type="text" name="rate" size="7"> / <select name="unit"><option value="Hour">Hour</option><option value="Item">Item</option></td>
	</tr>
	<tr>
		<td>Billable Rate:</td>
		<td>$ <input type="text" name="rate_billable" size="7"></td>
	</tr>
	<tr>
		<td>Assign to:</td>
		<td>
			<? if( $db->result['rows'] > 0 ): ?>
			<select name="employee_id" id="employee_id" onchange="changeRateNew();">
			<option value=""></option>
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['id'] ?>" onChange="changeRate('<?= $row['rate'] ?>');"><?= $row['name'] ?> (<?= $row['username'] ?>)</option>
			<? endwhile; ?>
			</select>
			<? else: ?>
			<em><strong>No employees assigned to this project</strong></em>
			<? endif; ?>
		</td>
	</tr>
	<tr>
		<td>Status:</td>
		<td>
			<select name="status">
				<? foreach( $status as $key => $value ): ?>
					<option value="<?= $key ?>"><?= $value ?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Time Limit:</td>
		<td>
			<input name="time_limit" type="text">
		</td>
	</tr>
	<? if( $db->result['rows'] > 0 ): ?>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
	<? endif; ?>
</table>
</form>
</div>

<div style="padding-top: 15px;">
	<p><a href="project_employee_add.php?id=<?= $id ?>" class="large_link">Add Employee to Project &raquo;</a></p>
</div>
<!-- End of New Project Task -->

<? include( "footer.php" ); ?>