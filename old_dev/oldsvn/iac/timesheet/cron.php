<?

include( "code/config.php" );

echo "Time Sheet (v1.0)\n";
echo "Written by Cory Becker\n";
echo "Becker Web Solutions, LLC\n";
echo "---------------------------------------------\n";
echo "Initializing...\t\t\t";
$db = new db();
echo "DONE\n";
// Check if timers are active
echo "Checking for active timers...\t";

$db->query( "DELETE FROM project_task WHERE employee_id = 0" );

$db->query( "SELECT project_task_clock.timestamp_notified, 
	project_task_clock.timestamp_start, 
	project_task_clock.task_id, 
	project_task_clock.`key`,
	project_task_clock.id, 
	project_task.name as task_name,
	employee.email, 
	employee.name as employee_name
FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
	 INNER JOIN employee ON project_task.employee_id = employee.id" );
echo "DONE\n\n";

$cTime = time();

$startNoticeHour = 1;	// begin sending clock notice
$incrementalNoticeHour = 6;	// wait x hours before sending another notice
$stopClockHours = 24;	// automatically stop clock if it reaches this point

$db2 = new db();

while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
{
	//var_dump( $row );
	if( strlen( $row['timestamp_notified'] ) > 0 )
		echo $row['employee_name']."\t".$row['task_name']."\t".date( "M d, Y", $row['timestamp_start'] )."\t".number_format( ( ( $cTime - $row['timestamp_start'] ) / 3600 ), 2 )." hours"."\t".number_format( ( ( $cTime - $row['timestamp_notified'] ) / 3600 ), 2 )." hours\n";
	else
		echo $row['employee_name']."\t".$row['task_name']."\t".date( "M d, Y", $row['timestamp_start'] )."\t".number_format( ( ( $cTime - $row['timestamp_start'] ) / 3600 ), 2 )." hours\n";
	
	if( $cTime - $row['timestamp_start'] > $startNoticeHour*60*60 )
	{
		if( $cTime - $row['timestamp_start'] > $stopClockHours*60*60 )
		{
			//echo "OVER hours";
			// Over 24 hours, stop clock and record time
			// If so, delete record, find difference in time, and add to project_task_hours table	

			$now = time();		// Time to end clock (time right now)
			$hours = round( floor( ( $now - $row['timestamp_start'] ) / 60 ) / 60, 2 );

			$db2->query( "DELETE FROM project_task_clock WHERE id = ".$row['id'] );

			$dbarray = array( "project_task_id" => $row['task_id'], "hours" => $hours, "date" => date( "m/d/Y", $now ), "approved" => "0", "timestamp_start" => $row['timestamp_start'], "timestamp_end" => "$now" );
			if( $hours > 0 ) 
				$db2->add( "project_task_hours", $dbarray );
		}
		elseif( $cTime - $row['timestamp_notified'] > $incrementalNoticeHour*60*60 || strlen( $row['timestamp_notified'] ) == 0 )
		{
			$db2->update( "project_task_clock", array( "timestamp_notified" => $cTime ), array( "id" => $row['id'] ) );
			sendNotice( $row['email'], $row['employee_name'], $row['task_name'], number_format( ( ( $cTime - $row['timestamp_start'] ) / 3600 ), 2 ), $row['key'] );
		}
	}
}

echo "\n\n";

function sendNotice( $email, $name, $taskName, $hours, $clockKey )
{
	global $base_url;
		
	$body = "<html>	
					<head>
						<style>	
							font-family: Verdana, Arial, sans-serif;
						</style>
					</head>
					<body>
						<p>".$name.",</p>
						<p><strong>You are \"clocked in\" for the following task:</strong></p>
						<p>".$taskName." ($hours hours)</p>
						<p><strong>To stop the clock, click the link below:</strong></p>
						<p><a href=\"$base_url/project_clock_stop.php?key=".$clockKey."\">$base_url/project_clock_stop.php?key=".$clockKey."</a></p>
						<p>Thanks,<br>IAC Professionals</p>
					</body>
				</html>";
				
	sendmail( $email, "Currently \"Clocked In\"", $body, "IAC Professionals", "info@iacprofessionals.com" );
}

?>