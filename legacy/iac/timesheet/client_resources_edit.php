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

	if (isset($_GET['mode'])){
		if ($_GET['mode'] == 'delete'){
			$db->delete( "client_resources", array( "id" => $_GET['id'] ) );
			header( "Location: client_resources.php" );
		}
	}

	if( count( $_POST ) > 0 )
	{
		$array = array( "id" => $_POST['id'],
						"url" => $_POST['url'],
						"username" => $_POST['username'],
						"password" => $_POST['password'],
						"notes" => $_POST['notes'],
						"client_id" => $_POST['client_id'],
						"resource_name" => $_POST['resourceName']);

		$db->update( "client_resources", $array, array( "id" => $_POST['id'] ) );
		header( "Location: client_resources.php" );
	}

	// Page specific functions
	$db->query( "SELECT c.id as c_id, c.resource_name as c_resource_name, c.url as c_url, c.username as c_username, c.password as c_password, c.notes as c_notes, d.organization as d_organization, d.id FROM client_resources c, client d WHERE c.client_id = d.id AND c.id = " . $_REQUEST['id'] );


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
<? if( $db->result['rows'] > 0 ): ?>
<? $row = mysql_fetch_assoc( $db->result['ref'] ) ?>
<? $currentClient = $row['d_organization']; ?>

<form action="client_resources_edit.php?id=<?= $row['c_id'] ?>" method="post">
<input type="hidden" name="id" value="<?= $row['c_id'] ?>">

<p>
	<strong>Resource Name</strong><br/>
	<input size="60" maxlength="60" name="resourceName" value="<?= $row['c_resource_name'] ?>">
</p>
<p>
	<strong>URL</strong><br/>
	<input size="60" name="url" value="<?= $row['c_url'] ?>">
</p>
<p>
	<strong>Username</strong><br/>
	<input name="username" value="<?= $row['c_username'] ?>">
</p>
<p>
	<strong>Password</strong><br/>
	<input name="password" value="<?= $row['c_password'] ?>">
</p>
<p>
	<strong>Notes</strong><br/>
	<textarea name="notes" style="width: 400px; height: 150px;"><?= $row['c_notes'] ?></textarea>
</p>
<p>
	<strong>Client</strong><br/>
	<select name="client_id">
		<? $db->query( "SELECT a.id as a_id, a.organization as a_organization FROM client a WHERE deleted = 0 ORDER BY a.organization" ); ?>
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<option value="<?= $row['a_id'] ?>" <?= is_selected( $currentClient, $row['a_organization'] ) ?> ><?= $row['a_organization'] ?></option>
		<? endwhile; ?>
	</select>
</p>
<p><input type="submit" value="Save"></p>

</form>

<? endif ?>
</div>

<? include( "footer.php" ); ?>