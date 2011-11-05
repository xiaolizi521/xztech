
<? if( $_REQUEST['action'] == "generate" ): ?>
<p class="color_black">Plan Details Report</p>
<?

$start = mktime( 0, 0, 0, $_POST['smonth'], $_POST['sday'], $_POST['syear'] );
$end = mktime( 23, 59, 59, $_POST['emonth'], $_POST['eday'], $_POST['eyear'] );

if( $post['project'] != "" ){
	if ( $post['project'] == 'allopen'){
		//$project = "AND project.status != 'completed'";
        $project = "AND plan_assignment.status != 'completed'";
	}elseif ( $post['project'] == 'allclosed'){
		//$project = "AND project.status = 'completed'";
        $project = "AND plan_assignment.status = 'complted'";
	}else{
		//$project = "AND project.id = ".$post['project'];
        $project = "AND plan_assignment.status = '".$post['project'];
	}
}

if( $post['employee'] != "" )
	$employee = "AND employee.id = ".$post['employee'];

if( $post['employee_active'] == "yes" )
	$employee_active = "AND employee.active = 1";
/*
$db->query( "SELECT employee.name,
	project_task_hours.hours,
	project_task_hours.hours*project_task.rate AS income,
	project_task_hours.approved,
	project_task_hours.timestamp_start,
	project_task_hours.timestamp_end,
	project.name as project_name,
	project_task.rate_billable,
	project_task.rate,
	project_task.name as task_name,
	project_task_hours.notes
FROM project_task_hours INNER JOIN project_task ON project_task_hours.project_task_id = project_task.id
	 INNER JOIN employee ON project_task.employee_id = employee.id
	 INNER JOIN project ON project.id = project_task.project_id
	WHERE project_task_hours.timestamp_start >= $start AND project_task_hours.timestamp_end <= $end AND project_task_hours.approved = 1 $employee_active $project $employee
	ORDER BY project.name, employee.name, project_task_hours.timestamp_start" );
 */

$db->query( "SELECT
              employee.name,
              plan_assignment_hours.hours,
              plan_assignment_hours.hours * plan_assignment.rate_billable AS income,
              plan_assignment_hours.approved,
              plan_assignment_hours.timestamp_start,
              plan_assignment_hours.timestamp_end,
              plan_assignment.name AS project_name,
              plan_assignment.rate_billable,
              plan_assignment.rate,
              plan_assignment.name AS task_name,
              plan_assignment_hours.notes
            FROM plan_assignment_hours
            INNER JOIN plan_assignment ON plan_assignment_hours.plan_assignment_id = plan_assignment.id
            INNER JOIN plan_assignment_employees ON plan_assignment_employees.plan_assignment_id = plan_assignment.id
            INNER JOIN employee ON plan_assignment_employees.employee_id = employee.id
            INNER JOIN plan_task_rates ON plan_task_rates.id = plan_assignment.plan_task_rate_id
            WHERE plan_assignment_hours.timestamp_start >= $start 
            AND plan_assignment_hours.timestamp_end <= $end
            AND plan_assignment_hours.approved =1
            $employee_active
            $project
            $employee
            ORDER BY plan_assignment.name, employee.name, plan_assignment_hours.timestamp_start" );

if( $start < $end && $db->result['rows'] > 0 ): ?>
	<div>
			<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ): ?>
				<? if( $prev_name == $row['project_name'] ): ?>
				<? // Continue current project ?>
				<tr class="report_row">

				<? elseif( $ploop ): ?>
				<? // End of project - tally each column ?>
				<tr class="report_row_total">
					<td colspan="4" align="right"><strong>Totals:</strong></td>
					<td><?= $total_hours ?></td>
					<td>&nbsp;</td>
					<td>$<?= number_format( $total_billable, 2, ".", "," ) ?></td>
				</tr>
				<?
					$total_cost = 0;
					$total_billable = 0;
					$total_profit = 0;
					$total_hours = 0;
				?>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
				</table>
				<p><strong><?= stripslashes( $row['project_name'] ) ?></strong></p>
				<table cellpadding="0" cellspacing="0" border="0" class="report_table">
					<tr class="report_heading">
						<td>Date</td>
						<td>Employee</td>
						<td>Task</td>
						<td>Notes</td>
						<td>Hours</td>
						<td>Billable Rate</td>
						<td>Fee</td>
					</tr>
				<? else: ?>
				<? // New project ?>
				<p><strong><?= stripslashes( $row['project_name'] ) ?></strong></p>
				<table cellpadding="0" cellspacing="0" border="0" class="report_table">
					<tr class="report_heading">
						<td>Date</td>
						<td>Employee</td>
						<td>Task</td>
						<td>Notes</td>
						<td>Hours</td>
						<td>Billable Rate</td>
						<td>Fee</td>
					</tr>
				<tr class="report_row">
				<? endif; ?>


				<? // Start of report ?>


				<? $prev_name = $row['project_name']; ?>

				<td><?= date( "m/d/Y", $row['timestamp_start'] ) ?></td>

				<? if( $prev_project == $row['name'] ): ?>
					<td style="color: #333;"><?= $row['name'] ?></td>
				<? else: ?>
					<td><?= $row['name'] ?></td>
				<? endif; ?>

				<? if( $prev_project == $row['name'] && $prev_task == $row['task_name'] ): ?>
					<td style="color: #333;"><?= $row['task_name'] ?></td>
				<? else: ?>
					<td><?= $row['task_name'] ?></td>
				<? endif; ?>

				<? $prev_project = $row['name']; ?>
				<? $prev_task = $row['task_name'] ?>

				<td><?= $row['notes'] ?></td>
				<td><?= $row['hours'] ?></td>
				<td>$<?= $row['rate_billable'] ?></td>

				<td>$<?= number_format( $row['income'], 2, ".", "," ) ?></td>

				<? $total_cost += $row['income']; ?>
				<? $total_hours += $row['hours']; ?>

				<? $total_billable += $row['rate_billable'] * $row['hours']; ?>

				<? $total_profit += round( $row['rate_billable'] * $row['hours'], 2 ) - round( $row['income'] *  $row['hours'], 2 ); ?>

			</tr>
			<? $ploop = true; ?>
			<? if( $prev_name != $row['project_name'] ): ?>
			</table>
			<? endif; ?>
			<? endwhile; ?>
			<tr class="report_row_total">
				<td colspan="4" align="right"><strong>Totals:</strong></td>
				<td><?= number_format( $total_hours, 2, ".", "," ) ?></td>
				<td>&nbsp;</td>
				<td>$<?= number_format( $total_billable, 2, ".", "," ) ?></td>
			</tr>
		</table>
	</div>
	<? elseif( $db->result['rows'] == 0 ): ?>
	<div>
		<p><strong>No results</strong></p>
		<p><a href="reports.php" class="large_link" alt="Reports">&laquo; Go Back</a></p>
	</div>
	<? else: ?>
	<div>
		<p class="color_red">Start date must come before end date</p>
		<p><a href="reports.php" class="large_link" alt="Reports">&laquo; Go Back</a></p>
	</div>
	<? endif; ?>

