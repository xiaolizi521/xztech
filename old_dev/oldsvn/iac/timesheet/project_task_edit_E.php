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
		$db->update( "project_task", array( "notes" => $post['notes'], "status" => $post['status'] ), array( "id" => $post['task_id'] ) );
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
		<td><?= stripslashes( $row['name'] ) ?></td>
	</tr>
	<tr>
		<td valign="top">Description:</td>
		<td><?= stripslashes( $row['description'] ) ?></td>
	</tr>
	<tr>
		<td valign="top">Notes:</td>
		<td><textarea cols="60" rows="6" name="notes"><?= stripslashes( $row['notes'] ) ?></textarea></td>
	</tr>
	<tr>
		<td>Rate:</td>
		<td>$ <?= $row['rate'] ?> / <?= $row['unit'] ?></td>
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
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>

<!-- End of New Project Task -->

<? include( "footer.php" ); ?>