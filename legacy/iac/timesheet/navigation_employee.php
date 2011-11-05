<?
	$p_levelType = $_SESSION['p_leveltype']; 
?>

<div class="center">
	<ul>
		<li><a href="home.php">Home</a></li> |
		<?
		$msgCount = getMessageCount($employeeArray['id']);
		if ( $msgCount > 0 ){?>			
			<li><a href="messages.php">Messages(<?= $msgCount ?>)</a></li> | 
		<?} else {?>
			<li><a href="messages.php">Messages</a></li> |
		<?}?>
		<li><a href="files.php">Files</a></li> |
		<? if ( $p_levelType == "human resources" ) { ?>
			<li><a href="reports.php">Reports</a></li> |
		<?}else{?>
			<li><a href="report_employee.php">Reports</a></li> |
		<?}?>
		<li><a href="/calendar/login.php" target="_BLANK">Calendar</a></li> |
		<li><a href="resources.php">Resources</a></li> |		
		<li><a href="account.php">My Account</a></li> |

		<?
		switch ( $p_levelType ){
			case ( 'manager' ):
				print '<li><a href="projects.php">Projects</a></li> |';
				print '<li><a href="client_resources.php">Client Resources</a></li> |';
				break;
				
			case ( 'timekeeper' ):
				print '<li><a href="client_resources.php">Client Resources</a></li> |';
				break;
				
			case ( 'human resources' ):
				print '<li><a href="projects.php">Projects</a></li> |';
				print '<li><a href="employees.php">Employees</a></li> |';
				print '<li><a href="client_resources.php">Client Resources</a></li> | ';
				break;
				
			default:
				print '<li><a href="client_resources_E.php">Client Resources</a></li> | ';
		}
		?>
		
		<li><a href="signout.php">Sign Out</a></li>
		
	</ul>
</div>