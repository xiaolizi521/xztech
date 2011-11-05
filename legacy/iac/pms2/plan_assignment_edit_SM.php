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
		$id = $_REQUEST['id'];
	else
		$id = $post['id'];
	
	if( strlen( $_REQUEST['plan_id'] ) > 0 )
		$plan_id = $_REQUEST['plan_id'];
	else
		$plan_id = $post['plan_id'];
	
	
	//$db->debug = true;
	$db->query( "SELECT a.*, b.id as plan_id, e.organization, b.rate_classification_id FROM plan_assignment a
				INNER JOIN client_plan b ON a.plan_id = b.id
				INNER JOIN client e ON b.client_id = e.id				
				WHERE a.id = $id");
	$row = mysql_fetch_assoc( $db->result['ref'] );
	//$plan = mysql_fetch_assoc( $db->result['ref'] );
	$plan_classification = $row['plan_classification'];
	$plan_classification_id = $row['plan_classification_id'];
	$rate_classification = $row['rate_classification'];
	$rate_classification_id = $row['rate_classification_id'];
	$organization = $row['organization'];
	
	// See if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		// assignment record
		//$db->debug = true;
		if ( $post['time_limit'] == '' ) $post['time_limit'] = 0;
		$db->update( "plan_assignment", array( "plan_id" => $post['plan_id'], "plan_task_rate_id" => $rate_classification_id, "rate" => $post['rate'], "rate_billable" => $post['rate_billable'], "unit" => "hour", "name" => $post['name'], "description" => $post['description'], "notes" => $post['notes'], "status" => $post['status'], "other_array" => "", "priority" => 1, "deadline" => "", "time_limit" => $post['time_limit']), array( "id" => $id ) );
		
		// add employees assignment records
		$data = $post['employee_id'];
		if (count($data) != 0) {							
			// need to hide all employees first
			$db->update( "plan_assignment_employees", array( "hidden" => 1 ), array( "plan_assignment_id" => $id ));	
			while (list($key, $value) = each($data)) {
				$db->get( "employee", array( "id" => $value ) );
				$row = mysql_fetch_assoc( $db->result['ref'] );
				$employee_rate = $row['rate'];
			
				$db->query( "SELECT * FROM plan_assignment_employees WHERE employee_id = $value AND plan_assignment_id = $id" );
				if ( $db->result['rows'] > 0 ){	
					$db->update( "plan_assignment_employees", array( "plan_id" => $post['plan_id'], "hidden" => 0, "rate" => $employee_rate ), array( "plan_assignment_id" => $id, "employee_id" => $value ));	
				}else{			
					$db->add( "plan_assignment_employees", array( "plan_id" => $post['plan_id'], "employee_id" => $value, "hidden" => 0, "plan_assignment_id" =>  $id, "rate" => $employee_rate ));
				}
			}
		}
				
		header( "Location: plan_manage.php?id=".$post['plan_id'] );
	}
	
	
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	function getCurrentlyAssignedEmployees($id, $db){
		$assignedEmployees = array();
		$db->query( "SELECT employee.id as emp_id, plan_assignment_employees.*
						FROM employee, plan_assignment_employees
						WHERE employee.id = plan_assignment_employees.employee_id
						AND plan_assignment_employees.hidden != 1
						AND plan_assignment_employees.plan_assignment_id = $id" );
		
		while( $row = mysql_fetch_assoc( $db->result['ref'] ) ){
			$assignedEmployees[] = $row['emp_id'];
		}	
		return $assignedEmployees;
	}
?>
	
<script type="text/javascript">
	function fnMoveItems(lstbxFrom,lstbxTo){
 		var varFromBox = document.all(lstbxFrom);
 		var varToBox = document.all(lstbxTo); 
 		if ((varFromBox != null) && (varToBox != null)){ 
  			if(varFromBox.length < 1){
   				alert('There are no items in the source ListBox');
   				return false;
  			}
  			if(varFromBox.options.selectedIndex == -1) // when no Item is selected the index will be -1
  			{
   				alert('Please select an Item to move');
   				return false;
  			}
  			while ( varFromBox.options.selectedIndex >= 0 ) 
  			{ 
   				var newOption = new Option(); // Create a new instance of ListItem 
   				newOption.text = varFromBox.options[varFromBox.options.selectedIndex].text; 
   				newOption.value = varFromBox.options[varFromBox.options.selectedIndex].value; 
   				//newOption.selected = true;
   				varToBox.options[varToBox.length] = newOption; //Append the item in Target Listbox
   				varFromBox.remove(varFromBox.options.selectedIndex); //Remove the item from Source Listbox 
  			} 
 		}
 		return false; 
	}

	function submitForm(){
		var varToBox = document.all('employee_id[]');
		var x = 0;
		for ( x = 0; x < varToBox.length; x++){
			varToBox.options[x].selected = true;
		}		
	}	
</script>

<div>
<h1>Edit Assignment Information</h1>
<form action="plan_assignment_edit.php" method="post" onSubmit="submitForm();">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="plan_id" value="<?= $plan_id ?>">
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
	//$db->debug = true;	
	$assignedEmployees = getCurrentlyAssignedEmployees($id, $db);	
	$db->query( "SELECT employee.* FROM employee WHERE employee.active = 1 AND employee.id NOT IN (SELECT employee_id FROM plan_assignment_employees WHERE plan_assignment_id = $id )" );	
	?>

	<tr>
		<td valign="top"><br/>Assign Employees:</td>
		<td>
			<table border="0">
				<tr>
					<td align="center" style="font-size:10pt;color:blue;">Employees</td>
					<td>&nbsp;</td>
					<td  align="center" style="font-size:10pt;color:blue;">Assigned</td>
				</tr>
				<tr>
					<td>
						<select multiple size="8" style="width:250px;" name="employee_pool">
							<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
								<option value="<?= $row['id'] ?>" ><?= $row['name'] ?> (<?= $row['username'] ?>)</option>
							<? endwhile; ?>
						</select>
					</td>
					<td width="40px" align="center">
						<input type="button" id="btnMoveRight" onClick="fnMoveItems('employee_pool','employee_id[]')" value="-->" /> <br/><br/>
						<input type="button" id="btnMoveLeft" onclick="fnMoveItems('employee_id[]','employee_pool')" value="<--" /><br/>
					</td>
					<? $db->query( "SELECT employee.* FROM employee WHERE employee.active = 1 AND employee.id IN (SELECT employee_id FROM plan_assignment_employees WHERE hidden != 1 AND plan_assignment_id = $id )" );	?>
					<td>
						<select multiple size="8"  style="width:250px;" name="employee_id[]">
							<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
								<option value="<?= $row['id'] ?>" ><?= $row['name'] ?> (<?= $row['username'] ?>)</option>
							<? endwhile; ?>		
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
		
	
	
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
<p><a href="plan_assignment_remove.php?id=<?= $task_id ?>&project_id=<?= $project_id ?>" class="large_link">Remove Assignment &raquo;</a></p>
</div>

<!-- End of New Project Task -->

<? include( "footer.php" ); ?>