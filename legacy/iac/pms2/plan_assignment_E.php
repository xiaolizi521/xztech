<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	
	if( $p_level != "employee" )
	{
		header( "Location: home.php" );
		exit();
	}
	else
		include( "header_functions.php" );

	// Page specific functions
	$id = $_REQUEST['id'];

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
		project.id as project_id,
		project_task.time_limit,
		project_task.deadline
	FROM project_task INNER JOIN employee ON project_task.employee_id = employee.id
		 INNER JOIN project ON project_task.project_id = project.id
		 INNER JOIN client ON project.client_id = client.id
	WHERE project_task.id = $id" ); 

	$row = mysql_fetch_assoc( $db->result['ref'] );
	$rate = $row['rate'];
	$project_id = $row['project_id'];
	$task_id = $id;
	$status = $row['status'];
	
	// Check to make sure employee can check this - is assigned task
	if( $row['employee_id'] != $employeeArray['id'] )
		header( "Location: home.php" );
	
	// Project Clock
	$db2 = new db();
	$db2->query( "SELECT project_task.employee_id, 
		project_task_clock.timestamp_start, 
		project_task_clock.id
	FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
	WHERE project_task.employee_id = ".$employeeArray['id'] );

	$timer = mysql_fetch_assoc( $db2->result['ref'] );
	$start = $timer['timestamp_start'];
	$now = time();
	$min = round( floor( ( $now - $start ) / 60 ) / 60, 2 );
	
	if( $db2->result['rows'] > 0 )
		$body = " onload=\"Reset(); Start();\"";
		
	function padit( $numb, $nlen) {
		$str="".$numb ;
		while (strlen( $str ) < $nlen) {
			$str="0".$str;
		}
		return $str;
	}
?>
<html>
	<head>
		<title>Time Sheets: IAC Professionals</title>
		<link href="style/style.css" rel="stylesheet" type="text/css" />
		<script language="JavaScript">

		<!--
		// please keep these lines on when you copy the source
		// made by: Nicolas - http://www.javascript-page.com

		var timerID = 0;
		var tStart  = null;
		var now = <?= time() ?>;
		var start = <?= $start ?>;
		var hours = 0;
		var minutes = 0;
		var seconds = 0;
		
		function UpdateTimer() {
		   if(timerID) {
		      clearTimeout(timerID);
		      clockID  = 0;
		   }

			now++;
			
			hours =  ( Math.floor( ( now - start ) / 3600 ) ) % 60;
			minutes = ( Math.floor( ( now - start ) / 60 ) ) % 60;
			seconds = ( now - start ) % 60;
			
			hours = padit( hours, 2 );
			minutes = padit( minutes, 2 );
			seconds = padit( seconds, 2 );
			
		   document.getElementById("hours").innerHTML = "Elapsed time: <strong> " + hours + ":" + minutes + ":" + seconds + "</strong>";

	   	timerID = setTimeout("UpdateTimer()", 1000);
		}

		function Start() {
		   document.getElementById("hours").innerHTML = "Elapsed time: <strong><?= padit( floor( ( now - start ) / 3600 ) % 60, 2 ) ?>:<?= padit( ( floor( $now - $start ) / 60 ) % 60, 2 ) ?>:<?= padit( ( $now - $start ) % 60, 2 ) ?></strong>";

		  timerID  = setTimeout("UpdateTimer()", 1000);
		}

		function Reset() {
		   tStart = null;

		   document.getElementById("hours").innerHTML = "00:00";
		}

		function roundNumber(num, dec) {
			var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
			return result;
		}
		
		function padit(numb,nlen) {
			var str="" + numb ;
			while (str.length < nlen) {
				str="0"+str;
			}
			return str;
		}
		
		//-->

		</script>
		<style>
		#hours
		{
			display: inline;
			color: #333;
		}
		</style>
	</head>
	<body<?= $body ?>>
		<div id="header">
			<div class="center">
				<img src="images/headerLogo.jpg" alt="Time Sheets">
			</div>
		</div>
		<div id="navigation">
			<? include( "navigation.php" ); ?>
		</div>
		<div class="center">
			<div id="content">
			
			
<div style="border-bottom: 3px solid #DDD; padding-bottom: 10px;">
	<h1>Task Information</h1>
	<div class="details">
		<p><strong>Project:</strong> <a href="project_manage.php?id= <?= $row['project_id'] ?>"><?= $row['project'] ?> </a></p>
		<p><strong>Client:</strong> <?= $row['organization'] ?></p>
		<p><strong>Task:</strong> <?= $row['name'] ?></p>
		<? if( strlen( $row['description'] ) > 0 ): ?><p><strong>Description:</strong> <?= stripslashes( str_replace( "\n", "<br>", $row['description'] ) ) ?></p><? endif; ?>
		<? if( strlen( $row['notes'] ) > 0 ): ?><p><strong>Notes:</strong> <?= stripslashes( str_replace( "\n", "<br>", $row['notes'] ) ) ?></p><? endif; ?>
		<? if( $employeeArray['permission'] != "limited" ): ?><p><strong>Rate:</strong> $<?= $row['rate']."/".$row['unit'] ?></p><? endif; ?>
		<p><strong>Status:</strong> <?= status( $row['status'] ) ?></p>
		<? if( $employeeArray['permission'] != "limited" ): ?><p><a href="project_task_edit.php?id=<?= $id ?>&project_id=<?= $row['project_id'] ?>" class="large_link">Edit Task Information &raquo;</a></p><? endif; ?>
		
		<? if( $row['time_limit'] > 0 ): ?>
		<p><strong>Time Limit:</strong> <?= $row['time_limit'] ?></p>
		<? endif; ?>
		
		<? if( strlen( $row['deadline'] ) > 0 ): ?>
		<p><strong>Deadline:</strong> <?= $row['deadline'] ?></p>
		<? endif; ?>
		
	</div>
</div>

<?
$db->query( "SELECT messages.employee_id, 
	messages.project_id, 
	messages.task_id, 
	messages.file_id, 
	messages.filename, 
	messages.message, 
	messages.date, 
	messages.global, 
	messages.priority, 
	messages.sent_by, 
	messages.status, 
	messages.sent_by_name, 
	messages.id,
	project.name as project_name,
	project.id as project_id,
	project_task.id as task_id,
	project_task.name as task_name
FROM project_employees INNER JOIN messages ON project_employees.project_id = messages.project_id
	 INNER JOIN project ON messages.project_id = project.id
	 INNER JOIN project_task ON project_task.project_id = project.id AND messages.task_id = project_task.id
WHERE project_employees.hidden != \"1\" AND project_task.id = $id AND project_employees.employee_id = ".$employeeArray['id']."
	ORDER BY messages.date DESC" );

?>
<div style="padding-top: 15px;">
<h1>Messages</h1>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table" width="100%">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>From</td>
		<td>Project</td>
		<td>Task</td>
		<td>Sent</td>
		<td>Attachment</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr>
		<td nowrap><img src="images/misc_icons/email-<?= $row['priority'] ?>.gif"></td>
		<td nowrap><strong><?= $row['sent_by_name'] ?></strong></td>
		<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['project_name'] ?></a></td>
		<td><a href="project_task.php?id=<?= $row['task_id'] ?>"><?= $row['task_name'] ?></a></td>
		<td nowrap><?= date( "g:i A", $row['date'] ) ?> on <?= date( "m/d/y", $row['date'] ) ?></td>
		<td><? if( strlen( $row['filename'] ) > 0 ): ?> <a href="./uploads/<?= $row['filename']?>"><img src="images/misc_icons/file.gif"> Download</a><? endif; ?> &nbsp;</td>
	</tr>
	<tr class="email_end">
		<td>&nbsp;</td>
		<td colspan="5"><?= stripslashes( $row['message'] ) ?>&nbsp;</td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>There are no messages for this task</em></p>
<? endif; ?>
<p><a href="message_new.php" class="large_link">New Message &raquo;</a></p>
</div>

<div style="padding-top: 15px;">
<h1>Time Sheet</h1>
<?

$db->query( "SELECT project_task_hours.hours, 
	project_task_hours.notes, 
	project_task_hours.date, 
	project_task_hours.approved, 
	project_task_hours.approved_date, 
	project_task_hours.approved_by, 
	project_task_hours.id,
	project_task_hours.timestamp_start,
	project_task_hours.timestamp_end
FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
WHERE project_task.id = $id
ORDER BY project_task_hours.approved ASC, project_task_hours.timestamp_start DESC" );

$total_hours = 0;

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Time</td>
			<td>Hours</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
		<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<? $total_hours += $row['hours'] ?>
			<? $amount += $row['hours'] * $row['rate']; ?>
			<tr class="table_row">
				<td><?= tsDate( "m/d/Y", $row['timestamp_start'] ) ?></td>
				<td><?= tsDate( "g:i A", $row['timestamp_start'] ) ?> - <?= tsDate( "g:i A", $row['timestamp_end'] ) ?></td>
				<td><?= $row['hours'] ?> hours</td>			
				<td><?= approved( $row['approved'] ) ?></td>
				<td class="link_button"><? if( $row['approved'] == "0") : ?><a href="project_task_hours_edit.php?id=<?= $row['id'] ?>&project_id=<?= $project_id ?>">View Details</a><? else: ?>&nbsp;<? endif; ?></td>
				</tr>
		<? endwhile; ?>
	</table>
	<p><strong><?= $total_hours ?></strong> Total Hours<? if( $employeeArray['permission'] != "limited" ): ?>( $<?= number_format( $rate * $total_hours ) ?> Earned )<? endif; ?>
<? else: ?>
<p><em>No time has been recorded for this task</em></p>
<? endif; ?>
</div>

<? if( $status != "completed" ): ?>

<div style="padding-top: 5px; border-top: 3px solid #DDD;">
<? 

// See if there's a clock active for THIS task
$db->query( "SELECT * FROM project_task_clock WHERE task_id = $id" );

// No active clock
if( $db->result['rows'] == 0 ): ?>

<?
$db2 = new db();

// Check if clock active elsewhere
$db2->query( "SELECT project_task_clock.id, project_task_clock.task_id
FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
WHERE project_task.employee_id = ".$employeeArray['id'] );

// If there is a clock active elsewhere:
if( $db2->result['rows'] > 0 ):
?>
<p><img src="images/startclockinactive.gif" alt="Inactive Clock"></p>
<p><strong>You are currently recording time for a different task.</strong></p>
<? elseif( $status != "do-not-start" ): ?>
<p><a href="project_task_clock.php?id=<?= $id ?>&pid=<?= $project_id ?>" alt="Clock in"><img src="images/startclock.gif" alt="Start Clock"></a></p>
<? endif; ?>

<? else: ?>
<? $row = mysql_fetch_assoc( $db->result['ref'] ); ?>
<?
	$start = $row['timestamp_start'];
	$now = time();
	echo "<p><strong>Clocked in at ".tsDate( "g:i A", $start )." on ".tsDate( "M. jS, Y", $start )."</strong><br>";
?>
<p id="hours">Elapsed time: <strong><?= padit( floor( ( now - start ) / 3600 ) % 60, 2 ) ?>:<?= padit( ( floor( $now - $start ) / 60 ) % 60, 2 ) ?>:<?= padit( ( $now - $start ) % 60, 2 ) ?></strong></p>
<p><a href="project_task_clock.php?id=<?= $id ?>&pid=<?= $project_id ?>" alt="Clock out"><img src="images/stopclock.gif" alt="Stop Clock"></a></p>
<? endif; ?>
</div>

<? endif; ?>

<? include( "footer.php" ); ?>