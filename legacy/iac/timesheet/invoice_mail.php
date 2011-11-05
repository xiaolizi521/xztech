<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( $p_level != "super-manager" )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page functions
	$id = $_REQUEST['id'];
	
	// FreshBooks API
	$fb = new FreshBooksAPI();
	
	if( $_REUQEST['method'] == "postal" )
	{
		$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<request method=\"invoice.sendBySnailMail\">
		  <invoice_id>$id</invoice_id>
		</request>";
	}
	else
	{
		$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<request method=\"invoice.sendByEmail\">
		  <invoice_id>$id</invoice_id>
		</request>";
	}
	
	$fb_result = $fb->post( $data );
	
	//Creating Instance of the Class
	$xml = new xmlarray($fb_result);
	//Creating Array
	$arrayData = $xml->createArray();
	
	//var_dump( $arrayData );
	
	$result = $arrayData['response']['status'];
		
	// Include header TEMPLATE
	include( "header_template.php" );

?>

<div>
<h1>Invoice Sent</h1>
<p>Invoice #<?= $id ?> was sent via <?= $_REQUEST['method'] == "email" ? "email" : "postal mail" ?>.</p>
<p><a href="invoice_view.php?id=<?= $id ?>" class="large_link">&laquo; Go Back</a></p>
</div>

<? include( "footer.php" ); ?>