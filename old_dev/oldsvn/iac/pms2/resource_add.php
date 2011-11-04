<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	include( "header_functions.php" );
	
	

	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of New Project -->
<div>
<h1>Add Resource Link</h1>
<form action="resources.php" method="post">
<table cellpadding="5">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" size="50" /></td>
	</tr>
	<tr>
		 <td>URL:</td>
		 <td><input type="text" name="link" size="100" /></td>
	</tr>
	<tr>
		 <td colspan="2" align="right"><input type="submit" value="Submit" /></td>
	</tr>
</table>
</form>
</div>
<!-- End of New Project -->

<? include( "footer.php" ); ?>