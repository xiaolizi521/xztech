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
		
	//$db->debug = true;
	// Get plan data
	$id = $_REQUEST['id'];
	$db->query( "SELECT a.*, d.organization, b.name as plan_classification, b.id
				as plan_classification_id, c.name as rate_classification, c.id as rate_classification_id
				FROM client_plan a
				INNER JOIN plan_classification b ON a.classification_id = b.id
				INNER JOIN client d ON a.client_id = d.id
				LEFT OUTER JOIN rate_classification c ON a.rate_classification_id = c.id
				WHERE a.id =  $id" );
	$plan = mysql_fetch_assoc( $db->result['ref'] );
	$plan_classification = $plan['plan_classification'];
	$plan_classification_id = $plan['plan_classification_id'];
	$rate_classification = $plan['rate_classification'];
	$rate_classification_id = $plan['rate_classification_id'];
	$organization = $plan['organization'];
		
	// See if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		//$db->debug = true;		
		
		// assignment record
		if ( $post['time_limit'] == '' ) $post['time_limit'] = 0;
		$db->add( "plan_assignment", array( "plan_id" => $post['plan_id'], "plan_id" => $post['id'], "plan_task_rate_id" => $rate_classification_id, "rate" => 0, "rate_billable" => $post['rate'], "unit" => "hour", "name" => $post['name'], "description" => $post['description'], "notes" => $post['notes'], "status" => $post['status'], "other_array" => "", "priority" => 1, "deadline" => "", "time_limit" => $post['time_limit'] ) );
		$plan_assignment_id = mysql_insert_id();
		
		// add employees assignment records
		$data = $post['employee_id'];
		if (count($data) != 0) {
			while (list($key, $value) = each($data)) {
				//echo $value."<BR>";
				$db->get( "employee", array( "id" => $value ) );
				$row = mysql_fetch_assoc( $db->result['ref'] );
				$employee_rate = $row['rate'];
				$db->add( "plan_assignment_employees", array( "plan_id" => $post['id'], "employee_id" => $value, "hidden" => 0, "plan_assignment_id" =>  $plan_assignment_id, "rate" => $employee_rate, "rate_billable" => $post['rate'], "unit" => "hour" ));
			}
		}
		
		
		header( "Location: plan_manage.php?id=".$post['id'] );
	}
	
	// Page specific functions		
	$db->query( "SELECT employee.* FROM employee WHERE employee.active = 1" );

	
	// Include header TEMPLATE
	include( "header_template.php" );
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
   				newOption.selected = true;
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

<!-- Start of New Client Assignment -->
<div>
<h1>Add Client Assignment</h1>
<? if( $db->result['rows'] == 0 ): ?>
<p class="color_red">An employee must be assigned to this project before tasks can be added</p>
<? endif; ?>
<form action="plan_assignment_add.php" method="post" name="assignment" onSubmit="javascript:submitForm();">
<input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>">
<table cellpadding="5">
	<tr>
		<td>Client:</td>
		<td><?= $organization ?></td>
	</tr>
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
		<td>Plan Classification:</td>
		<td><?= $plan_classification ?></td>
	</tr>
	<tr>
		<td>Rate Classification:</td>
		<td><?= $rate_classification ?></td>
	</tr>	
	
	<? $db2 = new db(); //$db2->debug = true; ?>
	<? $db2->query( "SELECT id, name, rate FROM plan_task_rates WHERE rate_classification_id = $rate_classification_id" ); ?>
	<tr>
		<td>Rate:</td>
		<td>
			<? if( $db2->result['rows'] > 0 ): ?>
			<select name="rate" id="rate" >			
			<? while( $row2 = mysql_fetch_assoc( $db2->result['ref'] ) ): ?>
			<option value="<?= $row2['rate'] ?>" ><?= $row2['name'] ?>&nbsp;(<?= $row2['rate'] ?>)</option>
			<? endwhile; ?>
			</select>
			<? else: ?>
			<em><strong>No rate classifications for this plan</strong></em>
			<? endif; ?>
		</td>
	</tr>
	
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
					<td>
						<select multiple size="8"  style="width:250px;" name="employee_id[]">
							
						</select>
					</td>
				</tr>
			</table>
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


<!-- End of New Project Task -->

<? include( "footer.php" ); ?>