<? else: ?>
<p class="color_black">Project Details Report</p>
	<form method="post" action="reports.php?report=projects&action=generate">
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
				<td>Plan:</td>
				<td>
				<select name="project">
				<option value="">All Plans</option>
				<option value="allopen">All Open Plans</option>
				<option value="allclosed">All Closed Plans</option>
				<option value="">-------------------------</option>
				// Get open projects first
				<optgroup label="Open Plans" >
				<?
					//$db->get( "project", array() );
                    $db->get( "plan_assignment", array() );
					while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):
						if ( $row['status'] != 'completed'){
				?>
							<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?}?>
				<? endwhile; ?>
				</optgroup>

				<!-- Now get closed projects -->
				<optgroup label="Closed Plans" >
				<?
					//$db->get( "project", array() );
                    $db->get( "plan_assignment", array() );
					while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):
						if ( $row['status'] == 'completed'){
				?>
							<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?}?>
				<? endwhile; ?>
				</optgroup>
				</select>
				</td>
			</tr>
			<tr>
				<td>Employee:</td>
				<td>
				<select name="employee">
				<option value="">All Employees</option>
				<option value="">-------------------------</option>
				// Get active employees first
				<optgroup label="Active Employees" >
				<?
					$db->get( "employee", array() );
					$recordSet = $db->result['ref'];
					while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):
						if ($row['active'] == 1){
				?>
							<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?}?>
				<? endwhile; ?>
				</optgroup>

				<optgroup label="Inactive Employees" >
				<?
				// Now inactive employees
					$db->get( "employee", array() );
					while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):
						if ($row['active'] == 0){
				?>
						<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?}?>
				<? endwhile; ?>
				</optgroup>
				</select>
				</td>
			</tr>
			<tr>
				<td>Other:</td>
				<td><input type="checkbox" name="employee_active" value="yes"> Show only active employees in report</td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<input type="submit" value="Generate Report">
				</td>
			</tr>
		</table>
	</form>
<? endif; ?>
