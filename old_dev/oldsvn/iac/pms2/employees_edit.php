<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	if( $p_level != "super-manager" && $p_levelType != 'human resources')
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );	
		
		
	// Start of page-specific actions
	$db->getID( "employee", $_REQUEST['id'] );
	if( $db->result['rows'] < 1 )
		header( "Location: employees.php" );
	
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	// Check to see if anything was posted from a form
	$post = $_POST;
	if( sizeof( $post ) > 0 )
		if( checklen( $post, array( "permission", "telephone", "email_paypal", "password", "notes" ) ) )
		{
			
		$array = array( "username" => $post['username'],
							 "name" => $post['name'],
							 "email" => $post['email'],
							 "email_paypal" => $post['email_paypal'],
							 "telephone" => $post['telephone'],
							 "permission" => $post['permission'],
							 "rate" => $post['rate'],
							 "type" => $post['type'],
							 "manager_id" => "0",
							 "payment_reference" => $post['payment_reference'],
							 "notes" => $post['notes'],
							 "position" => $post['position'] );
							
			if( strlen( $post['password'] ) > 0 )
				array_push_associative( $array, array( "password" => md5( $post['password'] ) ) );

			$db->update( "employee", $array, array( "id" => $_POST['id'] ) );
				
			header( "Location: employees.php" );
		}
		else
		{
			$row = $post;
			$error = true;
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
<h1>Edit Employee</h1>
<form action="employees_edit.php" method="post">
<? if( $error ) echo "<p class=\"color_red\">All fields must be completed.</p>"; ?>
<? if( $dup_username_error ) echo "<p class=\"color_red\">That username has already been used.</p>"; ?>

<input type="hidden" name="id" value="<?= $row['id'] ?>">
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
		<td>Username:</td>
		<td><input type="text" name="username" size="30" value="<?= $row['username'] ?>"> *</td>
	</tr>
	<tr>
		<td>Default Rate:</td>
		<td>$ <input type="text" name="rate" size="5" value="<?= $row['rate'] ?>"></td>
	</tr>
	<tr>
		<td>Change Password:</td>
		<td><input type="text" name="password" size="30"> <em>optional</em></td>
	</tr>
	<tr>
		<td valign="top">Permissions:</td>
		<td style="background: #EEE;">
			<em>Access level ignored if employee is a manager</em><br>
			<input type="radio" name="permission" value="full" <? if( $row['permission'] == "full" ) echo "checked"; ?> ><strong>Full access</strong></input> - can view rate and most project information<br>
			<input type="radio" name="permission" value="limited" <? if( $row['permission'] == "limited" ) echo "checked"; ?>><strong>Limited access</strong></input> - cannot view rate or other project information<br/>
			<!--
			<input type="radio" name="permission" value="timekeeper" <? if( $row['permission'] == "timekeeper" ) echo "checked"; ?> ><strong>Timekeeper</strong></input> - limited + can approve and import all time<br/> 
			<input type="radio" name="permission" value="manager" <? if( $row['permission'] == "manager" ) echo "checked"; ?> ><strong>Manager</strong></input> - limited + create/edit projects, create/edit tasks<br/> 
			-->
			<input type="radio" name="permission" value="human resources" <? if( $row['permission'] == "human resources" ) echo "checked"; ?> ><strong>Human Resources</strong></input> - limited + create/edit projects, create/edit tasks, create/edit employees<br/>
			<input type="radio" name="permission" value="system administrator" <? if( $row['permission'] == "system administrator" ) echo "checked"; ?> ><strong>System Administrator</strong></input> - limited + can view all files
		</td>
	</tr>
	<tr>
		<td>Type:</td>
		<td>
			<select name="type">
			<?
				$types = array( "Super Manager" => "super-manager", "Employee" => "employee", "Reports Only" => "reports" );
				
				foreach( $types as $key => $value )
				{
					if( $row['type'] == $value )
						echo "<option value=\"$value\" selected=\"true\">$key</option>\n";
					else
						echo "<option value=\"$value\">$key</option>\n";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Position:</td>
		<td>
			<select name="position">
			<?
				$types = array( "Bookkeeper" => "Bookkeeper", "Project VA" => "Project VA", "Dedicated VA" => "Dedicated VA", "Accountant" => "Accountant", "Project Manager" => "Project Manager", "Management" => "Management");
				
				foreach( $types as $key => $value )
				{
					if( $row['type'] == $value )
						echo "<option value=\"$value\" selected=\"true\">$key</option>\n";
					else
						echo "<option value=\"$value\">$key</option>\n";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Payment Reference:</td>
		<td>
			<select name="payment_reference">
			<?
				$types = array( "Direct Deposit" => "direct-deposit", "PayPal" => "paypal", "Check" => "check", "GURU Safe Pay" => "guru", "Other" => "other" );
		
				foreach( $types as $key => $value )
				{
					if( $row['payment_reference'] == $value )
						echo "<option value=\"$value\" selected=\"true\">$key</option>\n";
					else
						echo "<option value=\"$value\">$key</option>\n";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top">Notes:</td>
		<td><textarea name="notes" rows="10" cols="60"><?= stripslashes( $row['notes'] ) ?></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Save Employee &raquo;"></td>
	</tr>
</table>
</form>
</div>

<div style="padding-top: 15px;">
<p><a href="employees_deactivate.php?id=<?= $row['id'] ?>" class="large_link">Deactivate <?= $row['name'] ?>'s Account</a></p>
</div>

<? include( "footer.php" ); ?>