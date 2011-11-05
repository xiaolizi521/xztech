<?

include( "code/config.php" );

if( $employee_report )
{
	$db = new db();

	$days = 1.2;
	$time = mktime() - 60*60*24*$days;

	$db->query( "SELECT project.name AS project_name, 
		project_task_hours.hours, 
		project_task_hours.timestamp_start, 
		project_task.name AS task_name, 
		project_task_hours.approved, 
		employee.name AS employee_name
	FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
		 INNER JOIN project ON project_task.project_id = project.id
		 INNER JOIN employee ON project_task.employee_id = employee.id
	WHERE project_task_hours.timestamp_start > $time
	ORDER BY project_task_hours.timestamp_start DESC" );

	if( $db->result['rows'] > 0 )
	{
	
		$content = "<table cellpadding=\"6\" cellspacing=\"0\">
							<tr class=\"header\">
								<td><strong>Date</strong></td>
								<td><strong>Employee</strong></td>
								<td><strong>Project</strong></td>
								<td><strong>Task</strong></td>
								<td><strong>Hours</strong></td>
							</tr>";
					
		while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
		{
			$content .= "<tr class=\"row\">
								<td>".date( "M. d, Y", $row['timestamp_start'] )."</td>
								<td>".$row['employee_name']."</td>
								<td>".$row['project_name']."</td>
								<td>".$row['task_name']."</td>
								<td>".$row['hours']."</td>
							 </tr>";
		}

		$content .= "<table>";



	}
	else
	{
		$content = "No recent employee activity";
	}

	$body = "<html>	
		<head>
			<style>	
				body { font-family: Verdana, Arial, sans-serif; font-size: medium; }
				table { font-size: medium;	}
				.header { background: #EEE; }
				.header td { padding-right: 20px; }
				.row td { padding-right: 20px; }
			</style>
		</head>
		<body>
			<h1>Employee Activity Report</h1>
			<p><strong>Generated ".date( "M. d, Y" )." at ".date( "g:i A" )."</strong></p>
			<hr>
			$content
		</body>
	</html>";

	echo $body;
	
	sendmail( $employee_report_email, "Employee Activity", $body, "IAC Professionals", "info@iacprofessionals.com" );

}	

?>