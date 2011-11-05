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

	// Page specific functions
	$db->query( "SELECT r.company as r_company, r.id as r_id, r.name as r_name, r.phone as r_phone, r.status as r_status, c.organization, r.created as r_created, r.status, r.notes, r.email as r_email, r.commission FROM referrals AS r LEFT JOIN client AS c ON r.client_id = c.id WHERE r.id = " . $_REQUEST['id'] );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<div>
<h1>Referral</h1>
<? if( $db->result['rows'] > 0 ): ?>
<? $row = mysql_fetch_assoc( $db->result['ref'] ) ?>

<p><strong>Status</strong><br/><?= ucfirst( $row['status'] ) ?></p>
<p><strong>Company</strong><br/><?= $row['r_company'] ?></p>
<p><strong>Referred by</strong><br/><?= $row['organization'] ?></p>
<p><strong>Created</strong><br/><?= date( "M. d, Y", strtotime( $row['r_created'] ) ) ?></p>
<p><strong>Contact</strong><br/><?= $row['r_name'] ?></p>
<p><strong>Email</strong><br/><?= $row['r_email'] ?></p>
<p><strong>Phone</strong><br/><?= $row['r_phone'] ?></p>
<p><strong>Referral Notes</strong><br/><?= $row['notes'] ?></p>
<p><strong>Commission</strong><br/>$<?= number_format( $row['commission'] ) ?></p>
<p><a href="referral_edit.php?id=<?= $_REQUEST['id'] ?>" class="large_link">Edit</a></p>

<? endif ?>
</div>

<? include( "footer.php" ); ?>