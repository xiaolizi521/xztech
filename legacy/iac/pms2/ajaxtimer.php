<?

session_start();

include( "header_functions.php" );

?>
<html>
<head>
    <title>Timer</title>
	<style type="text/css">
		body
		{
			background: #f1edbd;
			font-family: verdana;
		}
		
		#content
		{
			width: 200px;
		}
		
		#clock
		{
			font-size: 22px;
			font-weight: bold;
			display: inline;
		}
		
		#timer
		{
			text-align: center;
			border: 1px solid #000;
			padding: 5px 5px 10px 5px;
		}
		
		.timer_stopped
		{
			background: #ffffcc;
		}
		
		.timer_running
		{
			background: #ccffcc;
		}
		
		#start
		{
			border: 1px solid #333;
			padding: 0px 15px;
			font-size: 12px;
			font-weight: bold;
		}
		
		#start a
		{
			color: #000;
			text-decoration: none;
		}
		
		#stop
		{
			border: 1px solid #333;
			padding: 0px 15px;
			font-size: 12px;
			font-weight: bold;
		}
		
		#stop a
		{
			color: #FFF;
			text-decoration: none;
		}
		
		.green
		{
			background: #93d630;
			display: inline;
			
		}
		
		.red
		{
			background: #cc3430;
			display: inline;

		}
		
		.hidden
		{
			background: #CCC;
			display: none;
		}
		
		#reset
		{
			border: 1px solid #333;
			padding: 0px 15px;
			background: #EEE;
			font-size: 12px;
			font-weight: bold;
			display: inline;
		}
		
		#reset a
		{
			color: #000;
			text-decoration: none;
		}
		
		h1
		{
			margin: 15px 0 2px 0;
			font-size: 11px;
		}
		
		input, select, textarea
		{
			border: 1px solid #000;
			font-size: 11px;
			font-family: verdana;
			width: 100%;
		}
		
		#notes
		{
			width: 100%;
			height: 110px;
		}
		
		#log
		{
			border: 1px solid #000;
			background: #ffffcc;
			padding: 6px;
			margin-top: 10px;
			text-align: center;
			font-size: 12px;
			font-weight: bold;
		}
		
		#log a
		{
			text-decoration: none;
			color: #000;
		}
	</style>
	
	<script type="text/javascript" src="js/prototype.js"></script>
	<script language="JavaScript">
	<!--
	
	var sec = 0;
	var min = 0;
	var hour = 0;
	var total = hour + ( min / 60 );
	var employee_id = <?= $employeeArray['id'] ?>;
	var project_id = 0;
	var task_id = 0;
	var active = 0;
	var timer;
	var statusUpdateTimer;
	var processing = 0;
	
	var timerEnabled = false;
	var timerKey = "";
	var getKeyAttempts = 0;
	
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
	
	function stop()
	{
		clearTimeout(timer);
		clearTimeout(statusUpdateTimer);
		active = 0;
		$('start').className = 'green';
		$('stop').className = 'hidden';
		$('timer').className = 'timer_stopped';
	}
	
	function reset()
	{
		clearTimeout(timer);
		clearTimeout(statusUpdateTimer);
		active = 0;
		$('start').className = 'green';
		$('stop').className = 'hidden';
		$('timer').className = 'timer_stopped';
		sec = 0;
		hour = 0;
		min = 0;
		total = 0;
		$("clock").innerHTML = PadDigits(hour,2)+":"+PadDigits(min,2)+":"+PadDigits(sec,2);
	}
	
	function start()
	{
		if( timerEnabled )
		{
			if( active == 0 )
			{
				timer = setTimeout('updateTime()',1000);
				statusUpdateTimer = setTimeout('statusUpdate()',1000);
				active = 1;
				$('start').className = 'hidden';
				$('stop').className = 'red'; 
				$('timer').className = 'timer_running';
			}
		}
		else
		{
			alert( "Cannot start timer until authorization received from server.  Please try again in a few seconds." );
		}

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
		
		total = hour + ( min / 60 );

		$("clock").innerHTML = PadDigits(hour,2)+":"+PadDigits(min,2)+":"+PadDigits(sec,2);
		timer = setTimeout('updateTime()',1000);
	}
	
	function getTasks()
	{
		$("task").options.length = 0;
		
		new Ajax.Request('ajax.php',
		  {
		    method:'get',
			parameters: {	action: "getTasks", 
						 	employee: employee_id, 
							project: $('plan_id').value },
						
		    onSuccess:
				function(transport)
				{
		      		var response = transport.responseText.evalJSON( true );
					// Clear all options										
					for( var i = 0; i <= response.length; i++ )
					{
						$("task").options[i] = new Option( response[i]['name'], response[i]['value'] );
					}
		    	},
		
		    onFailure: 
				function()
				{
					alert('Something went wrong...');
				}
		  });
	}
	
	function logTime()
	{		
		if( timerEnabled )
		{
			if( min < 1 && hour < 1 )
			{
				alert( "You must record more than one minute to log this entry." );
				return 0;
			}

			if( processing == 0 )
			{
				processing = 1;
			}
			else
			{
				alert( "Do not click \"Log Hours\" multiple times. The time is being recorded." );
				return 0;
			}

			if( $('task').value == "" )
			{
				alert( "Select a task before logging hours" );
				processing = 0;
				return 0;
			}

			// Disable submit button
			$('log').className = 'hidden';
			
			alert( "Logging time..." );
			
			new Ajax.Request('ajax.php',
			  {
			    method:'get',
				parameters: {	action: "logTime",
								notes: $('notes').value,
								project: $('plan_id').value,
								task: $('task').value,
								key: timerKey,
								hours: total,
								employee: employee_id },

			    onSuccess:
					function(transport)
					{				
			      		var response = transport.responseText.evalJSON( true );
						
						if( response['result'] == "success" )
						{
							clearTimeout(timer);
							alert( "The time has been logged." );
							$('notes').value = "";
							active = 0;
							$('start').className = 'green';
							$('stop').className = 'hidden';
							$('timer').className = 'timer_stopped';
							sec = 0;
							hour = 0;
							min = 0;
							total = 0;
							$("clock").innerHTML = PadDigits(hour,2)+":"+PadDigits(min,2)+":"+PadDigits(sec,2);
						}
						else
						{
							alert( "Something went wrong... " + " Response: " + response['result'] );
							processing = 0;
						}
						
						// Get new timer key
						getKey();
					},

			    onFailure: 
					function()
					{
						alert('Something went wrong...');
						processing = 0;
					}
			  });

			processing = 0;

			$('log').className = '';
		}
		else
		{
			alert( "Cannot start timer until authorization received from server.  Please try again in a few seconds." );
		}
	}
	
	// Send status update to server -- to show which employees are active
	function statusUpdate()
	{
		new Ajax.Request('ajax.php',
		  {
		    method:'get',
			parameters: {	action: "statusUpdate",
							project: $('plan_id').value,
							employee: employee_id,
							task: $('task').value,
							hours: total },
			
		    onSuccess:
				function(transport)
				{					
		      		// Do nothing
				},

		    onFailure: 
				function()
				{
					// Do nothing
				}
		  });
		
		statusUpdateTimer = setTimeout('statusUpdate()',60000);
	}
	
	function getKey()
	{
		getKeyAttempts++;
		
		if( getKeyAttempts < 4 )
		{
			timerEnabled = false;
			new Ajax.Request('ajax.php',
			  {
			    method:'get',
				parameters: { action: "getKey" },
			    onSuccess:
					function(transport)
					{					
			      		var response = transport.responseText.evalJSON( true );
						//alert( "Timer ready: " + response['key'] );
						timerKey = response['key'];
						timerEnabled = true;
						getKeyAttempts = 0;
					},

			    onFailure: 
					function()
					{
						getKey();
					}
			  });
		}
		else
		{
			alert('Something went wrong while getting a transaction key from the server. Please close this window and trying opening a new pop up timer.');
		}
		
	}
	
	Event.observe(window, 'load', function() {
		// Get key for active timer session
		getKey();
	});
	
	//-->
	</script>
	
