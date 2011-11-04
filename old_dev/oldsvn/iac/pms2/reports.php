<?
	session_start();
		
	$p_level = $_SESSION['p_level'];
	$p_levelType = $_SESSION['p_leveltype'];
	
	if( $p_level == "super-manager" || $p_level == "reports" || $p_levelType == "human resources" )
		include( "header_functions.php" );
	else
	{
		header( "Location: home.php" );
		exit();
	}
	
	// Include header TEMPLATE
	include( "header_template.php" );
	
	$post = $_POST;
	setlocale(LC_MONETARY, 'en_US');
?>

<div>
<h1>Reports</h1>

<!------------------------------ INCOME REPORT ----------------------------------------->
<? if( $_REQUEST['report'] == "income" ): ?>
<? include( "reports_income.php" ); ?>

<!-------------------------- COST / BILLABLE REPORT ------------------------------------>
<? elseif( $_REQUEST['report'] == "costs" ): ?>
<? include( "reports_costs.php" ); ?>

<!-------------------------- ACTIVE EMPLOYEE PROJECTS ------------------------------------>
<? elseif( $_REQUEST['report'] == "employee_projects" ): ?>
<? include( "reports_employee_projects.php" ); ?>

<!-------------------------- PROJECT REPORT ------------------------------------>
<? elseif( $_REQUEST['report'] == "projects" ): ?>
<? include( "reports_projects.php" ); ?>

<!------------------------------ DEFAULT INDEX ----------------------------------------->
<? else: ?>

<ul>
	<li><a href="reports.php?report=income">Employee Income Sheet</a></li>
	<? if ( $p_levelType != "human resources" ) { ?>
		<li><a href="reports.php?report=costs">Costs / Billable</a></li>
	<? } ?>
		<li><a href="reports.php?report=employee_projects">Active Employee Plans</a></li>
	<? if ( $p_levelType != "human resources" ) { ?>
		<li><a href="reports.php?report=projects">Plans</a></li>
	<? } ?>
	
</ul>

<? endif; ?>
</div>

<? include( "footer.php" ); ?>
