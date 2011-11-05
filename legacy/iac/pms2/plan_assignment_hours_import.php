<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	if( $p_level != "super-manager" && $p_levelType != 'timekeeper' )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	$post = $_POST;
	
	if( strlen( $_REQUEST['hours_id'] ) > 0 )
	{
		$db->query( "SELECT employee.id, 
			employee.name, 
			plan_assignment_hours.hours, 
			plan_assignment_hours.date,
			plan_assignment.rate,
			plan_assignment.id as assignment_id,
			plan_assignment.plan_id as plan_id,
			plan_assignment.name as assignment_name,
			plan_assignment_hours.id as plan_assignment_hours_id,
			plan_assignment_hours.approved,
			plan_assignment_hours.fb_import,
			plan_assignment_hours.notes
			FROM plan_assignment_hours 
		INNER JOIN plan_assignment ON plan_assignment_hours.plan_assignment_id = plan_assignment.id
		INNER JOIN plan_assignment_employees ON plan_assignment_employees.plan_assignment_id = plan_assignment.id AND plan_assignment_employees.employee_id = plan_assignment_hours.employee_id
		 INNER JOIN employee ON plan_assignment_hours.employee_id = employee.id
		WHERE plan_assignment_hours.id = ".$_REQUEST['hours_id'] );
			
		//FROM plan_assignment 
		//INNER JOIN employee ON project_task.employee_id = employee.id
		//INNER JOIN project_task_hours ON project_task_hours.project_task_id = project_task.id
		//WHERE project_task_hours.id = ".$_REQUEST['hours_id'] );
		
		if( $db->result['rows'] > 0 )
			$post['data'] = base64_encode( serialize( mysql_fetch_assoc( $db->result['ref'] ) ) );
		else
		{
			echo "There was an error importing this entry. Contact <a href=\"mailto:cory@beckerwebsolutions.com\">Cory Becker</a>.";
			exit();
		}
	}
	
	if( strlen( $_REQUEST['return'] ) > 0 )
		$post['return'] = $_REQUEST['return'];

	// FreshBooks
	$fb = new FreshBooksAPI();
	
	if( strlen( $post['project'] ) > 0 && strlen( $post['task'] ) > 0 && strlen( $post['data'] ) > 0 )
	{
		$project = unserialize( base64_decode( $post['project'] ) );
		$task = unserialize( base64_decode( $post['task'] ) );
		$data = unserialize( base64_decode( $post['data'] ) );
		$date = str_replace( "/", "", $data['date'] );
		// YYYY-MM-DD
		$date = substr( $date, 4, 4 )."-".substr( $date, 0, 2 )."-".substr( $date, 2, 2 );
		
		$xmldata = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<request method=\"time_entry.create\">
		  <time_entry>
			<project_id>".$project['project_id']."</project_id>
			<task_id>".$task['task_id']."</task_id>
			<hours>".$data['hours']."</hours>
			<notes>".htmlentities( $data['task_name'] )."</notes>
			<date>".$date."</date>
		  </time_entry>		
		</request>";
			
		$fb_result = $fb->post( $xmldata );
		
		$xml = new xmlarray($fb_result);
		
		//Creating Array
		$arrayData = $xml->createArray();
		$entry_id = $arrayData['response']['time_entry_id'];
			
		// id and project_task_hours_id are the same thing		
		$db->update( "project_task_hours", array( "fb_import" => $entry_id, "approved" => "1" ), array( "id" => $data['project_task_hours_id'] ) );
	
		header( "Location: " . base64_decode( $post['return'] ) );
	}
	
	include( "header_template.php" );
?>

<div>
<h1>Import Time to FreshBooks</h1>
<form action="project_task_hours_import.php" method="post">
	<input type="hidden" name="data" value="<?= $post['data'] ?>">
	<input type="hidden" name="return" value="<?= $post['return'] ?>">
	<table cellpadding="5">
		<tr>
			<th>Client/Plan:</th>
			<td>
				<? if( strlen( $post['project'] ) > 0 ): ?>
					<input type="hidden" name="project" value="<?= $post['project'] ?>">
					<? $project = unserialize( base64_decode( $post['project'] ) ) ?>
					<?= $project['name'] ?>
				<? else: ?>
					<?
						$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
						<request method=\"project.list\">
						  <per_page>1000</per_page>
						</request>";
						$fb_result = $fb->post( $data );

						//Creating Instance of the Class
						$xml = new xmlarray($fb_result);
						//Creating Array
						$arrayData = $xml->createArray();
						$projects = $arrayData['response']['projects'][0]['project'];
					?>
					<select name="project">
					<? foreach( $projects as $project ): ?>
					<option value="<?= base64_encode( serialize( $project ) ) ?>"><?= $project['name'] ?></option>
					<? endforeach; ?>
					</select>
				<? endif; ?>
			</td>
		</tr>
		<? if( strlen( $post['project'] ) > 0 ): ?>
		<?
			$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request method=\"task.list\">
			  <project_id>".$project['project_id']."</project_id>
			  <per_page>1000</per_page>
			</request>";
			$fb_result = $fb->post( $data );

			//Creating Instance of the Class
			$xml = new xmlarray($fb_result);
			$arrayData = $xml->createArray();
			count( $arrayData );
			$tasks = $arrayData['response']['tasks'];
			count( $tasks );
		?>
		<tr>
			<th>Task:</th>
			<td>
				<? if( isset( $tasks[0]['task'] ) ): ?>
				<? $tasks = $tasks[0]['task'] ?>
				<select name="task">
					<? foreach( $tasks as $task ): ?>
					<option value="<?= base64_encode( serialize( $task ) ) ?>"><?= $task['name'] ?> ($<?= $task['rate'] ?>)</option>
					<? endforeach; ?>
				</select>
				<? else: ?>
				No tasks are listed for this project in FreshBooks
				<? endif; ?>
			</td>
		</tr>
		<? endif; ?>
	</table>
	<input type="submit" value="Submit">
	
</form>
</div>

<? include( "footer.php" ) ?>