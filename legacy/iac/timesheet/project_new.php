<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];

	if( $p_level != "super-manager" 
		&& $p_levelType != 'manager'
		&& $p_levelType != 'human resources')
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );
	
	// Check to see if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		$db->add( "project", $post );
		header( "Location: projects.php" );
	}
	
	// Page specific functions
	$db->query( "SELECT * FROM client WHERE deleted != \"1\" ORDER BY organization" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of New Project -->
<div>
<h1>New Project</h1>
<form action="project_new.php" method="post">
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="35"></td>
	</tr>
	<tr>
		<td valign="top">Description:</td>
		<td><textarea name="description" cols="60" rows="5"></textarea></td>
	</tr>
	<tr>
		<td>Client:</td>
		<td>
			<select name="client_id">
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['id'] ?>"<? if( $row['id'] == $_REQUEST['id'] ) echo " selected=\"yes\""?>><?= $row['first_name']." ".$row['last_name'] ?> (<?= $row['organization'] ?>)</option>
			<? endwhile; ?>
			</select>
		</td>
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
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
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
				<option value="<?= $key ?>"><?= $value ?></option>
			<? endforeach; ?>
		</select>
		</td>
	</tr>
	<tr>
		<td>Deadline:</td>
		<td>
			<input type="text" name="deadline">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>
<!-- End of New Project -->

<? include( "footer.php" ); ?>