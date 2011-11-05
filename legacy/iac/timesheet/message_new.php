<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	include( "header_functions.php" );
	
	// Check to see if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		$db->add( "project", $post );
		header( "Location: projects.php" );
		exit();
	}
	
	// Page specific functions - for super manager
	if( $p_level == "super-manager" )
		$db->query( "SELECT project.name AS project_name, 
					project.id AS project_id
					FROM project 
					WHERE project.status != 'completed'
					ORDER BY project.name ASC" );
	else
		$db->query( "SELECT project.name AS project_name, 			
					project.id AS project_id
					FROM project INNER JOIN project_employees ON project_employees.project_id = project.id
					WHERE project.status != 'completed' AND project_employees.employee_id = ".$employeeArray['id']."
					ORDER BY project.name ASC" );

	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of New Project -->
<div>
<h1>Send Message</h1>
<form action="message_send.php" method="post">
<table cellpadding="5">
	<tr>
		<td>Project:</td>
		<td>
			<select name="project_id">
				<option value="0">No Project Selected</option>
				<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>					
					<option value="<?= $row['project_id'] ?>"><?= $row['project_name'] ?> </option>
				<? endwhile; ?>
			</select>
		</td>
	</tr>
	<?
	if( $p_level == "super-manager" )
		$db->query( "SELECT project.name as project_name,
			files.filename, 
			files.mime, 
			files.name, 
			files.date, 
			files.id, 
			files.audio_length, 
			files.project_id,
			files.uploaded_by_name
		FROM files INNER JOIN project ON files.project_id = project.id
		WHERE files.size > 10 AND project.status != 'completed'
		ORDER BY project.name DESC, files.date DESC" );
	else
		$db->query( "SELECT project.name as project_name,
		files.filename, 
		files.mime, 
		files.name, 
		files.date, 
		files.id, 
		files.audio_length, 
		files.project_id,
		files.uploaded_by_name
		FROM files 
		INNER JOIN project ON files.project_id = project.id
		INNER JOIN project_employees ON files.project_id = project_employees.project_id
		WHERE files.size > 10 AND project_employees.employee_id = ".$employeeArray['id']."
		ORDER BY project.name DESC, files.date DESC" );
	?>
	<tr>
		<td>File Attachment:</td>
		<td>
			<select name="file_id">
				<option value="0">No File Selected</option>
				<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
				<option value="<?= $row['id'] ?>|<?= $row['filename'] ?>"><?= $row['project_name'] ?> | <?= $row['filename'] ?></option>
				<? endwhile; ?>
			</select>
		</td>
	</tr>
	
	<?
	$db->query("SELECT id, name FROM employee WHERE active = 1 ORDER BY name");
	?>
	<tr>
		<td valign="top">Delivered To:</td>
		<td>
			<select name="recipients[]" multiple size="5">				
				<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
				<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
				<? endwhile; ?>
			</select>
		</td>
	<tr>
		<td>Priority:</td>
		<td>
			<select name="priority">
				<option value="normal">Normal</option>
				<option value="high">High</option>
			</select>
		</td>
	<tr>
		<td valign="top">Message:</td>
		<td><textarea name="message" cols="70" rows="8"></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>
<!-- End of New Project -->

<? include( "footer.php" ); ?>