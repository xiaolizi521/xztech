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

	$post = $_POST;
	
	if( sizeof( $_POST ) > 0 )
	{
		//var_dump( $_POST );
		
		// Create XML
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<request method=\"invoice.create\">\n<invoice>";
		$xml .= "\n<client_id>".$post['client_id']."</client_id>";
		$xml .= "\n<notes>".$post['notes']."</notes>";
		$xml .= "\n<terms>".$post['terms']."</terms>";
		$xml .= "\n<lines>\n";
		
		for( $i = 0; $i < count( $post['code'] ); $i++ )
		{
			$xml .= "<line>\n";
			$xml .= "<name>".str_replace( "&", "&amp;", $post['code'][$i] )."</name>\n";
			$xml .= "<description>".str_replace( "&", "&amp;", $post['description'][$i] )."</description>\n";
			$xml .= "<unit_cost>".$post['rate'][$i]."</unit_cost>\n";
			$xml .= "<quantity>".$post['hours'][$i]."</quantity>\n";
			$xml .= "<tax1_name>Tax</tax1_name>\n";
			$xml .= "<tax1_percent>".$post['percent']."</tax1_percent>\n";
			$xml .= "</line>\n";			
		}
		
		$xml .= "</lines>\n</invoice>\n</request>";
				
		// Create Invoice
		$fb = new FreshBooksAPI();
		$result = $fb->post( $xml );
		
		// Process response from XML post
		$xml2 = new xmlarray( $result );		
		$arrayData = $xml2->createArray();
		$invoice_id = $arrayData['response']['invoice_id'];
		
		// Update database to reflect new status
		$db->update( "project", array( "invoice" => $invoice_id ), array( "id" => $post['project_id'] ) );
		
		//echo $xml;
		
		header( "Location: invoice_view.php?id=$invoice_id" );
	}
	// Page specific functions
	$id = $_REQUEST['id'];
	$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status, project.description FROM project, client WHERE project.client_id = client.id AND project.id = $id" );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	$description = $row['description'];
	$client_id = $row['client_id'];
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	function roundHours($Price) {
	 $diff = $Price - floor($Price);
	 if ($diff<=0.25) {
	  $Price = floor($Price) + 0.25;
	 };
	 if ($diff>0.25 && $diff<=0.5) {
	  $Price = floor($Price) + 0.5;
	 };
	 if ($diff>0.5 && $diff<=0.75) {
	  $Price = floor($Price) + 0.75;
	 };
	 if ($diff>0.75) {
	  $Price = floor($Price) + 1;
	 };
	 return $Price;
	}
	
?>

<div style="border-bottom: 3px solid #DDD;">
<h1>Invoice Preview</h1>
<table cellpadding="5" border="0">
	<tr>
		<td><strong>Client:</strong></td>
		<td><?= $row['organization'] ?></td>
	</tr>
	<tr>
		<td><strong>Project:</strong></td>
		<td><?= $row['name'] ?></td>
	</tr>
</table>
</div>

