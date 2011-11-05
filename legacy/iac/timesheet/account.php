<?
	session_start();

	include( "header_functions.php" );		
	if( $p_level != "employee" )
	{
		header( "Location: home.php" );
		exit();
	}
	
	// Start of page-specific actions
	$row = $employeeArray;
	// Check to see if anything was posted from a form
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		$row = $post;
		if( checklen( $post, array( "telephone", "email_paypal", "password" ) ) )
		{
			
		$array = array( "name" => $post['name'],
							 "email" => $post['email'],
							 "email_paypal" => $post['email_paypal'],
							 "telephone" => $post['telephone'] );
							
			if( strlen( $post['password'] ) > 0 )
			{
				array_push_associative( $array, array( "password" => md5( $post['password'] ) ) );
				
				//Update password in session
				$_SESSION['password'] = md5( $post['password'] );
			}
			
			$db->update( "employee", $array, array( "id" => $employeeArray['id'] ) );
			header( "Location: home.php" );
		}
		else
		{
			$row = $post;
			$error = true;
		}
	}
	
	function checklen( $dataArg, $exceptionArray )
	{
		foreach( remove_array_key( $dataArg, $exceptionArray ) as $key => $value )
		{
			if( strlen( $value ) < 1 )
				return false;
		}
		
		return true;
	}
	
	function remove_array_key( $array, $searchArray  )
	{
		$newArray = array( "hello" => "test" );
		foreach( $array as $key => $value )
		{
			$ok_to_add = true;
			foreach( $searchArray as $search )
				if( $search == $key )
					$ok_to_add = false;
					
			if( $ok_to_add ) 
				array_push_associative( $newArray, array( $key => $value ) );
		}
		return $newArray;
	}
	
	function array_push_associative(&$arr) {
	   $args = func_get_args();
	   foreach ($args as $arg) {
	       if (is_array($arg)) {
	           foreach ($arg as $key => $value) {
	               $arr[$key] = $value;
	               $ret++;
	           }
	       }else{
	           $arr[$arg] = "";
	       }
	   }
	   return $ret;
	}
	
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>

<!-- Start of New Employee -->
<div>
<h1>My Account</h1>
<form action="account.php" method="post">
<? if( $error ) echo "<p class=\"color_red\">All fields must be completed.</p>"; ?>
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="30" value="<?= $row['name'] ?>"> *</td>
	</tr>
	<tr>
		<td>Email:</td>
		<td><input type="text" name="email" size="30" value="<?= $row['email'] ?>"> *</td>
	</tr>
	<tr>
		<td>PayPal Email:</td>
		<td><input type="text" name="email_paypal" size="30" value="<?= $row['email_paypal'] ?>"></td>
	</tr>
	<tr>
		<td>Telephone:</td>
		<td><input type="text" name="telephone" size="30" value="<?= $row['telephone'] ?>"></td>
	</tr>	
	<tr>
		<td>Change Password:</td>
		<td><input type="text" name="password" size="30"> <em>optional</em></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" value="Update Account"></td>
	</tr>
</table>
</form>
</div>

<? include( "footer.php" ); ?>