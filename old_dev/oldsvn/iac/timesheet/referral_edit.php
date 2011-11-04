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
		
	if( count( $_POST ) > 0 )
	{
		$array = array( "company" => $_POST['company'],
						"name" => $_POST['name'],
						"phone" => $_POST['phone'],
						"status" => $_POST['status'],
						"email" => $_POST['email'],
						"notes" => $_POST['notes'],
						"deleted" => $_POST['deleted'],
						"commission" => $_POST['commission'] );
						
		$db->update( "referrals", $array, array( "id" => $_REQUEST['id'] ) );
		header( "Location: referrals.php" );
		exit();
	}

	// Page specific functions
	$db->query( "SELECT r.company as r_company, r.id as r_id, r.name as r_name, r.phone as r_phone, r.status as r_status, c.organization, r.created as r_created, r.status, r.notes, r.commission, r.deleted, r.email as r_email FROM referrals AS r LEFT JOIN client AS c ON r.client_id = c.id WHERE r.id = " . $_REQUEST['id'] );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	function is_selected( $var, $check )
	{
		if( $var == $check )
			return 'SELECTED';
	}
	
?>

<div>
<h1>Referral</h1>
<? if( $db->result['rows'] > 0 ): ?>
<? $row = mysql_fetch_assoc( $db->result['ref'] ) ?>

<form action="referral_edit.php?id=<?= $row['r_id'] ?>" method="post">
<p>
	<strong>Status</strong><br/>
	<select name="status">
		<option value="">&nbsp;</option>
		<option value="new" <?= is_selected( "new", $row['r_status'] ) ?>>New</option>
		<option value="in progress" <?= is_selected( "in progress", $row['r_status'] ) ?>>In Progress</option>
		<option value="closed" <?= is_selected( "closed", $row['r_status'] ) ?>>Closed</option>
	</select>
</p>
<p>
	<strong>Company</strong><br/>
	<input name="company" value="<?= $row['r_company'] ?>">
</p>
<p><strong>Referred by</strong><br/><?= $row['organization'] ?></p>
<p><strong>Created</strong><br/><?= date( "M. d, Y", strtotime( $row['r_created'] ) ) ?></p>
<p>
	<strong>Contact</strong><br/>
	<input name="name" value="<?= $row['r_name'] ?>">
</p>
<p>
	<strong>Email</strong><br/>
	<input name="email" value="<?= $row['r_email'] ?>">
</p>
<p>
	<strong>Phone</strong><br/>
	<input name="phone" value="<?= $row['r_phone'] ?>">
</p>
<p>
	<strong>Referral Notes</strong><br/>
	<textarea name="notes" style="width: 400px; height: 150px;"><?= $row['notes'] ?></textarea>
</p>
<p>
	<strong>Commission</strong><br/>
	<input name="commission" value="<?= $row['commission'] ?>">
</p>
<p>
	<strong>Deleted / Hidden Referral?</strong><br/>
	<select name="deleted">
		<option value="0" <?= is_selected( "0", $row['deleted'] ) ?>>No</option>
		<option value="1" <?= is_selected( "1", $row['deleted'] ) ?>>Yes</option>
	</select>
</p>
<p><input type="submit" value="Save"></p>

</form>

<? endif ?>
</div>

<? include( "footer.php" ); ?>