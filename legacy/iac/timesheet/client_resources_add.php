<?
	session_start();

	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];

	if( $p_level != "super-manager" 
		&& $p_levelType != 'timekeeper'
		&& $p_levelType != 'manager'
		&& $p_levelType != 'human resources')
	{
		header( "Location: client_resources.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	if( count( $_POST ) > 0 )
	{
		$array = array( "id" => null,
						"url" => $_POST['url'],
						"username" => $_POST['username'],
						"password" => $_POST['password'],
						"notes" => $_POST['notes'],
						"client_id" => intval($_POST['client_id']),
						"resource_name" => $_POST['resourceName']);

		$db->add( "client_resources", $array );
		header( "Location: client_resources.php" );
	}

	// Page specific functions

	// Include header TEMPLATE
	include( "header_template.php" );

	function is_selected( $var, $check )
	{
		if( $var == $check )
			return 'SELECTED';
	}

?>

<div>
<h1>Client Resource</h1>

<form action="client_resources_add.php" method="post">

<p>
	<strong>Resource Name</strong><br/>
	<input size="60" maxlength="60" name="resourceName" value="">
</p>
<p>
	<strong>URL</strong><br/>
	<input size="60" name="url" value="">
</p>
<p>
	<strong>Username</strong><br/>
	<input name="username" value="">
</p>
<p>
	<strong>Password</strong><br/>
	<input name="password" value="">
</p>
<p>
	<strong>Notes</strong><br/>
	<textarea name="notes" style="width: 400px; height: 150px;"></textarea>
</p>
<p>
	<strong>Client</strong><br/>
	<select name="client_id">
		<? $db->query( "SELECT a.id as a_id, a.organization as a_organization FROM client a WHERE deleted = 0 ORDER BY a.organization" ); ?>
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['a_id'] ?>" ><?= $row['a_organization'] ?></option>
		<? endwhile; ?>
	</select>
</p>
<p><input type="submit" value="Save"></p>

</form>
</div>

<? include( "footer.php" ); ?>