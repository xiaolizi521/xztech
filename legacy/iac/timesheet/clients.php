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

	// FreshBooks API
	$fb = new FreshBooksAPI();
	
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
	$db->query( "SELECT * FROM client ORDER BY organization" );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	$deleted = 0;
?>

<!-- Start of Clients -->
<div>
<h1>Active Clients</h1>

<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Organization</td>
		<td>Name</td>
		<td>Email</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<? if( $row['deleted'] == "1" ): ?>
	<? $deleted++; ?>
	<? else: ?>
	<tr class="table_row">
		<td><a href="client_manage.php?id=<?= $row['id'] ?>"><?= $row['organization'] ?></a></td>
		<td><a href="client_manage.php?id=<?= $row['id'] ?>"><?= $row['first_name']." ".$row['last_name'] ?></a></td>
		<td><?= $row['email'] ?></td>
		<td class="link_button"><a href="client_edit.php?id=<?= $row['id'] ?>">Edit Client</a></td>
	</tr>
	<? endif; ?>
	<? endwhile; ?>
</table>

</div>
<!-- End of Clients -->

<div style="padding-top: 15px;">
	<p><? if( $deleted > 0 ): ?><a href="clients_removed.php" class="large_link">Removed Clients (<?= $deleted ?>)</a> | <? endif; ?><a href="client_new.php" class="large_link">New Client &raquo;</a></p>
</div>


<? include( "footer.php" ); ?>