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
		$retainedHours = 0;
		$hoursPurchased = 0;
		if ( $post['retainedHours'] != "" ) $retainedHours = $post['retainedHours'];
		if ( $post['hoursPurchased'] != "" ) $hoursPurchased = $post['hoursPurchased'];
		//$db->debug = true;
		$db->add( "client_plan" , array( "client_id" => $post['client_id'], "classification_id" => $post['plan_classification_id'], "rate_classification_id" => $post['rate_classification_id'], "notes" => $post['notes'], "status" => $post['status'], "retained_hours" => $retainedHours, "hours_purchased" => $hoursPurchased ) );
		$newId = mysql_insert_id(); 
		header( "Location: plan_manage.php?id=".$newId );
	}
	
	// Page specific functions	
	$db->query( "SELECT id, organization FROM client WHERE deleted != 1 ORDER BY organization ASC" );

	// Include header TEMPLATE
	include( "header_template.php" );
?>

<script language="javascript">
	function getRateClassification(el){
		//alert(el.options[el.selectedIndex].value);
		var id = el.options[el.selectedIndex].value;
		new Ajax.Updater('rate_class', 'ajax_functions.php?f=RATE_CLASS&&id=' + id, { method: 'get' });	
	}
	
</script>

<!-- Start of New Client Plan -->
<div>
<h1>Add Client Plan</h1>
<form action="client_plan_add.php" method="post" name="task">
<table cellpadding="5">
	<tr>
		<td>Client Name:</td>
		<td>
			<? if( $db->result['rows'] > 0 ): ?>
			<select name="client_id" id="client_id" >
			<option value=""></option>
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['id'] ?>" ><?= $row['organization'] ?></option>
			<? endwhile; ?>
			</select>
			<? else: ?>
			<em><strong>No client to assign to this plan</strong></em>
			<? endif; ?>
		</td>
	</tr>
	
	<!-- Plan Classification -->
	<? $db->query( "SELECT id, name FROM plan_classification WHERE active = 1 ORDER BY name" ); ?>
	<tr>
		<td>Plan Classification:</td>
		<td>
			<? if( $db->result['rows'] > 0 ): ?>
			<select name="plan_classification_id" id="plan_classification_id" onChange="javascript:getRateClassification(this);">
			<option value=""></option>
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['id'] ?>" ><?= $row['name'] ?></option>
			<? endwhile; ?>
			</select>
			<? else: ?>
			<em><strong>No plan classifications for this plan</strong></em>
			<? endif; ?>
		</td>
	</tr>
	
	
	<!-- Rate Classification -->
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
	<tr id="retainedHours" name="retainedHours">
		<td>Retained Hours</td>
		<td style="color:red;font-size:9pt;"><input type="text" name="retainedHours" value="" /> <i>* for Administrative Retainer plans only</i> </td>
	</tr>
	<tr id="hoursPurchased">
		<td>Hours Purchased</td>
		<td style="color:red;font-size:9pt;"><input type="text" name="hoursPurchased" value="" /> <i>* for Administrative Prepaid plans only</i> </td>
	</tr>
	<tr>
		<td>Status:</td>
		<td>
			<select name="status" id="status">
				<option value="do-not-start" <? if ( $status == "do-not-start" ) echo " selected "; ?>>Do Not Start</option>
				<option value="completed" <? if ( $status == "completed" ) echo " selected "; ?>>Completed</option>
				<option value="in-progress" <? if ( $status == "in-progress" ) echo " selected "; ?>>In Progress</option>
				<option value="not-started" <? if ( $status == "not-started" ) echo " selected "; ?>>Not Started</option>
			</select>
		</td>
	</tr>	
	<tr>
		<td valign="top">Notes:</td>
		<td><textarea name="notes" cols="60" rows="5"></textarea></td>
	</tr>	
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>

<!-- End of New Client Plan -->

<? include( "footer.php" ); ?>