<?php

header("Cache-Control: no-cache");
set_time_limit( ini_get('max_execution_time') );

require( "code/config.php" );
require( "code/XMLGenerator.php" );

// FreshBooks API
$fb = new FreshBooksAPI();
$db = new db();
$rtn = chr(10);

/**
* First grab all active prepaid projects
*
**/
$query   = 	"SELECT * FROM project WHERE category =6 AND STATUS = 'in-progress'";
$result  = mysql_query($query) or die('Error, query failed');

while($projectData = mysql_fetch_array($result, MYSQL_ASSOC)){
	print '--------------------------------------------------------------------<br/>'.$rtn;
	print 'Project ID: '.$projectData['id'].'&nbsp;Name: '.$projectData['name'].'<br/>'.$rtn;
	print 'Description: '.$projectData['description'].'<br/><br/>'.$rtn;
	print 'Limit hours: '.$projectData['limit_hours'].'<br/>'.$rtn;
	$limitHours = $projectData['limit_hours'];

	/**
	* Get hours used
	*
	**/
	$query = "SELECT sum( a.hours ) as total_worked FROM project_task_hours a, project_task b
			  WHERE b.project_id = ".$projectData['id']." AND b.id = a.project_task_id";
	$result2  = mysql_query($query) or die('Error, query failed');
	$row2 = mysql_fetch_array($result2, MYSQL_ASSOC);
	$totalWorked = $row2['total_worked'];

	print 'Hours Worked: '.$totalWorked.'<br/>'.$rtn;
	print '--------------------------------------------------------------------<br/>'.$rtn;

	if (($limitHours - $totalWorked) < 2){
		$query = "SELECT first_name, last_name, email FROM client WHERE id = ".$projectData['client_id'];
		$result3 = mysql_query($query) or die('Error, query failed');
		$clientData = mysql_fetch_array($result3, MYSQL_ASSOC);

		$query = "SELECT sum( a.hours ) as total_worked FROM project_task_hours a, project_task b
			  	 WHERE b.project_id = ".$projectData['id']." AND b.id = a.project_task_id
			  	 AND STR_TO_DATE( a.date, '%m/%d/%Y' ) > DATE_SUB(now(), INTERVAL 10 DAY)";
		print $query;
		$result4 = mysql_query($query) or die('Error, query failed');
		$workHoursData = mysql_fetch_array($result4, MYSQL_ASSOC);

		if ($workHoursData['total_worked'] > 0){
			$workHoursData['tenDayAverage'] = 	$workHoursData['total_worked']/10;
		}else{
			$workHoursData['tenDayAverage'] = 0;
			$workHoursData['total_worked'] = 0;
		}
		print '<br/><br/>'.$workHoursData['total_worked']."->".$workHoursData['tenDayAverage'].'<br/>'.$rtn;

		if (checkForAlertFlag($projectData['id'], $projectData['client_id'], 'LOW_HOURS') == false){
			print 'sending notice';
			sendLowFundsNotice($projectData, $clientData, $workHoursData);
			setAlertFlag($projectData['id'], $projectData['client_id'], 'LOW_HOURS', 1, '');
		}
	}

}


function sendLowFundsNotice($projectData, $clientData, $workHoursData){
	$body = "<html><body>";
	$body .= "<p>Dear ".$clientData['first_name']."&nbsp;".$clientData['last_name']."</p>";
	$body .= "<p>Our systems have indicated that you have less than 2 hours available on your Prepaid Time loaded with IAC Professionals.</p>";
	$body .= "<p>As you know, once time expires, work completely stops with Prepaid projects.</p>";
	$body .= "<p>To avoid interruption with your service, please re-load your time <a href='https://iacprofessionals.com/hire.php?service=administrative&type=prepaid&step=client&project_id=".$projectData['id']."'>HERE</a>.</p>";
	$body .= "<p>Alternatively, to never worry about your time or the progress of your work, please think about upgrading to one of our Retainer or Pay-As-You-Go plans.</p>";
	$body .= "<p>Based on your usage, you have used ".$workHoursData['total_worked']." hours in a 10 day period, that is an average of ".$workHoursData['tenDayAverage']." hours a day! ";
	$body .= "Our basic retainer plan is set for 20 hours a month, or 1 hour a Business day. For only $380.00 ($1.00 less per hour than your current hourly rate) you never have to worry about receiving a notice such as this again.</p>";
	$body .= "<p>If you would like to convert to a retainer plan, just complete the process <a href='https://iacprofessionals.com/hire.php?service=administrative&type=retn&step=client&project_id=".$projectData['id']."'>HERE</a>.</p>";
	$body .= "<p>Thanks for your time - and have a wonderful day!</p>";
	$body .= "<p>Office Manager<br/><br/>";
	$body .= "IAC Professionals<br/>";
	$body .= "Where Helping You is all we do!<br/>";
	$body .= "<a href='www.iacprofessionals.com'>www.iacprofessionals.com</a><br/>";
	$body .= "Tel: 1.786.214.6046<br/>";
	$body .= "Toll Free: 1.877.MY-IAC-VA<br/>";
	$body .= "Fax: 1.786.214.6047<br/>";
	$body .= "</body></html>";


	$to = $clientData['email'].',admin@iacprofessionals.com,toni@iacprofessionals.com,shayne@iacprofessionals.com,carrie@iacprofessionals.com,trish@iacprofessionals.com';
	$subject = 'NOTICE: Prepaid Time Expiring';
	$fromname = 'admin@iacprofessionals.com';
	$fromemail = 'admin@iacprofessionals.com';
	sendmailPHP($to, $subject, $body, $fromname, $fromemail );
}

function checkForAlertFlag($projectId, $clientId, $alertName){
	$rtnValue = false;
	$query = "SELECT * from project_alerts WHERE project_id = $projectId AND client_id = $clientId AND alert_name = '$alertName'";
	print '<br/>'.$query.'<br/>';
	$result = mysql_query($query) or die('Error, query failed');

	if (mysql_num_rows($result) > 0){
		$alertData = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($alertData['alert_value1'] == 1) $rtnValue = true;
		print 'return value is true';
	}
	print '<br/>Return Value = '.$rtnValue.'<br/>';
	return $rtnValue;
}

function setAlertFlag($projectId, $clientId, $alertName, $alertValue1, $alertValue2){
	$query = "SELECT * from project_alerts WHERE project_id = $projectId AND client_id = $clientId AND alert_name = '$alertName'";
	print '<br/>'.$query.'<br/>';
	$result = mysql_query($query) or die('Error, query failed');

	if (mysql_num_rows($result) > 0){
		$query = "UPDATE project_alerts SET alert_value1 = $alertValue1, alert_value2 = '$alertValue2' WHERE project_id = $projectId AND client_id = $clientId AND alert_name = '$alertName'";
		print '<br/>'.$query.'<br/>';
		mysql_query($query) or die('Error, query failed');
	}else{
		$query = "INSERT INTO project_alerts VALUES(null, $projectId, $clientId, '$alertName', $alertValue1, '$alertValue2')";
		print '<br/>'.$query.'<br/>';
	    mysql_query($query) or die('Error, query failed');
	}

}

?>