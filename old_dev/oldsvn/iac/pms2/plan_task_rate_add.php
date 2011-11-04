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
		//$db->debug = true;
		$db->add( "plan_task_rates" , array( "name" => $post['name'], "description" => $post['desc'], "active" => $post['active'], "rate_classification_id" => $post['rate_classification_id'], "rate" => $post['rate'], "unit" => $post['unit'] ) );
		
		header( "Location: rate_classifications.php" );
	}
	

	// Include header TEMPLATE
	include( "header_template.php" );
?>



<div>
<h1>Add Plan Task Rate</h1>
<form action="plan_task_rate_add.php" method="post" name="rate">
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" value="" size="50" /></td>
	</tr>
	
	<? $db->query( "SELECT id, name FROM rate_classification WHERE active = 1 ORDER BY name" ); ?>
	<tr>
		<td>Rate Classification:</td>
		<td>
			<? if( $db->result['rows'] > 0 ): ?>
			<select name="rate_classification_id" id="rate_classification_id" >
			<option value=""></option>
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['id'] ?>" ><?= $row['name'] ?></option>
			<? endwhile; ?>
			</select>
			<? else: ?>
			<em><strong>No rate classifications for this plan</strong></em>
			<? endif; ?>
		</td>
	</tr>
	<tr>
		<td valign="top">Description:</td>
		<td><textarea name="desc" cols="60" rows="5"></textarea></td>
	</tr>
	<tr>
		<td valign="top">Rate:</td>
		<td><input size="8" name="rate" type="text" value="" /></td>
	</tr>
	<tr>
		<td>Unit:</td>
		<td>
			<select name="unit" id="status">
				<option value="hour">Per Hour</option>
				<option value="assignment">Per Assignment</option>
			</select>
		</td>
	</tr>		
	<tr>
		<td>Status:</td>
		<td>
			<select name="active" id="status">
				<option value="0">Not Active</option>
				<option value="1">Active</option>
			</select>
		</td>
	</tr>	
	
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>



<? include( "footer.php" ); ?>