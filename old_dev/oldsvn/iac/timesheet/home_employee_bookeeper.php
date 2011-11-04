<?
/**
 * Author:   	Cory Becker
 * Date:   	 	September 21, 2007
 * Company:		Becker Web Solutions, LLC
 * Website:	 	www.beckerwebsolutions.com
 *
 * Description:
 *					Header
 */

// Include
include( "code/config.php" );

session_start();

$user = new user();
$check = $user->checkUser( $_SESSION['username'], $_SESSION['password'] );
if(!$check)
{
	header( "Location: index.php" );
	exit();
}

$employeeArray = $user->array;

$p_level = $employeeArray['type'];
$_SESSION['p_level'] = $p_level;

$verified = true;	// used to check that included page used the header

// Database
$db = new db();

// Page specific functions

$db2 = new db();
$db2->query( "SELECT project_task.employee_id, 
	project_task_clock.timestamp_start, 
	project_task_clock.id
FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
WHERE project_task.status != \"completed\" AND project_task.employee_id = ".$employeeArray['id'] );

$row = mysql_fetch_assoc( $db2->result['ref'] );
$start = $row['timestamp_start'];
$now = time();

if( $db2->result['rows'] > 0 )
	$body = " onload=\"updateTime();\"";
	
function padit( $numb, $nlen) {
	$str="".$numb ;
	while (strlen( $str ) < $nlen) {
		$str="0".$str;
	}
	return $str;
}

function tsDate( $str, $timestamp )
{
	$offset_hours = 1;
	
	$offset = 3600 * $offset_hours;
	return date( $str, $timestamp + $offset );
}

?>

<html>
	<head>
		<title>Time Sheets: IAC Professionals</title>
		<link href="style/style.css" rel="stylesheet" type="text/css" />	
		<script language="JavaScript">
		var hour = <?= floor( ( $now - $start ) / 3600 ) % 60 ?>;
		var min  = <?= ( floor( $now - $start ) / 60 ) % 60 ?>;
		var sec  = <?= ( $now - $start ) % 60; ?>;

		// From http://www.aspfaq.com/show.asp?id=2300
		function PadDigits(n, totalDigits) 
		{ 
			n = n.toString(); 
			var pd = ''; 
			if (totalDigits > n.length) 
			{ 
				for (i=0; i < (totalDigits-n.length); i++) 
				{ 
					pd += '0'; 
				} 
			} 
			return pd + n.toString(); 
		} 

		function updateTime()
		{
			sec++;
			if (sec==60)
			{
				min++;
				sec = 0;
			}	

			if (min==60)
			{
				hour++;
				min = 0;
			}

			if (hour==24)
			{
				hour = 0;
			}

			document.getElementById("idTime").innerHTML = PadDigits(hour,2)+":"+PadDigits(min,2)+":"+PadDigits(sec,2);
			setTimeout('updateTime()',1000);
		}

		updateTime();
		</script>
		<style>
		#hours
		{
			display: inline;
			color: #FFF;
		}
		</style>
		<script type="text/javascript">
		function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=220,height=370');");
		}
		</script>
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
	
<div>
<h1>Recent Employee Activity</h1>
<?
$days = 1.5;
$time = mktime() - 60*60*24*$days;

