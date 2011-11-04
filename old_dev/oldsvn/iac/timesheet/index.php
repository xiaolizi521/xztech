<? include( "header_index.php" ); ?>

<? if( $success != true ): ?>
<!-- Start of Employee Login -->
<div align="center">
	<h1>Project Management System</h1>
	<form method="post" action="index.php">
	<?= $error == "credentials" ? "<p class=\"color_red\">Username or password incorrect</p>" : ""; ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table" align="center">
		<tr>
			<td><strong>Username:</strong></td>
			<td><input type="text" name="username"></td>
		</tr>
		<tr>
			<td><strong>Password:</strong></td>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" value="Log In &raquo;"></td>
		</tr>
	</table>
	</form>
</div>
<!-- End of Employee Login -->
<? else: ?>
<!-- Start of Employee Login -->
<div align="center">
	<h1>Success!</h1>
	<p><a href="home.php">Click here if you are not redirected automatically &raquo;</a></p>
</div>
<!-- End of Employee Login -->
<? endif; ?>

<? include( "footer.php" ); ?>