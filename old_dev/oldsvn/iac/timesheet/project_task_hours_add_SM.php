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
	
	// Check to see if anything was posted
	$post = $_POST;
	if( sizeof( $post ) > 0 )
	{
		if( $post['hours'] != "0" || $post['minutes'] != "0" )
		{
			$hours = ( $post['hours'] * 60 + $post['minutes'] ) / 60;
			
			if( $hours > 0 ) $flag = true;
		}
		else if( $post['time_start'] != "" && $post['time_end'] != "" )
		{	
			$start = getHours( $post['time_start'] );
			$end = getHours( $post['time_end'] );
			$hours = ( $end - $start ) / 60;
			
			if( $hours > 0 ) $flag = true;
		}
		else if( $post['exact_hours'] != "" )
		{
			$hours = $post['exact_hours'];
			
			if( $hours > 0 ) $flag = true;
		}
	}
	
	// Need to do mktime FROM date selected
	$adjusted_start_time = mktime( 0,0,0, $post['month'], $post['day'], $post['year'] );
	$start_time = $adjusted_start_time;
	$end_time = $adjusted_start_time + 60*60*$hours;
	
	// Everything's good. Add it to the database.
	if( $flag )
	{
		$dbarray = array( "project_task_id" => $post['id'], "hours" => $hours, "notes" => $post['notes'], "date" => $post['month']."/".$post['day']."/".$post['year'], "approved" => "0", "timestamp_start" => $start_time, "timestamp_end" => $end_time, "manual_entry_date" => mktime() );
		$db->add( "project_task_hours", $dbarray );
		
		header( "Location: project_manage.php?id=".$post['project_id'] );
	}
	
	
	function getHours( $string )	// HH:MM AM/PM
	{
		$array = split( ":", $string );
		if( sizeof( $array ) == 1 )
		{
			$array[0] = substr( $string, 0, 2 );
			$array[1] = substr( $string, 2, 2 );
		}
		
		$arrayTwo = split( " ", $array[1] );
		
		$arrayFinal = array( $array[0], $arrayTwo[0], $arrayTwo[1] );
	
		if( strtolower( $arrayFinal[2] ) == "pm" && $arrayFinal[0] != 12 );
			$arrayFinal[0] += 12;
			
		$hours = $arrayFinal[0] * 60 + $arrayFinal[1];
		
		return $hours;
	}
	
	// Page specific functions
	if( $_REQUEST['id'] != "" )
	{
		$id = $_REQUEST['id'];
	}
	else
	{
		$id = $post['id'];
	}

	$db->query( "SELECT project_task.name, 
		project_task.description, 
		project_task.notes, 
		project_task.status, 
		project_task.unit, 
		project_task.rate, 
		client.organization,
		employee.name as employee_name,
		employee.id as employee_id,
		client.id as client_id,
		project.name as project,
		project.id as project_id
	FROM project_task INNER JOIN employee ON project_task.employee_id = employee.id
		 INNER JOIN project ON project_task.project_id = project.id
		 INNER JOIN client ON project.client_id = client.id
	WHERE project_task.id = $id"); 

	$row = mysql_fetch_assoc( $db->result['ref'] );	
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>
	
<!-- Start of New Project -->
<div>
<h1>Record Time</h1>
<form action="project_task_hours_add.php" method="post">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">

<table cellpadding="5">
	<tr>
		<td>Task:</td>
		<td><?= $row['name'] ?></td>
	</tr>
	<tr>
		<td valign="top">Time:</td>
		<td style="background: #EEE;">
			<p><em>Use one of the following methods to record time:</em></p>
			<p>
			<strong>#1: </strong> &nbsp; 
			<select name="hours">
				<? for( $i = 0; $i < 50; $i++ ): ?>
				<? if( $i == 1 ) $plural = ""; else $plural = "s"; ?>
				<option value="<?= $i ?>"><?= $i ?> hour<?= $plural ?></option>
				<? endfor; ?>
			</select>
			<select name="minutes">
				<option value="0">0 minutes</option>
				<option value="15">15 minutes</option>
				<option value="30">30 minutes</option>
				<option value="45">45 minutes</option>
			</select>
			</p>
			
			<p>
				<strong>#2:</strong> &nbsp; <input type="text" size="8" name="time_start"> to <input type="text" size="8" name="time_end"> &nbsp; (Examples: <strong>2:36 pm</strong> or <strong>1436</strong>)
			</p>
			
			<p><strong>#3:</strong> &nbsp; <input type="text" size="8" name="exact_hours"> hours</p>
		</td>
	</tr>
	<tr>
		<td>Date:</td>
		<td>
			<?
			$months = array( 	"01" => "January",
								  	"02" => "February",
								  	"03" => "March",
									"04" => "April",
									"05" => "May",
									"06" => "June",
									"07" => "July",
									"08" => "August",
									"09" => "September",
									"10" => "October",
									"11" => "November",
									"12" => "December" );
			
			if( count( $post ) > 0 )
			{
				$date_month = $post["month"];
				$date_day = $post['day'];
				$date_year = $post['year'];
			}
			else
			{
				$date_month = date( "m" );
				$date_day = date( "d" );
				$date_year = date( "Y" );
			}
			?>
		
		<select name="month">
		<? foreach( $months as $numeric => $name ): ?>
			<? if( $numeric == $date_month ): ?>
				<option value="<?= $numeric ?>" selected="yes"><?= $name ?></option>
			<? else: ?>
				<option value="<?= $numeric ?>"><?= $name ?></option>
			<? endif; ?>
		<? endforeach; ?>
		</select>
		
		<select name="day">
		<? for( $i = 1; $i < 32; $i++ ): ?>
			<?
				if( strlen( $i ) == 1 )
					$day = "0".$i;
				else
					$day = $i;
					
				if( $day == $date_day ):
			?>
				<option value="<?= $day ?>" selected="yes"><?= $day ?></option>
			<? else: ?>
				<option value="<?= $day ?>"><?= $day ?></option>
			<? endif; ?>
		<? endfor; ?>
		</select>
		
		<select name="year">
		<? for( $i = $date_year - 1; $i < $date_year + 10; $i++ ): ?>
			<?
				if( strlen( $i ) == 1 )
					$year = "0".$i;
				else
					$year = $i;

				if( $year == $date_year ):
			?>
				<option value="<?= $year ?>" selected="yes"><?= $year ?></option>
			<? else: ?>
				<option value="<?= $year ?>"><?= $year ?></option>
			<? endif; ?>
		<? endfor; ?>
		</select>
	<tr>
		<td valign="top">Notes:</td>
		<td><textarea name="notes" cols="60" rows="5"></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" value="Submit &raquo;"></td>
	</tr>
</table>
</form>
</div>
<!-- End of New Project -->

<? include( "footer.php" ); ?>