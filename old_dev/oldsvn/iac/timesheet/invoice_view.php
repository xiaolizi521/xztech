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
	
	$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<request method=\"invoice.get\">
	  <invoice_id>$id</invoice_id>
	</request>";
	$fb_result = $fb->post( $data );
	
	//Creating Instance of the Class
	$xml = new xmlarray($fb_result);
	//Creating Array
	$arrayData = $xml->createArray();
	
	//var_dump( $arrayData );
	
	$row = $arrayData['response']['invoice'][0];
	
	//var_dump( $row );
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
?>

<!-- Start of Invoice -->
<div>
<h1>View Invoice (#<?= $row['invoice_id'] ?>)</h1>
<p><strong>Client:</strong> <a href="client_manage.php?id=<?= $row['client_id'] ?>"><?= $row['organization'] ?></a></p>
</div>

<div style="padding-top: 15px; padding-bottom: 5px;">

	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Task</td>
			<td>Description</td>
			<td>Rate</td>
			<td>Quantity</td>
			<td>Total</td>
		</tr>
		<? for( $i = 0; $i < sizeof( $row['lines'][0]['line'] ); $i++ ): ?>
		<tr class="table_row">
			<td><?= $row['lines'][0]['line'][$i]['name'] ?></td>
			<td><?= $row['lines'][0]['line'][$i]['description'] ?></td>
			<td>$<?= number_format( $row['lines'][0]['line'][$i]['unit_cost'], 2, ".", "," ) ?></td>
			<td><?= $row['lines'][0]['line'][$i]['quantity'] ?></td>
			<td>$<?= number_format( $row['lines'][0]['line'][$i]['quantity'] * $row['lines'][0]['line'][$i]['unit_cost'], 2, ".", "," ) ?></td>
		</tr>
		<? $subtotal += $row['lines'][0]['line'][$i]['quantity'] * $row['lines'][0]['line'][$i]['unit_cost']; ?>
		<? endfor; ?>
		<tr>
			<td colspan="5"></td>
		</tr>
		<tr>
			<td align="right" colspan="4">
				<em>Subtotal:</em>
			</td>
			<td align="left">
				$<?= number_format( $subtotal, 2, ".", "," ) ?>
			</td>
		</tr>
		<tr>
			<td align="right" colspan="4">
				<em>Tax:</em>
			</td>
			<td align="left">
				$<?= number_format( $row['amount'] - $subtotal, 2, ".", "," ) ?>
			</td>
		</tr>
		<tr>
			<td align="right" colspan="4">
				<strong>Total:</strong>
			</td>
			<td align="left">
				$<strong><?= number_format( $row['amount'], 2, ".", "," ) ?></strong>
			</td>
		</tr>
	</table>
</div>

<div style="text-align: left; padding-bottom: 5px; border-top: 3px solid #DDD; padding-top: 5px;">
	<table cellpadding="5" border="0">
		<tr>
			<td valign="top"><strong>Terms:</strong></td>
			<td><?= $row['terms'] ?></td>
		</tr>
		<tr>
			<td valign="top"><strong>Notes:</strong></td>
			<td><?= $row['notes'] ?></td>
		</tr>
	</table>
</div>

<!-- End of Invoice -->

<div style="padding-top: 15px;">
	<p><a href="invoice_mail.php?id=<?= $id ?>&method=email" class="large_link">Send Invoice by Email &raquo;</a></p>
	<p><a href="invoice_mail.php?id=<?= $id ?>&method=postal" class="large_link">Send Invoice by Postal Mail &raquo;</a></p>
</div>

<? include( "footer.php" ); ?>