$db->query( "SELECT employee.id, 
	employee.name, 
	project_task_hours.hours, 
	project_task_hours.date,
	project_task.rate,
	project_task.id as task_id,
	project_task.project_id as project_id,
	project_task.name as task_name,
	project_task_hours.id as project_task_hours_id,
	project_task_hours.approved,
	project_task_hours.fb_import,
	project_task_hours.notes,
	project_task_hours.manual_entry_date,
	project_task_hours.original_hours
FROM project_task INNER JOIN employee ON project_task.employee_id = employee.id
	 INNER JOIN project_task_hours ON project_task_hours.project_task_id = project_task.id
WHERE project_task_hours.approved = 0 OR project_task_hours.timestamp_start > $time
ORDER BY project_task_hours.approved ASC, project_task_hours.timestamp_start DESC" );

$amount = 0;

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Employee</td>
			<td>Task</td>
			<td>Hours</td>
			<td>Status</td>
			<td colspan="2">&nbsp;</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<? $row['return'] = "home.php"; ?>
		<? $total_hours += $row['hours'] ?>
		<? $amount += $row['hours'] * $row['rate']; ?>
		<tr class="table_row">
			<td><?= $row['date'] ?></td>
			<td><?= $row['name'] ?></td>
			<td><a href="project_task.php?id=<?= $row['task_id'] ?>"><?= $row['task_name'] ?></a></td>
			<? if( $row['manual_entry_date'] > 0 ): ?>
				<td class="modified"><?= $row['hours'] ?> (<?= $row['original_hours']?>)</td>	
			<? else: ?>
				<td><?= $row['hours'] ?></td>
			<? endif; ?>
			<td nowrap><?= approved( $row['approved'] ) ?></td>
			<td class="link_button" nowrap>
				<a href="project_task_hours_edit.php?id=<?= $row['project_task_hours_id'] ?>&project_id=<?= $row['project_id'] ?>">Edit</a>
				<? if( $row['approved'] == "0" ): ?> | <a href="project_task_hours_approve.php?id=<?= $row['project_task_hours_id'] ?>&project_id=<?= $row['project_id'] ?>&ret=home">Approve</a><? endif; ?>
				<? if( $row['fb_import'] == "0" ): ?> | <a href="project_task_hours_import.php?hours_id=<?= $row['project_task_hours_id'] ?>&return=<?= base64_encode( "home.php" ) ?>">Import</a><? endif; ?>
			</td>
		</tr>
	<? endwhile; ?>
		<tr class="table_footer">
			<td colspan="3" align="right">Total:</td>
			<td colspan="3"><?= $total_hours ?></td>
		</tr>
	</table>
<? else: ?>
<p><em>No employees have recently recorded time for projects</em></p>
<? endif; ?>
<a href="#" onclick="popUp('ajaxtimer.php');"><img src="images/timer.jpg" style="margin: 10px 0 0 0;"></a>
</div>

<div style="padding-top: 15px;">
<h1>Employee Activity</h1>
<?

// Combine traditional and AJAX timers to one result array
$activeClocksArray = array();

// -----------------------------------
// Active traditional clocks
// -----------------------------------
$db->query( "SELECT project_task_clock.task_id, 
	project_task_clock.timestamp_start, 
	employee.id AS employee_id, 
	employee.name AS employee_name, 
	project_task.name AS task_name, 
	project_task.id AS task_id
FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
	 INNER JOIN employee ON project_task.employee_id = employee.id" );

$now = mktime();

if( $db->result['rows'] > 0 )
{
	while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
	{			
		$array = array( "timestamp" => $row['timestamp_start'],
		 				"employee_name" => $row['employee_name'],
						"task_name" => $row['task_name'],
						"task_id" => $row['task_id'],
						"hours" => round( floor( ( $now - $row['timestamp_start'] ) / 60 ) / 60, 2 ),
						"type" => "Standard Timer" );
		
		array_push( $activeClocksArray, $array );
	}
}

// -----------------------------------
// Active AJAX Timers
// -----------------------------------

// Delete timer entries over 5 minutes
$expiredTime = $now - (60 * 5);

$db->query( "DELETE FROM project_timer WHERE `timestamp` < $expiredTime" );

$db->query( "SELECT project_timer.task_id, project_timer.task_name, project_timer.employee_id, employee.name AS employee_name, project_timer.hours FROM project_timer LEFT JOIN employee ON employee.id = project_timer.employee_id" );

if( $db->result['rows'] > 0 )
{
	while( $row = mysql_fetch_assoc( $db->result['ref'] ) )
	{
		if( strlen( $row['task_name'] ) == 0 )
			$row['task_name'] = "<em>No Task Selected</em>";
			
		$array = array( "timestamp" => $now - ( 3600 * $row['hours'] ),
		 				"employee_name" => $row['employee_name'],
						"task_name" => $row['task_name'],
						"task_id" => $row['task_id'],
						"hours" => $row['hours'],
						"type" => "Pop-Up Timer" );
						
		array_push( $activeClocksArray, $array );
	}
}

?>
<? if( count( $activeClocksArray ) > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Started</td>
			<td>Employee</td>
			<td>Task</td>
			<td>Hours</td>
			<td>Timer Type</td>
		</tr>
	<? foreach( $activeClocksArray as $row ): ?>
		<tr class="table_row">
			<td><?= tsDate( "m/d/Y", $row['timestamp'] ) ?></td>
			<td><?= tsDate( "g:i A", $row['timestamp'] ) ?></td>
			<td><?= $row['employee_name'] ?></td>
			<td><?= $row['task_name'] ?></td>
			<td><?= $row['hours']; ?></td>
			<td><?= $row['type'] ?></td>		
		</tr>
	<? endforeach; ?>
	</table>
<? else: ?>
<p><em>No employees are currently working on projects</em></p>
<? endif; ?>
</div>

<?
	/*$db->query( "SELECT messages.employee_id, 
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
	WHERE messages.date > $time
	ORDER BY messages.date DESC" );*/
	
	$db->query( "SELECT * FROM messages
		LEFT OUTER JOIN project_task ON messages.task_id = project_task.id
		LEFT OUTER JOIN project ON project_task.project_id = project.id
		ORDER BY messages.date DESC" );
?>
<div style="padding-top: 15px;">
<h1>Recent Messages</h1>

<? if( isset( $_REQUEST['success'] ) ): ?>
<p class="color_blue">Message sent successfully</p>
<? endif; ?>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>&nbsp;</td>
		<td>From</td>
		<td>Project</td>
		<td>Task</td>
		<td>Sent</td>
		<td>Attachment</td>
		<td>&nbsp;</td>
	</tr>
	<? for( $i = 0; $i < $db->result['rows']; $i++ ): ?>
	<? $row = mysql_fetch_assoc_join( $db->result['ref'] ); ?>
	<!-- <? var_dump( $row ) ?> -->
	<tr>
		<td nowrap><img src="images/misc_icons/email-<?= $row['messages.priority'] ?>.gif"></td>
		<td nowrap><strong><?= $row['messages.sent_by_name'] ?></strong></td>
		<td><a href="project_manage.php?id=<?= $row['project.id'] ?>"><?= $row['project.name'] ?></a></td>
		<td><a href="project_task.php?id=<?= $row['project_task.id'] ?>"><?= $row['project_task.name'] ?></a></td>
		<td nowrap><?= date( "g:i A", $row['messages.date'] ) ?> on <?= date( "m/d/y", $row['messages.date'] ) ?></td>
		<td nowrap><? if( strlen( $row['messages.filename'] ) > 0 ): ?> <a href="./uploads/<?= $row['messages.filename']?>"><img src="images/misc_icons/file.gif"> Download</a><? endif; ?> &nbsp;</td>
		<td nowrap><a href="message_delete.php?id=<?= $row['messages.id'] ?>"><img src="images/misc_icons/email-delete.gif"> Delete</a></td>
	</tr>
	<tr class="email_end">
		<td>&nbsp;</td>
		<td colspan="6"><?= stripslashes( $row['messages.message'] ) ?>&nbsp;</td>
	</tr>
	<? endfor; ?>
</table>
<? else: ?>
<p><em>There are no messages</em></p>
<? endif; ?>
<p><a href="message_new.php" class="large_link">Compose Message &raquo;</a></p>
</div>

<? include( "footer.php" ); ?>