<?
$db->query( "SELECT SUM(project_task_hours.hours) AS hours, 
	project_task.project_id, 
	project_task.task_id, 
	project_task.rate_billable as rate,
	project_task.unit, 
	project_task.name, 
	project_task.description, 
	project_task.status, 
	project_task.id
FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
WHERE project_task.project_id = $id
GROUP BY project_task.project_id, project_task.task_id, project_task.rate, project_task.unit, project_task.name, project_task.description, project_task.status, project_task.id" );

?>


<div style="padding-top: 15px; padding-bottom: 5px;">
<form name="invoice" method="post" action="invoice_preview.php">
<script type="text/javascript">

function updateTotals()
{
	var item_code, item_name, item_description, item_unitcost, item_qty;
	var subtotal = 0, total = 0, tax = 0;
	var current_cost = 0;
	
	for( var i = 0; i < invoice.elements.length - 6; null )
	{
		item_code = invoice.elements[i++].value;
		item_name = invoice.elements[i++].value;
		item_description = invoice.elements[i++].value;
		item_unitcost = invoice.elements[i++].value;
		item_qty = invoice.elements[i++].value;
				
		//alert( item_code + " / " + item_name + " / " + item_description + " / " + item_unitcost + " / " + item_qty );
		
		// Update subtotals
		current_cost = ( Math.round (item_qty * item_unitcost *100)/100 );
		
		invoice.elements[i++].value = current_cost;
		
		subtotal += current_cost;
	}
	
	tax = subtotal * document.getElementById('percent').value / 100;
	total = subtotal + tax;
	
	//alert( document.getElementById('percent').value );
	
	document.getElementById('subtotal').innerHTML = "Subtotal: <strong> $" + subtotal.toFixed(2) + "</strong>";
	document.getElementById('tax').innerHTML = "Tax: <strong> $" + tax.toFixed(2) + "</strong>";
	document.getElementById('total').innerHTML = "Total: <strong> $" + total.toFixed(2) + "</strong>";
	
}

function submit()
{
	invoice.submit();
}

</script>

<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Code</td>
			<td>Task</td>
			<td>Description</td>
			<td>Rate</td>
			<td>Quantity</td>
			<td>Total</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<? $total += $row['hours'] * $row['rate']; ?>
		<tr class="table_row">
			<td valign="top"><input type="text" onChange="updateTotals()" name="code[]" size="10" value="<?= substr( $row['name'], 0, 8 ) ?>"></td>
			<td valign="top"><input type="text" onChange="updateTotals()" name="name[]" size="20" value="<?= $row['name'] ?>"></td>
			<td><textarea cols="30" rows="2" onChange="updateTotals()" name="description[]"><?= $row['description'] ?></textarea></td>
			<td valign="top"><input type="text" onChange="updateTotals()" name="rate[]" size="5" value="<?= $row['rate'] ?>"></td>
			<td valign="top"><input type="text" onChange="updateTotals()" name="hours[]" size="5" value="<?= number_format(  $row['hours'] , 2, '.', ',' ) ?>"></td>
			<td valign="top"><input type="text" name="subtotal[]" disabled="disabled" size="7" value="<?= number_format(  $row['hours'] * $row['rate'], 2, '.', ',' ) ?>"></td>
		</tr>
	<? endwhile; ?>
	</table>
<? else: ?>
<p><em>No current tasks have been added for this project</em></p>
<? endif; ?>
</div>

<div style="text-align: right; padding-bottom: 5px; padding-top: 5px;">
<table>
	<tr>
		<td align="left">
	
			<table cellpadding="5" border="0" align="left" style="margin-right: 20px;">
				<tr>
					<td valign="top">Terms:</td>
					<td><textarea cols="60" rows="1" name="terms">Payment due within 30 days.</textarea>
				</tr>
				<tr>
					<td valign="top">Notes:</td>
					<td><textarea cols="60" rows="7" name="notes"><?= $description ?></textarea>
				</tr>
			</table>
		
		</td>
		<td align="right">
			<p><div id="subtotal">Subtotal: <strong> $<?= $total ?></strong></div></p>
			( <input type="text" size="2" value="0" name="percent" id="percent" onChange="updateTotals()"> %) <div id="tax" style="display: inline;">Tax: <strong>$0.00</strong></div></p>
			<p><div id="total" style="border-top: solid 2px #0079d6; padding: 5px; background: #EEE;">Total: <strong> $<?= $total ?></strong></div>
			<p><input type="button" onClick="submit()" value="Create Invoice &raquo;"></p>
		</td>
	</tr>
</table>
</div>

<input type="hidden" name="project_id" value="<?= $id ?>">
<input type="hidden" name="client_id" value="<?= $client_id ?>">

</form>

<? include( "footer.php" ); ?>