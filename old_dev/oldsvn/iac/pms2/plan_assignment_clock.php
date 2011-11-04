<?

session_start();
	
$p_level = $_SESSION['p_level'];

// Can be any level of staff
include( "header_functions.php" );

$id = $_REQUEST['id'];
$pid = $_REQUEST['pid'];
$emp_id = $employeeArray['id'];

// Check if entry exists in database for this task
$db->debug = true;
$db->query( "SELECT * FROM plan_assignment_clock WHERE plan_assignment_id = $id AND employee_id = $emp_id" );

if( $db->result['rows'] > 0 )
{
	$row = mysql_fetch_assoc( $db->result['ref'] );
	
	// If so, delete record, find difference in time, and add to project_task_hours table	
	
	$now = time();		// Time to end clock (time right now)
	$hours = round( floor( ( $now - $row['timestamp_start'] ) / 60 ) / 60, 2 );
	
	$db->query( "DELETE FROM plan_assignment_clock WHERE plan_assignment_id = $id AND employee_id = $emp_id");
	
	$dbarray = array( "plan_assignment_id" => $id, "hours" => $hours, "date" => date( "m/d/Y", $now ), "approved" => "0", "timestamp_start" => $row['timestamp_start'], "timestamp_end" => "$now", "employee_id" => $emp_id, "notes" => 'none' );
	if( $hours > 0 ) $db->add( "plan_assignment_hours", $dbarray );
}
else
{
	// Check that there is not a clock activated for that employee (they may only have ONE clock active at any given time)
	$db->query( "SELECT plan_assignment_clock.id
	FROM plan_assignment_clock 
	INNER JOIN plan_assignment ON plan_assignment_clock.plan_assignment_id = plan_assignment.id
	INNER JOIN plan_assignment_employees ON plan_assignment_employees.plan_assignment_id = plan_assignment.id
	WHERE plan_assignment_employees.employee_id = ".$employeeArray['id'] );
	if( !( $db->result['rows'] > 0 ) )
	{
		$now = time();
		$db->add( "plan_assignment_clock", array( "employee_id" => $emp_id, "plan_assignment_id" => $id, "timestamp_start" => $now, "key" => generateKey( 32 ) ) );	
	
		// Update status to in progress
		$db->update( "plan_assignment", array( "status" => "in-progress" ) , array( "id" => $id ) );
		$db->update( "client_plan", array( "status" => "in-progress" ) , array( "id" => $pid ) );

	}

}
// If not, create a new record

// Done! Return to sender
$pid = $_REQUEST['pid'];

if( $_REQUEST['ret'] == "pm" )
	header( "Location: plan_manage.php?id=$pid" );
	//echo 'here';
elseif( $_REQUEST['ret'] == "home" )
	header( "Location: home.php" );
	//echo 'here';
else
	header( "Location: plan_assignment.php?id=$id" );
	//echo 'here';


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