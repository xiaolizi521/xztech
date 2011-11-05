
<? if( $_REQUEST['action'] == "generate" ): ?>
<p class="color_black">Active Employee Projects</p>
<?

if( strlen( $post['project'] ) > 0 )
	$project = "AND project.id = ".$post['project'];

if( strlen( $post['employee'] ) > 0 ){
	if ( $post['employee'] == 'allactive'){
		$employee = "AND employee.active = 1";
	}else{
		$employee = "AND employee.id = ".$post['employee'];
	}
}

$includeHidden = "";
if ( isset( $post['includeHidden'] ) ){
	if ( $post['includeHidden'] = "1" )
		$includeHidden = "";		
}else{
	$includeHidden = " AND project_employees.hidden != 1 ";		
}

$db->query( "SELECT 	employee.name as employee_name,
	project.name as project_name,
	client.organization
FROM project_employees INNER JOIN project ON project_employees.project_id = project.id
	 INNER JOIN client ON project.client_id = client.id
	 INNER JOIN employee ON project_employees.employee_id = employee.id
	WHERE project.status != 'completed' $project $employee $includeHidden
ORDER BY employee.name ASC, project.name ASC, client.organization ASC" );

if( $db->result['rows'] > 0 ): ?>
	<div>
		<table cellpadding="0" cellspacing="0" border="0" class="report_table">
			<tr class="report_heading">
				<td>Employee</td>
				<td>Project</td>
				<td>Client</td>
			</tr>
		<? $first_run = true; ?>
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<? if( $prev_name == $row['employee_name'] ): ?>
			<tr class="report_row">
				<td>&nbsp;</td>
			<? elseif( $first_run ): ?>
				<tr class="report_row">
					<td><strong><?= $row['employee_name'] ?></strong></td>
			<? else: ?>
			<tr class="report_row_end">
				<td><strong><?= $row['employee_name'] ?></strong></td>
			<? endif; ?>
			<? $prev_name = $row['employee_name']; ?>
			<td><?= $row['project_name'] ?></td>
			<td><?= $row['organization'] ?></td>
		</tr>
		<? $first_run = false; ?>
		<? endwhile; ?>
		</table>
	</div>
	<? elseif( $db->result['rows'] == 0 ): ?>
	<div>
		<p><strong>No results</strong></p>
		<p><a href="reports.php" class="large_link" alt="Reports">&laquo; Go Back</a></p>
	</div>
	<? endif; ?>

<? else: ?>
<p class="color_black">Active Employee Projects</p>
	<form method="post" action="reports.php?report=employee_projects&action=generate">
		<table cellpadding="5" border="0">
			<tr>
				<td>Project:</td>
				<td>
				<select name="project">
				<option value="">All Projects</option>
				<option value="">-------------------------</option>
				<?
					$db->get( "project", array() );

					while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):
						if ( $row['status'] != 'completed'){
				?>
						<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?}?>
				<? endwhile; ?>
				</select>
				</td>
			</tr>
			<tr>
				<td>Employee:</td>
				<td>
				<select name="employee">
				<option value="">All Employees</option>
				<option value="allactive">Active Employees Only</option>
				<option value="">-------------------------</option>

				// Get active employees first
				<optgroup label="Active Employees" >
				<?
					$db->get( "employee", array() );
					$recordSet = $db->result['ref'];
					while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):
						if ($row['active'] == 1){
				?>
				<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?}?>
				<? endwhile; ?>
				</optgroup>
				<optgroup label="Inactive Employees" >
				<?
				// Now inactive employees
					$db->get( "employee", array() );
					while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):
						if ($row['active'] == 0){
				?>
				<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?}?>
				<? endwhile; ?>
				</optgroup>
				</select>
				</td>
			</tr>
			<tr>
				<td>Include Hidden Employees:</td>
				<td><input type="checkbox" name="includeHidden" value="1" /></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<input type="submit" value="Generate Report">
				</td>
			</tr>
		</table>
	</form>
<? endif; ?>