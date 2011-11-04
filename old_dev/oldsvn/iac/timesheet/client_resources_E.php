<?
	session_start();

	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];	

		include( "header_functions.php" );


	// Get user_id 
	$db->query( "SELECT id FROM employee WHERE username = '".$_SESSION['username']."'");
	$userData = mysql_fetch_assoc( $db->result['ref'] );
	
	// Get associated projects and clients
	$db->query( "SELECT c.id as c_id, c.resource_name as c_resource_name, c.url as c_url, c.username as c_username, c.password as c_password, c.notes as c_notes, d.organization FROM client_resources c, client d WHERE c.client_id = d.id AND c.client_id in (SELECT distinct a.client_id FROM project a, project_employees b where a.id = b.project_id and a.status != 'completed' and b.hidden = 0 and b.employee_id = ".$userData['id'].") ORDER BY d.organization" );

	// Include header TEMPLATE
	include( "header_template.php" );

?>

<script language="javascript">
	function confirmResourceDelete(id){
		if (confirm("Delete this client resource?")){
			location.href = "client_resources_edit.php?mode=delete&id=" + id;
		}
	}
</script>

<div>
<h1>Client Resources</h1>
<p><a href="client_resources_add_E.php">Add New Resource</a></p>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table" >
	<tr class="table_heading">	
		<td>Client</td>	
		<td>Resource Name</td>
		<td>Username</td>
		<td>Password</td>
		<td>Notes</td>		
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">	
		<td valign="top"><?= $row['organization'] ?></td>	
		<? if ($row['c_resource_name'] == ''){$resourceName = 'Resource Link';}else{$resourceName = $row['c_resource_name'];} ?>
		<td valign="top" style="width:166px;word-wrap:break-word;white-space: -moz-pre-wrap;white-space: pre-wrap;"><a href="<?= $row['c_url']?>" title="<?= $row['c_url']?>" target="_blank"><?= $resourceName ?></a></td>
		<td valign="top" style="width:166px;word-wrap:break-word;white-space: -moz-pre-wrap;"><?= $row['c_username'] ?></td>
		<td valign="top"><?= $row['c_password'] ?></td>
		<td valign="top"><a href="client_resources_edit_E.php?mode=edit&id=<?= $row['c_id'] ?>">View</a></td>		
		<td valign="top"><a href="client_resources_edit_E.php?mode=edit&id=<?= $row['c_id'] ?>">Edit</a>&nbsp;|&nbsp;<a href="client_resource_request_delete.php?mode=request&id=<?= $row['c_id'] ?>">Delete</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There are no client resources</em></p>
<? endif; ?>
</div>

<? include( "footer.php" ); ?>