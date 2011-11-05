<?

session_start();
include( "header_functions.php" );

$action = $_GET['action'];
$employee = $employeeArray['id'];

if( $action == "getKey" )
{
	// start with a blank password
	$key = "";

	// define possible characters
	$possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 

	// set up a counter
	$i = 0; 

	// add random characters to $password until $length is reached
	while ( $i < 64 )
	{ 

	  	// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

	    // we don't want this character if it's already in the password
	    if ( !strstr( $password, $char ) ) 
		{ 
	      	$key .= $char;
	      	$i++;
	    }
	}
	
	echo json_encode( array( "key" => $key ) );	
}
elseif( $action == "getTasks" )
{	
	//$db->query( "SELECT project_task.name, project_task.id, project_task.status, employee.name as employee FROM project_task LEFT JOIN employee ON project_task.employee_id = employee.id WHERE project_id = ".$_GET['project'] . " AND project_task.status != \"completed\" AND project_task.employee_id = $employee ORDER BY name ASC");
	//$db->debug = true;
	if( $employeeArray['type'] == "super-manager" )
		$db->query( "SELECT plan_assignment.name,
     			plan_assignment.id,
     			plan_assignment.status,
     			employee.name as employee
				FROM plan_assignment
  				LEFT JOIN plan_assignment_employees ON plan_assignment_employees.plan_assignment_id = plan_assignment.id
  				LEFT JOIN employee ON plan_assignment_employees.employee_id = employee.id
  				WHERE plan_assignment.plan_id = ".$_GET['project']." AND plan_assignment.status != 'completed'
  				AND plan_assignment_employees.employee_id = $employee ORDER BY name ASC;");
	else
		$db->query( "SELECT plan_assignment.id as assignment_id, plan_assignment.name as name FROM plan_assignment 
					LEFT JOIN plan_assignment_employees ON plan_assignment_employees.plan_assignment_id = plan_assignment.id
					WHERE plan_assignment.plan_id = ".$_GET['project']." AND plan_assignment_employees.employee_id = ".$employee . " AND plan_assignment.status != \"completed\" ORDER BY plan_assignment.name ASC" );
	
	$result = array();
	
	if( $db->result['rows'] > 0 )
	{
		while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
		{
			if( $employeeArray['type'] == "super-manager" )
				$line = array( "name" => stripslashes( $row['name'] ) . " (" . $row['employee'] . ")", "value" => $row['id'] );
			else
				$line = array( "name" => stripslashes( $row['name'] ), "value" => $row['assignment_id'] );
				
			array_push( $result, $line );
		}
	}
	
	//print_r( $result );
	
	echo json_encode( $result );
}
elseif( $action == "logTime" )
{
	// Delete timers from this employee
	$db->query( "DELETE FROM project_timer WHERE employee_id = ".$employee );
	
	$array = array( "plan_assignment_id" => $_GET['task'],
					"hours" => $_GET['hours'],
					"notes" => $_GET['notes'],
					"key" => $_GET['key'],
					"date" => date("m/d/Y"),
					"timestamp_start" => time() - ( 60 * 60 * $_GET['hours'] ),
					"timestamp_end" => time(),
					"employee_id" => $employee );
	
	if( strlen( $_GET['key'] ) > 0 )
	{
		$db->query( "SELECT COUNT(*) AS entries FROM plan_assignment_hours WHERE `key` = '".$_GET['key']."'" );
		$row = mysql_fetch_assoc( $db->result['ref'] );
		if( $row['entries'] == 0 )
		{
			$db->add( "plan_assignment_hours", $array );
			$db->update( "client_plan", array( "status" => "in-progress" ) , array( "id" => $_GET['project'] ) );
			$db->update( "plan_assignment", array( "status" => "in-progress" ) , array( "id" => $_GET['task'] ) );
			echo json_encode( array( "result" => "success" ) );
			exit();
		}
		else
		{
			echo json_encode( array( "result" => "failure - too many entries (".$row['entries'] ).") for key" );
			exit();
		}
	}

	echo json_encode( array( "result" => "failure - no key sent" ) );	
}
elseif( $action == "statusUpdate" )
{
	// Delete entries older than five minutes
	$currentTime = mktime();
	$expiredTime = mktime() - 60 * 5;
	$employee_id = $_GET['employee'];
	if( strlen( $employee_id ) == 0 )
		$employee_id = $employeeArray['id'];
	
	// Delete expired timers
	$db->query( "DELETE FROM project_timer WHERE timestamp < $expiredTime" );
	
	// Delete timers from this employee
	$db->query( "DELETE FROM project_timer WHERE employee_id = ".$employee_id );
		
	$db->query( "select plan_assignment.name as task_name,
  			client.organization as project_name
  			from plan_assignment
  			left join client_plan on plan_assignment.plan_id = client_plan.id
  			left join client on client_plan.client_id = client.id
  			WHERE plan_assignment.id = ".$_GET['task'] );
	
	if( $db->result['rows'] == 1 )
	{
		$result = mysql_fetch_assoc( $db->result['ref'] );

		$array = array( "employee_id" => $employee,
						"timestamp" => $currentTime,
						"task_id" => $_GET['task'],
						"task_name" => $result['task_name'],
						"description" => $employeeArray['name']." is working on ".$result['task_name']." for ".$result['project_name'].".",
						"hours" => $_GET['hours'] );
	}
	else
	{
		$array = array( "employee_id" => $employee,
						"timestamp" => $currentTime,
						"task_id" => "-1",
						"task_name" => "",
						"description" => $employeeArray['name']." is working but has not selected a project and task.",
						"hours" => $_GET['hours'] );
	}

	if( $employee_id > -1 && strlen( $employeeArray['name'] ) > 0 )
		$db->add( "project_timer", $array );
}

?>