<? include( "header_index.php" ); ?>

<? if( $success != true ): ?>
<!-- Start of Employee Login -->
<div align="center">
	<h1>Project Management System</h1>	
	<form method="post" action="index.php">
	<?= $error == "credentials" ? "<p class=\"color_red\">Username or password incorrect</p>" : ""; ?>
	<table cellpadding="4" cellspacing="4" border="0" width="500" align="center">
		<tr>
			<td style="color:red;"><strong>Sorry the PMS system is down for maintenance</strong></td>
			
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