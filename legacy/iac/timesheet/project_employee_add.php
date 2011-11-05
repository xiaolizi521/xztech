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
	
	// Check if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		$db->get( "project_employees", array( "employee_id" => $post['employee'], "project_id" => $post['id'] ) );
		if( $db->result['rows'] == 0 )
			$db->add( "project_employees", array( "employee_id" => $post['employee'], "project_id" => $post['id'] ) );
		
		header( "Location: project_manage.php?id=".$post['id'] );
	}

	// Page specific functions
	$id = $_REQUEST['id'];
	$db->query( "SELECT * FROM project, client WHERE project.client_id = client.id AND project.id = $id" );
	
	if( $db->result['rows'] == 0 )
		header( "Location: projects.php" );
	else
		$project = mysql_fetch_assoc( $db->result['ref'] );
	
	$db->get( "employee", array( "active" => "1" ) );
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of New Project -->
<div>
<h1>Add Employee to Project</h1>
<? if( $db->result['rows'] == 0 ): ?>
<p class="color_red">An employee must be assigned to this project before tasks are created</p>
<? endif; ?>
<form action="project_employee_add.php" method="post">
<input type="hidden" name="id" value="<?= $id ?>">
<table cellpadding="5">
	<tr>
		<td>Client:</td>
		<td><?= $project['first_name']." ".$project['last_name']." (".$project['organization'].")"?>
	</tr>
	<tr>
		<td>Project:</td>
		<td><?= $project['name'] ?></td>
	</tr>
	<tr>
		<td>Employee:</td>
		<td>
			<select name="employee">
			<? while( $row = mysql_fetch_array( $db->result['ref'] ) ): ?>
				<option value="<?= $row['id'] ?>"><?= $row['name'] ?> (<?= $row['username'] ?>)</option>
			<? endwhile; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Add Employee &raquo;"></td>
	</tr>
</table>
</form>
</div>

<? if( $db->result['rows'] == 0 ): ?>
<div style="padding-top: 15px;">
	<p><a href="project_employee_add.php?id=" class="large_link">Add Employee to Project &raquo;</a></p>
</div>
<? endif; ?>
<!-- End of New Project -->

<? include( "footer.php" ); ?>