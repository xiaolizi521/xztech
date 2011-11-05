<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];

	if( $p_level != "super-manager" 
		&& $p_levelType != 'manager'
		&& $p_levelType != 'human resources' )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );
		
	// See if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		//$db->debug = true;
		$db->add( "rate_classification" , array( "name" => $post['name'], "description" => $post['desc'], "active" => $post['active'] ) );
		
		header( "Location: rate_classifications.php" );
	}
	

	// Include header TEMPLATE
	include( "header_template.php" );
?>



<div>
<h1>Add Rate Classification</h1>
<form action="rate_classification_add.php" method="post" name="rate">
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input size="50" type="text" name="name" value="" /></td>
	</tr>
	

	<tr>
		<td>Status:</td>
		<td>
			<select name="active" id="status">
				<option value="0">Not Active</option>
				<option value="1">Active</option>
			</select>
		</td>
	</tr>	
	<tr>
		<td valign="top">Description:</td>
		<td><textarea name="desc" cols="60" rows="5"></textarea></td>
	</tr>	
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>



<? include( "footer.php" ); ?>