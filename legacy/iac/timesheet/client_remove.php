<?
	session_start();
		
	include( "header_functions.php" );
	
	if( $p_level != "super-manager" )
	{
		header( "Location: home.php" );
		exit();
	}
		
	// FreshBooks API
	$fb = new FreshBooksAPI();
	
	$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<request method=\"client.delete\">
	  <client_id>".$_REQUEST['id']."</client_id>
	</request>";
	$fb_result = $fb->post( $data );
	
	header( "Location: clients.php" );
?>