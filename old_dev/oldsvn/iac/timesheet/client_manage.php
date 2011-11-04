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

	// Check if client exists in database
	$db->getID( "client", $_REQUEST['id'] );
	if( $db->result['rows'] < 1 )
		header( "Location: client.php" );
		
	// FreshBooks API
	$fb = new FreshBooksAPI();
	
	$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<request method=\"client.get\">
	  <client_id>".$_REQUEST['id']."</client_id>
	</request>";
	$fb_result = $fb->post( $data );
	
	//Creating Instance of the Class
	$xml = new xmlarray($fb_result );
	//Creating Array
	$arrayData = $xml->createArray();
	
	//var_dump( $arrayData );
	
	$data = $arrayData['response']['client'];
	
	// Get updated list of clients
	$db->getID( "client", $_REQUEST['id'] );
	
	$row = mysql_fetch_assoc( $db->result['ref'] );
	$id = $row['id'];
	$deleted = $row['deleted'];
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	$deleted = 0;
?>

<!-- Start of Client -->
<div style="border-bottom: 3px solid #DDD;">
<h1><? if( strlen( $row['organization'] ) > 0 ) echo $row['organization']; else echo $row['lastname'].", ".$row['firstname']; ?></h1>
<p style="top: -10px; position: relative;"><?= $row['first_name']." ".$row['last_name'] ?> &bull; <?= $row['email'] ?></p>
</div>

<div style="padding-top: 15px;">
<h1>Active Projects</h1>
<?
$db->query( "SELECT * FROM project WHERE client_id = ".$_REQUEST['id']." AND status != 'completed'");	
?>
<? if( $db->result['rows'] > 0 ): ?>
<ul>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<li><a href="project_manage.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></li>
	<? endwhile; ?>
</ul>
<? else: ?>
<em>No projects have been created for this client</em>
<? endif; ?>
</div>

<div style="padding-top: 15px;">
<? if( $deleted == "0" ): ?>
<a href="project_new.php?id=<?= $id ?>" class="large_link">New Project &raquo;</a>
<? else: ?>
<span class="color_red">Projects may not be created for inactive clients</span>
<? endif; ?>
</div>

<div style="padding-top: 15px; padding-bottom: 15px; border-bottom: 3px solid #DDD;">
<h1>Completed Projects</h1>
<?
$db->query( "SELECT * FROM project WHERE client_id = ".$_REQUEST['id']." AND status = 'completed'");
?>
<? if( $db->result['rows'] > 0 ): ?>
<ul>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<li><a href="project_manage.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></li>
	<? endwhile; ?>
</ul>
<? else: ?>
<em>No projects have been completed for this client</em>
<? endif; ?>
</div>

<div style="">
<p><a href="client_remove.php?id=<?= $id ?>" class="large_link">Delete Client</a> | <a href="client_edit.php?id=<?= $id ?>" class="large_link">Edit Client &raquo;</a></p>
</div>
<!-- End of Client -->

<? include( "footer.php" ); ?>