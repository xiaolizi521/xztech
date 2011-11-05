<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( $p_level != "super-manager" )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Where
	$deleted = $_REQUEST['deleted'];
	$status = $_REQUEST['status'];

	if( strlen( $deleted ) > 0 )
		$where = "r.deleted = $deleted ";
	
	if( strlen( $status ) > 0 )
	{
		if( strlen( $where ) > 0 )
			$where .= " AND ";
			
		$where .= "r.status = \"$status\"";
	}
	
	if( strlen( $where ) > 0 )
		$where = "WHERE $where";
	else
		$where = "WHERE r.status != \"closed\"";
	
	// Page specific functions
	$db->query( "SELECT r.company as r_company, r.id as r_id, r.name as r_name, r.phone as r_phone, r.status as r_status, c.organization, r.created as r_created, r.status FROM referrals AS r LEFT JOIN client AS c ON r.client_id = c.id $where ORDER BY r.status" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div>
<h1>Referrals</h1>
<p><a href="referrals.php" class="large_link">Not Closed</a> | <a href="referrals.php?status=new" class="large_link">New</a> | <a href="referrals.php?status=closed" class="large_link">Closed</a> | <a href="referrals.php?deleted=1" class="large_link">Deleted</a></p>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Created</td>
		<td>Status</td>
		<td>Company</td>
		<td>Name</td>
		<td>Phone</td>
		<td>Referred By</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td nowrap><?= date( "M. d, Y", strtotime( $row['r_created'] ) ) ?></td>
		<td><?= ucfirst( $row['status'] ) ?></td>
		<td><?= $row['r_company'] ?>&nbsp;</td>
		<td><?= $row['r_name'] ?>&nbsp;</td>
		<td><?= $row['r_phone'] ?>&nbsp;</td>
		<td><?= $row['organization'] ?></td>
		<td nowrap class="link_button"><a href="referral.php?id=<?= $row['r_id'] ?>">View</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There are no referrals</em></p>
<? endif; ?>
</div>

<? include( "footer.php" ); ?>