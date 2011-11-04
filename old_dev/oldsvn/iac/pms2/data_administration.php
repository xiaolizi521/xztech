<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( strlen( $p_level ) > 0 )
		include( "header_functions.php" );
	else
	{
		header( "Location: home.php" );
		exit();
	}
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	
	
   
?>

<div>
	<h1>Data Administration</h1>
	
	<div style="margin-top: 15px;">
		<ul>
			<li>
				<a href="rate_classifications.php">Rate Classifications &amp; Rates</a>
			</li>
		</ul>
	</div>
</div>

<? include( "footer.php" ); ?>