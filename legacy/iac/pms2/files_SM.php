<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];

	if( $p_level != "super-manager" 
		&& $p_levelType != 'timekeeper'
		&& $p_levelType != 'manager'
		&& $p_levelType != 'human resources'
		&& $p_levelType != 'system administrator')
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page specific functions
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
	WHERE files.size > 10
	ORDER BY files.date DESC" );
	
	// Include header TEMPLATE
	include( "header_template.php" );

?>

<div>
<h1>Files</h1>
<? if( isset( $_REQUEST['success'] ) ): ?>
<p class="color_blue">File uploaded successfully</p>
<? endif; ?>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>File</td>
		<td>Name</td>
		<td>Project</td>
		<td>Created</td>
		<td>Owner</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><img src="images/file_icons/<?= fileTypeImage( $row['mime'] ) ?>"></td>
		<td><a href="./uploads/<?= $row['filename']?>"><?= $row['filename'] ?></a></td>
		<td><a href="./uploads/<?= $row['filename']?>"><?= $row['name'] ?>&nbsp;</a></td>
		<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['project_name'] ?></a></td>
		<td nowrap><?= date( "m/d/y", $row['date'] ) ?> at <?= date( "g:i:s A", $row['date'] ) ?></td>
		<td><?= $row['uploaded_by_name'] ?>&nbsp;</td>
		<td nowrap><a href="file_delete.php?id=<?= $row['id'] ?>"><img src="images/misc_icons/email-delete.gif"> Delete</a></td>
		<td nowrap><a href="file_details.php?id=<?= $row['id'] ?>" class="lbOn"><img src="images/misc_icons/details.gif"> Details</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There are no files</em></p>
<? endif; ?>
<p><a href="file_new.php" class="large_link">Upload File &raquo;</a></p>
</div>

<? include( "footer.php" ); ?>