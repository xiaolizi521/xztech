<?

session_start();
	
$p_level = $_SESSION['p_level'];

// Can be any level of staff
include( "header_functions.php" );

$id = $_REQUEST['id'];
$pid = $_REQUEST['pid'];

// Check if entry exists in database for this task
$db->query( "SELECT * FROM project_task_clock WHERE task_id = $id" );

if( $db->result['rows'] > 0 )
{
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	// If so, delete record, find difference in time, and add to project_task_hours table	
	
	$now = time();		// Time to end clock (time right now)
	$hours = round( floor( ( $now - $row['timestamp_start'] ) / 60 ) / 60, 2 );
	
	$db->query( "DELETE FROM project_task_clock WHERE task_id = $id");
	
	$dbarray = array( "project_task_id" => $id, "hours" => $hours, "date" => date( "m/d/Y", $now ), "approved" => "0", "timestamp_start" => $row['timestamp_start'], "timestamp_end" => "$now" );
	if( $hours > 0 ) $db->add( "project_task_hours", $dbarray );
}
else
{
	// Check that there is not a clock activated for that employee (they may only have ONE clock active at any given time)
	$db->query( "SELECT project_task_clock.id
	FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
	WHERE project_task.employee_id = ".$employeeArray['id'] );
	if( !( $db->result['rows'] > 0 ) )
	{
		$now = time();
		$db->add( "project_task_clock", array( "task_id" => $id, "timestamp_start" => $now, "key" => generateKey( 32 ) ) );	
	
		// Update status to in progress
		$db->update( "project_task", array( "status" => "in-progress" ) , array( "id" => $id ) );
		$db->update( "project", array( "status" => "in-progress" ) , array( "id" => $pid ) );

	}

}
// If not, create a new record

// Done! Return to sender
$pid = $_REQUEST['pid'];

if( $_REQUEST['ret'] == "pm" )
	header( "Location: project_manage.php?id=$pid" );
elseif( $_REQUEST['ret'] == "home" )
	header( "Location: home.php" );
else
	header( "Location: project_task.php?id=$id" );


function generateKey( $length )
{
	$keys = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	for( $i = 1; $i < $length + 1; $i++ )
	{
 		$key .= substr( $keys, rand( 0, strlen( $keys) ), 1 );
	}
		

	return $key;
}

?>