<div class="center" style="font-size:10pt;">
	<ul>
		<li><a href="home.php">Home</a></li> |
		<li><a href="client_activity.php">Client Activity</a></li> |
		<?
		$msgCount = getMessageCount($employeeArray['id']);
		if ( $msgCount > 0 ){?>			
			<li><a href="messages.php">Messages(<?= $msgCount ?>)</a></li> | 
		<?} else {?>
			<li><a href="messages.php">Messages</a></li> |
		<?}?>		
		<li><a href="files.php">Files</a></li> |
		<li><a href="clients.php">Clients</a></li> |
		<li><a href="employees.php">Employees</a></li> |
		<li><a href="reports.php">Reports</a></li> |
		<li><a href="/calendar/login.php" target="_BLANK">Calendar</a></li> |
		<li><a href="resources.php">Resources</a></li> |
		<li><a href="referrals.php">Referrals</a></li> |
		<li><a href="client_resources.php">Client Resources</a></li> |
		<li><a href="data_administration.php">Admin</a></li> |
		<li><a href="signout.php">Sign Out</a></li>		
	</ul>
</div>