<?
	set_time_limit( ini_get('max_execution_time') );
	
	require( "code/config.php" );
	require( "code/XMLGenerator.php" );
	
	// FreshBooks API
	$fb = new FreshBooksAPI();
	$db = new db();
	
	$page = 0;
	$resultArrays = array();
	
	do
	{
		$page++;
		//$resultArrays = array();
		
		//echo $data;
		
		$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request method=\"client.list\">
	  			<page>$page</page>
				<per_page>100</per_page>
			</request>";
	
		$fb_result = $fb->post( $data );
	
		//Creating Instance of the Class
		$xml = new xmlarray($fb_result);
		//Creating Array
		$arrayData = $xml->createArray();
	
		//var_dump( $arrayData );
		$resultArrays[] = $arrayData['response']['clients'][0]['client'];
	}
	while( count( $arrayData['response']['clients'][0]['client'] ) == 100 );
		
	// Set all clients currently in DB to 1
	$db->updateNoID( "client", array( "deleted_check" => "1" ) );
	
	foreach( $resultArrays as $clientListArray )
	{	
		foreach( $clientListArray as $row )
		{
		
			$client = array( "id" => $row['client_id'],
							  "username" => $row['username'],
							  "first_name" => $row['first_name'],
							  "last_name" => $row['last_name'],
							  "organization" => $row['organization'],
						     "email" => $row['email'],
						 	  "deleted" => "0",
						 	  "deleted_check" => "0" );
						

			// Check if client exists in database; if not, create
			$db->getID( "client", $client['id'] );
		
			if( $db->result['rows'] > 0 )
			{
				// UPDATE client
				$db->update( "client", $client, array( "id" => $client['id'] ) );
				//echo "UPDATE<br>";
			}
			else
			{
				// CREATE client
				$db->add( "client", $client );
				//echo "ADD<br>";
			}		
		}
	}
	// Find clients with 1, those are deleted - mark that
	$db->update( "client", array( "deleted" => "1" ), array( "deleted_check" => "1" ) );
	
	// Finally, set everything back to 0
	$db->updateNoID( "client", array( "deleted_check" => "0" ) );
	
	// Get updated list of clients
	$db->query( "SELECT * FROM client WHERE deleted != 1 ORDER BY organization" );
	
	$db2 = new db();
	
	$count = 1;
	
	while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
	{
		$tempArray = $fb->invoiceList( array( "client_id" => $row['id'], "date_from" => "2008-01-01", "per_page" => 999 ) );
		$invoice_total = 0;
		
		if( count( $tempArray['response']['invoices'][0]['invoice'] ) > 0 )
		{
			foreach( $tempArray['response']['invoices'][0]['invoice'] as $invoice )
			{
				$invoice_total += $invoice['amount'];
			}
		}
		
		$db2->update( "client", array( "invoice_totals" => $invoice_total ), array( "id" => $row['id'] ) );
		
		print "#$count: Updated " . $row['organization'] . " - $" . $invoice_total . "\n";
		
		$count++;		
	}

?>