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
		$db->query( "SELECT * FROM project WHERE status != \"completed\" ORDER BY name" );
	else
		$db->query( "SELECT project.client_id, 
			project.description, 
			project.name, 
			project.status, 
			project.invoice, 
			project.id
		FROM project INNER JOIN project_employees ON project.id = project_employees.project_id
		WHERE project.status != \"completed\" AND project_employees.employee_id = ".$employeeArray['id']."
		ORDER BY project.name" );
		
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of New Project -->
<div>
<h1>File Upload</h1>
<form enctype="multipart/form-data" action="file_upload.php" method="post">
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="35"></td>
	</tr>
	<tr>
		<td>File:</td>
		<td><input type="file" name="file" size="35"></td>
	</tr>
	<tr>
		<td>Project:</td>
		<td>
			<select name="project_id">
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['id'] ?>"<? if( $row['id'] == $_REQUEST['id'] ) echo " selected=\"yes\""?>><?= $row['name'] ?></option>
			<? endwhile; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top">Description:</td>
		<td><textarea name="description" cols="60" rows="5"></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>
<!-- End of New Project -->

<? include( "footer.php" ); ?>