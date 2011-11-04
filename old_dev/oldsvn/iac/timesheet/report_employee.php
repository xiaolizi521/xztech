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
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	$post = $_POST;
	setlocale(LC_MONETARY, 'en_US');
?>

<div>
<h1>Reports</h1>

<? if( $_REQUEST['action'] == "generate" ): ?>
<?

$start = mktime( 0, 0, 0, $_POST['smonth'], $_POST['sday'], $_POST['syear'] );
$end = mktime( 23, 59, 59, $_POST['emonth'], $_POST['eday'], $_POST['eyear'] );

if( $post['employee_active'] == "yes" )
	$employee_active = "AND employee.active = 1";
	
$db->query( "SELECT employee.name, 
	SUM( project_task_hours.hours ) as total_hours,
	project_task_hours.hours,		
	SUM(project_task_hours.hours*project_task.rate) AS income
FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
	 INNER JOIN employee ON project_task.employee_id = employee.id
WHERE project_task_hours.timestamp_start >= $start AND project_task_hours.timestamp_end <= $end AND project_task_hours.approved = 1 $employee_active AND employee.id = ".$employeeArray['id']."
GROUP BY employee.name" );

if( $start < $end ): ?>
<p class="color_black">Employe Time Sheet Report
 (<?= date( "M. j, Y", $start ) ?> to <?= date( "M. j, Y", $end ) ?>)</p>

	<div style="border-bottom: 2px solid #DDD; padding-bottom: 15px;">
		<p><strong>Summary Income</strong></p>
		<table cellpadding="0" cellspacing="0" border="0" class="report_table">
			<tr class="report_heading">
				<td>Employee</td>
				<td>Hours</td>
				<td>Approved Income</td>
			</tr>
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
			<tr class="report_row">
				<td><?= $row['name'] ?></td>
				<td><?= $row['total_hours'] ?></td>
				<td><strong>$<?= number_format( $row['income'], 2, ".", "," ) ?></strong></td>
			</tr>				
			<? endwhile; ?>
		</table>
	</div>
	<? else: ?>
	<div>
		<p class="color_black">Employee Income Sheet</p>
		<p class="color_red">Start date must come before end date</p>
		<p><a href="reports.php" class="large_link" alt="Reports">&laquo; Go Back</a></p>
	</div>
	<? endif; ?>
	
	
	<?

	$start = mktime( 0, 0, 0, $_POST['smonth'], $_POST['sday'], $_POST['syear'] );
	$end = mktime( 23, 59, 59, $_POST['emonth'], $_POST['eday'], $_POST['eyear'] );

	$db->query( "SELECT employee.name, 
		project_task_hours.hours, 
		project_task_hours.hours*project_task.rate AS income, 
		project_task_hours.approved, 
		project_task_hours.timestamp_start, 
		project_task_hours.timestamp_end,
		project.name as project_name
	FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
		 INNER JOIN employee ON project_task.employee_id = employee.id
		 INNER JOIN project ON project.id = project_task.project_id
		WHERE project_task_hours.timestamp_start >= $start AND project_task_hours.timestamp_end <= $end AND project_task_hours.approved = 1 AND employee.active = 1 AND employee.id = ".$employeeArray['id']."
		ORDER BY employee.name, project_task_hours.timestamp_start DESC" );

	if( $start < $end ): ?>
		<div>
			<p><strong>Detailed Income</strong></p>
			<table cellpadding="0" cellspacing="0" border="0" class="report_table">
				<tr class="report_heading">
					<td>Date</td>
					<td>Project</td>
					<td>Hours</td>
					<td>Approved Income</td>
				</tr>
				<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
				
					<? if( $prev_name == $row['name'] ): ?>
					<tr class="report_row">			
					<? else: ?>
					<tr class="report_row_end">
					<? endif; ?>
					<? $prev_name = $row['name']; ?>
					
					<td><?= date( "M. j, Y", $row['timestamp_start'] ) ?></td>
					
					<? if( $prev_project == $row['project_name'] ): ?>
						<td><?= $row['project_name'] ?></td>
					<? else: ?>
						<td><?= $row['project_name'] ?></td>
					<? endif; ?>
					<? $prev_project = $row['project_name']; ?>
										
					<td><?= $row['hours'] ?></td>
					
					<td>$<?= number_format( $row['income'], 2, ".", "," ) ?></td>
				</tr>				
				<? endwhile; ?>
			</table>
		</div>
		<? endif; ?>
	
	
<? else: ?>
<p class="color_black">Employe Time Sheet Report</p>
<form method="post" action="report_employee.php?action=generate">
	<table cellpadding="5" border="0">
		<tr>
			<td>Start Date:</td>
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

				$date_month = date( "m" );
				$date_day = date( "d" );
				$date_year = date( "Y" );
				?>

			<select name="smonth">
			<? foreach( $months as $numeric => $name ): ?>
				<? if( $numeric == $date_month ): ?>
					<option value="<?= $numeric ?>" selected="yes"><?= $name ?></option>
				<? else: ?>
					<option value="<?= $numeric ?>"><?= $name ?></option>
				<? endif; ?>
			<? endforeach; ?>
			</select>

			<select name="sday">
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

			<select name="syear">
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
		</tr>
		<tr>
			<td>End Date:</td>
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

				$date_month = date( "m" );
				$date_day = date( "d" );
				$date_year = date( "Y" );
				?>

			<select name="emonth">
			<? foreach( $months as $numeric => $name ): ?>
				<? if( $numeric == $date_month ): ?>
					<option value="<?= $numeric ?>" selected="yes"><?= $name ?></option>
				<? else: ?>
					<option value="<?= $numeric ?>"><?= $name ?></option>
				<? endif; ?>
			<? endforeach; ?>
			</select>

			<select name="eday">
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

			<select name="eyear">
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
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" value="Generate Report">
			</td>
		</tr>
	</table>
</form>
<? endif; ?>

</div>

<? include( "footer.php" ); ?>
