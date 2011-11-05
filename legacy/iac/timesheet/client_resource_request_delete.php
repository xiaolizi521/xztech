<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	include( "header_functions.php" );
	
	$mode = $_GET['mode'];
	$resourceId = $_GET['id'];
	
	$db->query( "SELECT a.client_id, a.resource_name, b.organization FROM
				client_resources a, client b where a.client_id = b.id and a.id = $resourceId" );
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	$resourceName = $row['resource_name'];
	$clientName = $row['organization'];
	
	// if mode is delete than send delete request to administrators
	if ( $mode == 'delete' ) {
		// get reason
		$reason = $_GET['message'];
		// create message
		$msg = "<p>User ".$employeeArray['name']." has requested deletion of client resource ".$resourceName;
		$msg .= " from client ".$clientName.". Click <a href=client_resources_edit.php?mode=delete&id=".$resourceId;
		$msg .= "><strong>here</strong></a> to delete.</p>";
		$msg .= "<p>Reason: ".$reason."</p>";
		
		// add message to message table
		$data = array( "date" => time(),
					"project_id" => 0,
					"task_id" => 0,
					"file_id" => 0,
					"filename" => "",
					"message" => $msg,
					"priority" => "normal",
					"sent_by" => $employeeArray['id'],
					"sent_by_name" => $employeeArray['name'] );

		$db->add( "messages", $data );

		// get message id
		$db->query("SELECT LAST_INSERT_ID() AS id");  
		$row = mysql_fetch_assoc( $db->result['ref'] );
		$messageId = $row['id'];
		
		// get administrators and add record to message_recipients
		$db2 = new db();
		$db2->query( "SELECT id FROM employee WHERE type = 'super-manager' AND active = 1" );
		while ( $row2 = mysql_fetch_assoc( $db2->result['ref'] ) ){		
				$msgData = array ( 	"message_id" => $messageId,
									"employee_id" => $row2['id'],
									"status" => 1);	
				$db->add( "message_recipients", $msgData );
		}		
	}
		
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	

<div>
<h1>Request Client Resource Delete</h1>
<form action="client_resource_request_delete.php" method="get">
<input type="hidden" name="mode" value="delete" />
<input type="hidden" name="id" value="<?= $resourceId ?>" />

<table cellpadding="5">
	<? if ( $mode == 'request' ){?>
	<tr>
		<td colspan="2">You have requested the deletion of the following client resource.</td>		
	</tr>
	<tr>
		<td colspan="2">						
			<strong><?= $resourceName ?> for client <?= $clientName ?></strong>		
		</td>
	</tr>
	<tr>
		<td colspan="2">Please state your reason for this request below. An administrator will review the request.</td>
	</tr>

	<tr>
		
		<td colspan="2"><textarea name="message" cols="75" rows="8"></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
	<?}else{?>
	<tr>
		<td colspan="2">Your request has been sent.</td>		
	</tr>
	<?}?>
</table>
</form>
</div>

<? include( "footer.php" ); ?>