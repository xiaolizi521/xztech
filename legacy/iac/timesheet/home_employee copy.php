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
	header("Location: index.php" );

$employeeArray = $user->array;

$p_level = $employeeArray['type'];
$_SESSION['p_level'] = $p_level;

$verified = true;	// used to check that included page used the header

// Database
$db = new db();

	// Page specific functions
	$db->query( "SELECT project.id as project_id, client.id as client_id, client.organization, project.name, project.status FROM project, client WHERE project.client_id = client.id AND project.status != 'completed'" );

$db2 = new db();
$db2->query( "SELECT project_task.employee_id, 
	project_task_clock.timestamp_start, 
	project_task_clock.id
FROM project_task_clock, project_task
WHERE project_task.employee_id = ".$employeeArray['id'] );

$row = mysql_fetch_assoc( $db2->result['ref'] );
$start = $row['timestamp_start'];
$now = time();
$min = round( floor( ( $now - $start ) / 60 ) / 60, 2 );

if( $db2->result['rows'] > 0 )
	$body = " onload=\"Reset(); Start();\"";
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

		function UpdateTimer() {
		   if(timerID) {
		      clearTimeout(timerID);
		      clockID  = 0;
		   }
		
			now++;
			
		   document.getElementById("hours").innerHTML = "" + roundNumber( ( now - start ) / 60, 2 ) + " minutes";
	
	   	timerID = setTimeout("UpdateTimer()", 1000);
		
		}

		function Start() {
		   document.getElementById("hours").innerHTML = "<?= number_format( floor( $now - $start ) / 60, 2, ".", "," ) ?> minutes";
		
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
		//-->

		</script>
		<style>
		#hours
		{
			display: inline;
			color: #FFF;
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
	
<div>
<h1>My Projects</h1>
<? if( $db->result['rows'] > 0 ): ?>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Project</td>
		<td>Client</td>
		<td>&nbsp;</td>
	</tr>
	<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
	<tr class="table_row">
		<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['name'] ?></a></td>
		<td><?= $row['organization'] ?></td>
		<td class="link_button"><a href="project_manage.php?id=<?= $row['project_id'] ?>">View Details</a></td>
	</tr>
	<? endwhile; ?>
</table>
<? else: ?>
<p><em>No projects have been assigned</em></p>
<? endif; ?>
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
	project_task.project_id
FROM project_task INNER JOIN employee ON project_task.employee_id = employee.id
	 INNER JOIN project ON project_task.project_id = project.id
WHERE employee.username = \"".$_SESSION['username']."\" AND project.status != 'completed'
ORDER BY project.id" );

?>
<? if( $db->result['rows'] > 0 ): ?>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Clock</td>
			<td>Project</td>
			<td>Task</td>
			<td>Description</td>
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
			if( $db2->result['rows'] > 0 ):
			?>
			<td>&nbsp;</td>
			<? else: ?>
			<td><a href="project_task_clock.php?id=<?= $row['id'] ?>&ret=home" alt="Clock in" class="color_green">Clock In</a></td>
			<? endif; ?>

			<? else: ?>
			<? $row2 = mysql_fetch_assoc( $db2->result['ref'] ); ?>
			<?
				$start = $row2['timestamp_start'];
				$now = time();
				$min = number_format( floor( $now - $start ) / 60, 2, ".", "," );
				
				$clockFlag = true;
			?>
			<td><a href="project_task_clock.php?id=<?= $row['id'] ?>&ret=home" alt="Clock out" class="color_red">Clock Out &raquo; <span id="hours" style="color: #ffc7c7;"><?= $min ?> minutes</span></a></td>
			<? endif; ?>

			<td><a href="project_manage.php?id=<?= $row['project_id'] ?>"><?= $row['project_name'] ?></a></td>
			<td><a href="project_task.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
			<td><?= strlen( $row['description'] ) > 0 ? substr( $row['description'], 0, 20 )."..." : "&nbsp;" ?></td>			
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