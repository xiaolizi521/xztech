<?
	session_start();
		
	$p_level = $_SESSION['p_level'];

	include( "header_functions.php" );
	
	// Page specific functions
	$db->query( "SELECT * FROM files WHERE id = ".$_REQUEST['id'] );
	$row = mysql_fetch_assoc( $db->result['ref'] );
?>

<div style="padding: 10px;">
<p style="font-weight: bold; margin: 0 0 5px 0; background: #000; color: #FFF; padding: 3px;">Details</p>
<table cellpadding="5" cellspacing="3" border="0" style="font-size: small;">
	<tr>
		<th>Filename</th>
		<td><?= $row['filename'] ?></td>
	</tr>
	<tr>
		<th>Original Filename</th>
		<td><?= $row['original_filename'] ?>
	</tr>
	<tr>
		<th>Size</th>
		<td><?= round( $row['size'] / 1000 ) ?> kb</td>
	</tr>
	<tr>
		<th>Date</th>
		<td><?= date( "m/d/y g:i A", $row['date'] ) ?></td>
	</tr>
	<tr>
		<th>Description</th>
		<td><?= stripslashes( $row['description'] ) ?></td>
	</tr>
	<tr>
		<th>Mime</th>
		<td><?= $row['mime'] ?></td>
	</tr>
	<tr>
		<th>Audio Length</th>
		<td><?= $row['audio_length'] ?> seconds</td>
	</tr>
	<tr>
		<th>Uploaded By</th>
		<td><?= $row['uploaded_by_name'] ?></td>
	</tr>
</table>
</div>
<div style="padding: 10px;">
	<a href="#" class="lbAction" rel="deactivate"><button>Close</button></a>
</div>
