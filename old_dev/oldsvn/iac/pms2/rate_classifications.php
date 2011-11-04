<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( strlen( $p_level ) > 0 )
		include( "header_functions.php" );
	else
	{
		header( "Location: home.php" );
		exit();
	}
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	
	
   
?>

<div>
	<h1>Rate Classifications</h1>
	<? $db->query( "SELECT * FROM rate_classification ORDER BY name" )?>
	
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Name</td>
			<td>Description</td>
			<td>Create Date</td>
			<td>Active</td>
			<td>&nbsp;</td>
		</tr>
		
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<tr class="table_row">
			<td><a href="rate_classification_edit.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
			<td><?= strlen( $row['description'] ) > 0 ? substr( $row['description'], 0, 30 )."..." : "&nbsp;" ?></td>			
			<td><?= $row['create_date'] ?></td>
			<td><? if( $row['active'] == 1 ) echo "YES"; else echo "NO" ?></td>
			<td class="link_button"><a href="rate_classification_edit.php?id=<?= $row['id'] ?>">Edit</a></td>
		</tr>
		<? endwhile; ?>		
	</table>
	
	<p><a href="rate_classification_add.php" class="large_link">Add Rate Classification</a></p>
</div>


<div>
	<h1>Plan Task Rates</h1>
	<? $db->query( "SELECT a.*, b.name as rate_classification_name FROM plan_task_rates a, rate_classification b
					WHERE a.rate_classification_id = b.id ORDER BY rate_classification_name, b.name" )?>
	
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Name</td>
			<td>Rate Classification</td>
			<td>Description</td>
			<td>Rate</td>
			<td>Unit</td>
			<td>Active</td>
			<td>&nbsp;</td>
		</tr>
		
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<tr class="table_row">
			<td><a href="plan_task_rate_edit.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
			<td><?= $row['rate_classification_name'] ?></td>
			<td><?= strlen( $row['description'] ) > 0 ? substr( $row['description'], 0, 30 )."..." : "&nbsp;" ?></td>			
			<td><?= $row['rate'] ?></td>
			<td><?= $row['unit'] ?></td>
			<td><? if( $row['active'] == 1 ) echo "YES"; else echo "NO" ?></td>
			<td class="link_button"><a href="plan_task_rate_edit.php?id=<?= $row['id'] ?>">Edit</a></td>
		</tr>
		<? endwhile; ?>		
	</table>
	
	<p><a href="plan_task_rate_add.php" class="large_link">Add Plan Task Rate</a></p>
</div>


<? include( "footer.php" ); ?>