<? $page = "super-manager"; ?>
<? include( "header.php" ); ?>

<?
	$db->get( "project_task", array() );
?>
	
<!-- Start of Tasks -->
<div>
<h1>Current Tasks</h1>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Task</td>
		<td>Description</td>
		<td>Rate</td>
		<td>Actions</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><?= $row['name'] ?></td>
		<td><?= $row['description'] ?></td>
		<td><?= $row['rate'] ?></td>
		<td><strong><a href="employee_edit.php?id=<?= $row['id'] ?>">Edit</a></strong></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<em>No tasks to display</em>
<? endif; ?>
</div>
<!-- End of Tasks -->

<div style="padding-top: 15px;">
	<a href="tasks_add.php" class="large_link">Add Task &raquo;</a>
</div>

<div style="padding-top: 15px;">
	<h1>Awaiting Approval</h1>
</div>

<? include( "footer.php" ); ?>