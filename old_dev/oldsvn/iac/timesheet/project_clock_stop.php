<?

include( "header_index.php" );

$db = new db();
$db->get( "project_task_clock", array( "key" => $_REQUEST['key'] ) );

if( $db->result['rows'] > 0 )
{
	$row = mysql_fetch_assoc( $db->result['ref'] );

	// If so, delete record, find difference in time, and add to project_task_hours table	

	$now = time();		// Time to end clock (time right now)
	$hours = round( floor( ( $now - $row['timestamp_start'] ) / 60 ) / 60, 2 );

	$db->query( "DELETE FROM project_task_clock WHERE `key` = \"".$_REQUEST['key']."\"");

	$dbarray = array( "project_task_id" => $row['task_id'], "hours" => $hours, "date" => date( "m/d/Y", $now ), "approved" => "0", "timestamp_start" => $row['timestamp_start'], "timestamp_end" => "$now" );
	if( $hours > 0 ) 
		$db->add( "project_task_hours", $dbarray );

}


?>

<div>
<h1>Clock Stopped</h1>
<p>The clock has been stopped. Thank you!</p>
<p><a href="index.php"">Click here to login &raquo;</a></p>
</div>

<? include( "footer.php" ); ?>