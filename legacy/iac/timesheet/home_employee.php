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
<h1>My Projects</h1>
<?
$db->query("SELECT project.name as name,
	project.description, 
	client.organization as organization,
	project.id as project_id,
	project.status,
	project_employees.hidden
FROM project INNER JOIN client ON project.client_id = client.id
	 INNER JOIN project_employees ON project_employees.project_id = project.id
	WHERE project_employees.hidden = 0 AND project.status != \"completed\" AND project_employees.employee_id = ".$employeeArray['id'] );
?>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Project</td>
		<td>Client</td>
		<td>Status</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['name'] ?></a></td>
		<td><?= $row['organization'] ?></td>
		<td><?= status( $row['status'] ) ?></td>
		<td class="link_button"><a href="project_manage.php?id=<?= $row['project_id'] ?>">View Details</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>No projects have been assigned</em></p>
<? endif; ?>
<a href="#" onclick="popUp('ajaxtimer.php');"><img src="images/timer.jpg" style="margin: 10px 0 0 0;"></a>
</div>


<div style="padding-top: 15px;">
<h1>My Tasks</h1>
<?
$db->query( "SELECT employee.username, 
	project_task.rate, 
	project_task.task_id, 
	project_task.unit, 
	project_task.name, 
	project_task.id, 
	project_task.description, 
	project_task.notes, 
	project_task.status, 
	project.name as project_name,
	project_task.project_id,
	project_task.time_limit,
	project_task.deadline
FROM project_task INNER JOIN employee ON project_task.employee_id = employee.id
	 INNER JOIN project ON project_task.project_id = project.id
WHERE employee.id = ".$employeeArray['id']." AND project_task.status != \"completed\"
ORDER BY project.id" );

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Clock</td>
			<td>Project</td>
			<td>Task</td>
			<td>Time Limit</td>
			<td>Deadline</td>
			<td>Status</td>
			<? if( $employeeArray['permission'] != "limited" ): ?><td>Rate</td><? endif; ?>
			<td>&nbsp;</td>
		</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
		<tr class="table_row">
			<? 
			$db2 = new db();

			$db2->query( "SELECT * FROM project_task_clock WHERE task_id = ".$row['id'] );
			if( $db2->result['rows'] == 0 ): ?>

			<?
			$db2->query( "SELECT project_task_clock.id, project_task_clock.task_id
			FROM project_task_clock INNER JOIN project_task ON project_task_clock.task_id = project_task.id
			WHERE project_task.employee_id = ".$employeeArray['id'] );
			if( $db2->result['rows'] > 0 || $row['status'] == "do-not-start" ):
			?>
			<td>&nbsp;</td>
			<? else: ?>
			<td><a href="project_task_clock.php?id=<?= $row['id'] ?>&ret=home&pid=<?= $row['project_id'] ?>" alt="Clock in" class="color_green">Clock In</a></td>
			<? endif; ?>

			<? else: ?>
			<? $row2 = mysql_fetch_assoc( $db2->result['ref'] ); ?>
			<?
				$start = $row2['timestamp_start'];
				$now = time();

				$min = number_format( floor( $now - $start ) / 60, 2, ".", "," );
				
				$clockFlag = true;
			?>
			<td nowrap><a href="project_task_clock.php?id=<?= $row['id'] ?>&ret=home&pid=<?= $row['project_id'] ?>" alt="Clock out" class="color_red">Clock Out &raquo; <span id="idTime" style="color: #ffc7c7;"><strong><?= padit( floor( ( $now - $start ) / 3600 ) % 60, 2 ) ?>:<?= padit( ( floor( $now - $start ) / 60 ) % 60, 2 ) ?>:<?= padit( ( $now - $start ) % 60, 2 ) ?></strong></span></a></td>
			<? endif; ?>

			<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['project_name'] ?></a></td>
			<td><a href="project_task.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
			<td><? if( $row['time_limit'] > 0 ) echo $row['time_limit']; else echo "-" ?></td>
			<td><? if( strlen( $row['deadline'] ) > 0 ) echo $row['deadline']; else echo "-" ?></td>
			<td nowrap><?= status( $row['status'] ) ?></td>
			<? if( $employeeArray['permission'] != "limited" ): ?><td>$<?= $row['rate'] ?>/<?= $row['unit'] ?></td><? endif; ?>
			<td class="link_button"><a href="project_task.php?id=<?= $row['id'] ?>">View Details</a></td>
		</tr>
	<? endwhile; ?>
	</table>
<? else: ?>
<p><em>No tasks have been assigned</em></p>
<? endif; ?>
</div>

<? if( $clockFlag ): ?>
<div style="padding-top: 15px;">
	<p><em>*You may be clocked in for a maximum of one task at a time.</em></p>
</div>
<? endif; ?>

<? include( "footer.php" ); ?>