<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	if( $p_level != "super-manager" && $p_levelType != 'human resources' )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Check to see if anything was posted from a form
	$post = $_POST;
	if( sizeof( $post ) > 0 )
		if( checklen( $post ) )
		{
			// Check for duplicates
			$array = array( "username" => $post['username'] );
			$db->get( "employee", $array );
			if( $db->result['rows'] > 0 )
				$dup_username_error = true;
			else
			{
				$array = array( "username" => $post['username'],
								 "password" => md5( $post['password'] ),
								 "name" => $post['name'],
								 "email" => $post['email'],
								 "email_paypal" => $post['email_paypal'],
								 "telephone" => $post['telephone'],
								 "permission" => $post['permission'],
								 "rate" => $post['rate'],
								 "type" => "reports",
								 "payment_reference" => $post['payment_reference'],
								 "manager_id" => "0" );

				$db->add( "employee", $array );
				header( "Location: employees.php" );
			}
		}
		else
		{
			$error = true;
		}

	function checklen( $data )
	{
		foreach( $data as $key => $value )
		{
			if( strlen( $value ) < 1 )
				return false;
		}
		
		return true;
	}
	// Start of page-specific actions
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>

<!-- Start of New Employee -->
<div>
<h1>Add Employee (Reports Only)</h1>
<form action="employees_addreports.php" method="post">
<? if( $error ) echo "<p class=\"color_red\">All fields must be completed.</p>"; ?>
<? if( $dup_username_error ) echo "<p class=\"color_red\">That username has already been used.</p>"; ?>

<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="30" value="<?= $post['name'] ?>"></td>
	</tr>
	<tr>
		<td>Email:</td>
		<td><input type="text" name="email" size="30" value="<?= $post['email'] ?>"></td>
	</tr>
	<tr>
		<td>Telephone:</td>
		<td><input type="text" name="telephone" size="30" value="<?= $post['telephone'] ?>"></td>
	</tr>
	<tr>
		<td>Username:</td>
		<td><input type="text" name="username" size="30" value="<?= $post['username'] ?>"></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="text" name="password" size="30" value="<?= $post['password'] ?>"></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Add Employee &raquo;"></td>
	</tr>
</table>
</form>
</div>
<? include( "footer.php" ); ?>