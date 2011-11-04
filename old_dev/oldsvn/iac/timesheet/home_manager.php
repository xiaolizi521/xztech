<? $page = "manager"; ?>
<? include( "header.php" ); ?>

<?

?>

<!-- Start of Active Projects -->
<div>
<h1>Active Projects</h1>
<table cellpadding="0" cellspacing="0" border="0" class="data_table">
	<tr class="table_heading">
		<td>Client</td>
		<td>Project</td>
		<td>Hours</td>
		<td>Status</td>
	</tr>
	<tr class="table_row">
		<td><a href="client.php">Becker Web Solutions, LLC</a></td>
		<td><a href="project.php">Quarterly Tax Preparation</a></td>
		<td>2.2 Hours</td>
		<td><span class="color_red">Problems</span></td>
	</tr>
	<tr class="table_row">
		<td><a href="client.php">Schott Enterprises, LLC</a></td>
		<td><a href="project.php">Brochure Design</a></td>
		<td>3.2 Hours</td>
		<td><span class="color_green">In Progress</span></td>
	</tr>
	<tr class="table_row">
		<td><a href="client.php">Schott Enterprises, LLC</a></td>
		<td><a href="project.php">Business Card Designs</a></td>
		<td>3.2 Hours</td>
		<td><span class="color_yellow">On Hold</span></td>
	</tr>
	<tr class="table_row">
		<td><a href="client.php">Airport Professional Services, LLC</a></td>
		<td><a href="project.php">Website Design &amp; Development</a></td>
		<td>3.2 Hours</td>
		<td><span class="color_gray">On Hold</span></td>
	</tr>
	<tr class="table_row">
		<td><a href="client.php">VGA Group</a></td>
		<td><a href="project.php">Website Design &amp; Development</a></td>
		<td>3.2 Hours</td>
		<td><span class="color_gray">On Hold</span></td>
	</tr>
	<tr class="table_row">
		<td><a href="client.php">Derr &amp; Howell, LLO, PC</a></td>
		<td><a href="project.php">Tax Preparation &amp; Website Design</a></td>
		<td>14.0 Hours</td>
		<td><span class="color_blue">Completed</span></td>
	</tr>
</table>
</div>
<!-- End of Active Projects -->


<!-- Start of Employee Activity -->
<div style="padding-top: 20px;">
	<h1>Employee Activity</h1>
	<table cellpadding="0" cellspacing="0" border="0" class="data_table">
		<tr class="table_heading">
			<td>Date</td>
			<td>Employee</td>
			<td>Project</td>
			<td>Task</td>
			<td>Hours</td>
			<td>Charge</td>
		</tr>
		<tr class="table_row">
			<td>09/02/2007</td>
			<td>Cory Becker</td>
			<td>Quarterly Tax Return</td>
			<td>Tax Preparation</td>
			<td>5.1 Hours</td>
			<td>$1,040.00</td>
		</tr>
	</table>
</div>
<!-- End of Employee Activity -->

<? include( "footer.php" ); ?>