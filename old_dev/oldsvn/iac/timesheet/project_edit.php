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
	
	// Page functions
	if( $post['project_id'] > 0 )
		$id = $post['project_id'];
	else
		$id = $_REQUEST['id'];
	
	// Check to see if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		if( $post['status'] == "completed" )
		{
			$id = $post['project_id'];
			$db->query( "SELECT * FROM project_task WHERE `project_id` = $id AND `status` != 'completed'" );
		
			if( $db->result['rows'] > 0 )
				$error = "All project tasks must be marked completed before marking this project completed";
		}

		if( strlen( $error ) < 1 )
		{
			$db->update( "project", array( "name" => $post['name'], "description" => $post['description'], "status" => $post['status'], "client_id" => $post['client_id'], "category" => $post['category'], "limit_hours" => $post['limit_hours'] ), array( "id" => $post['project_id'] ) );
			
			if( strlen( $post['return'] ) > 0 )
				header( "Location: ".$post['return'].".php" );
			else
				header( "Location: project_manage.php?id=".$post['project_id'] );
		}
	}

	// Page specific functions
	$db->query( "SELECT project.name, project.id, project.status, project.deadline, project.description, project.limit_hours, client.organization, client.id as client_id, project.category FROM project, client WHERE client.id = project.client_id AND project.id = $id" );
	$row = mysql_fetch_assoc( $db->result['ref'] );

	$db3 = new db();
	$db3->query( "SELECT * FROM client ORDER BY organization ASC" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of Edit Project -->
<div>
<h1>Edit Project</h1>
<? if( strlen( $error ) > 0 ): ?>
<p class="color_red"><?= $error ?></p>
<? endif; ?>
<form action="project_edit.php" method="post">
<input type="hidden" name="project_id" value="<?= $id ?>">
<input type="hidden" name="return" value="<?= $_REQUEST['ret'] ?>">
<table cellpadding="5">
	<tr>
		<td>Client:</td>
		<td>
		<select name="client_id">
		<? while( $clients = mysql_fetch_assoc( $db3->result['ref'] ) ): ?>
		<? if( $row['client_id'] == $clients['id'] ): ?>
			<option value="<?= $clients['id'] ?>" selected="yes"><?= $clients['organization'] ?></option>
		<? else: ?>
			<option value="<?= $clients['id'] ?>"><?= $clients['organization'] ?></option>
		<? endif; ?>
		<? endwhile; ?>
		</select>
		</td>
	</tr>
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="35" value="<?= stripslashes( $row['name'] ) ?>"></td>
	</tr>
	<tr>
		<td valign="top">Description:</td>
		<td><textarea name="description" cols="60" rows="5"><?= stripslashes( $row['description'] ) ?></textarea></td>
	</tr>
	<?
		$db->query( "SELECT * FROM project_categories ORDER BY name ASC" );
		if( $db->result['rows'] > 0 ):
	?>
	<tr>
		<td>Category:</td>
		<td>
			<select name="category">
			<option value=""></option>
			<? while( $cat = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
				<? if( $row['category'] == $cat['id'] ): ?>
					<option value="<?= $cat['id'] ?>" selected="yes"><?= $cat['name'] ?></option>
				<? else: ?>
					<option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
				<? endif; ?>
			<? endwhile; ?>
			</select>
		</td>
	</tr>
	<? endif; ?>
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
		<td>Hours Purchased:</td>
		<td>
			<input type="text" name="limit_hours" value="<?= stripslashes( $row['limit_hours'] ) ?>">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>

<div style="padding-top: 15px;">
	<p><a href="project_remove.php?id=<?= $id ?>" class="large_link">Remove Project &raquo;</a></p>
</div>

<!-- End of Edit Project -->

<? include( "footer.php" ); ?>