</head>
<body>
	<div id="content">
		<div id="timer" class="timer_stopped">
			<div id="clock">00:00:00</div>
			<div>
				<div id="start" class="green"><a href="#" onclick="start();">Start</a></div>
				<div id="stop" class="hidden"><a href="#" onclick="stop();">Stop</a></div>
				<div id="reset" class="hidden"><a href="#" onclick="reset();">Reset</a></div>
			</div>
		</div>
		
		<form>
		<div>
			<h1>Plan:</h1>
			<select id="plan_id" onchange="getTasks();">
				<option value="clear">Select a Client/Plan</option>
				<?	
					if( $employeeArray['type'] == "super-manager" )
						$db->query( "SELECT plan_classification.name AS classification_name,
       							client_plan.id AS id,
       							client.organization AS organization
       							FROM client_plan
  								INNER JOIN plan_classification ON client_plan.classification_id = plan_classification.id
  								INNER JOIN client ON client_plan.client_id = client.id
  								WHERE client_plan.status != 'completed' ORDER BY organization, classification_name ASC;" );
					else
						$db->query( "SELECT distinct plan_classification.name AS classification_name,
       							client_plan.id AS id,
       							client.organization AS organization,
       							plan_assignment_employees.employee_id as employee_id
       							FROM client_plan
      							INNER JOIN plan_classification ON client_plan.classification_id = plan_classification.id
      							INNER JOIN client ON client_plan.client_id = client.id
      							INNER JOIN plan_assignment ON plan_assignment.plan_id = client_plan.id
      							INNER JOIN plan_assignment_employees ON plan_assignment_employees.plan_assignment_id = plan_assignment.id
  								WHERE plan_assignment_employees.employee_id = ".$employeeArray['id']." AND client_plan.status != 'completed' ORDER BY organization, classification_name ASC" );
						
				?>
				
				<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):  ?>
					<option value="<?= $row['id'] ?>">(<?= $row['organization'] ?>) <?= $row['classification_name'] ?></option>
				<? endwhile; ?>
			</select>
			
			<h1>Task:</h1>
			<select id="task">
			
			</select>
			
			<h1>Notes:</h1>
			<textarea id="notes"></textarea>
			
			<div id="log"><a href="#" onclick="logTime();">Log Hours</a></div>
		</div>
			
	</div>
</body>
</html>
