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

	// Page specific functions
	//$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status, project_categories.color_light, project_categories.color_text, project_categories.name as category_name FROM project, client LEFT JOIN project_categories ON project_categories.id = project.category WHERE project.client_id = client.id AND project.status != 'completed' ORDER BY project_categories.priority DESC, project.name ASC" );
	$sql = "select a.id as client_id, a.organization, b.code, c.name as assignment_name, c.status, c.id as assignment_id, d.color_text, d.color_light from client a
			inner join classification b on a.classification = b.id
			inner join client_assignment c on a.id = c.client_id
			inner join project_categories d on b.code = d.name
			where a.deleted != 1 and c.status != 'completed'
			order by a.organization, c.name;";
	
	$sql = "SELECT a.id as client_id, a.organization, b.id as plan_id, c.name as plan_name, b.status FROM client a
			INNER JOIN client_plan b ON a.id = b.client_id
			INNER JOIN plan_classification c ON b.classification_id = c.id
			WHERE b.status != 'completed'
			ORDER BY a.organization;";
	$db->query( $sql );
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<script language="javascript">
	function displayHide(f){
		document.getElementById(f).classname = 'hide';
		//alert(f);
    	//if (document.all[f].style.display == 'none'){
    	//	document.all[f].style.display = 'block';
        //	var sb = 'b' + f;
        //	document.all[sb].src = 'images/collapse.gif';
    	//}else{
        //	document.all[f].style.display = 'none';
        //	var sb = 'b' + f;
        //	document.all[sb].src = 'images/expand.gif';
    	//}
	}
</script>

<div>
<h1>Active Clients/Plans</h1>
<p><a href="client_plan_add.php" class="large_link">Add New Plan</a></p>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">		
		<td>Client</td>
		<td>Plan</td> 
		<td>Status</td>
		<td>&nbsp;</td>
	</tr>
	<? $newGroup = false ?>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<?	$new_client_plan = $row['organization']."/".$row['plan_name']; 
		if ($client_plan != $new_client_plan) {
			$newGroup = true;
			$client_plan = $row['organization']."/".$row['plan_name'];
		}
	?>
	<?	if ($newGroup) { ?>
			
			<tr class="group_row">
		<?}else{?>
			
			<tr id="row<?= $row['client_id'] ?>" class="table_row">
		<?}
		
		if ($newGroup){
		?>
		<td><span style="font-weight: bold;"><a href="client_manage.php?id=<?= $row['client_id'] ?>"><?= $row['organization'] ?></a></span></td>
		
		<? $client_plan = $row['organization']."/".$row['code']; ?>
		<?}else{?>
		<td>&nbsp;</td>
		
		<?}?>
		<td><a href="client_plan_edit.php?id=<?= $row['plan_id'] ?>"><span style="color: blue;"><?= $row['plan_name'] ?></span></a></td> 
		<td nowrap ><?= status( $row['status'] ) ?></td>
		<td nowrap class="link_button" <?= categoryStyle( $row['color_light'] ) ?>><a href="plan_manage.php?id=<?= $row['plan_id'] ?>">Manage</a>&nbsp;|&nbsp;<a href="plan_assignment_add.php?id=<?= $row['plan_id'] ?>">Add Assignment</a></td>
	</tr>
	<? $newGroup = false; ?>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There is no client activity</em></p>
<? endif; ?>
</div>

<? include( "footer.php" ); ?>