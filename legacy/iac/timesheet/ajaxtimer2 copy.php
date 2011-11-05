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
							project: $('project').value },
						
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
		if( min < 1 && hour < 1 )
		{
			alert( "You must record more than one minute to log this entry." );
			return;
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
		
		new Ajax.Request('ajax.php',
		  {
		    method:'get',
			parameters: {	action: "logTime",
							notes: $('notes').value,
							project: $('project').value,
							task: $('task').value,
							hours: total },
			
		    onSuccess:
				function(transport)
				{					
		      		var response = transport.responseText.evalJSON( true );
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
	
	// Send status update to server -- to show which employees are active
	function statusUpdate()
	{
		new Ajax.Request('ajax.php',
		  {
		    method:'get',
			parameters: {	action: "statusUpdate",
							project: $('project').value,
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
				<div id="reset"><a href="#" onclick="reset();">Reset</a></div>
			</div>
		</div>
		
		<form>
		<div>
			<h1>Project:</h1>
			<select id="project" onchange="getTasks();">
				<option value="clear">Select a project</option>
				<?	
					if( $employeeArray['type'] == "super-manager" )
						$db->query( "SELECT project.name AS name, 
						project.id AS id,
						client.organization AS organization
						FROM project
						LEFT JOIN client ON project.client_id = client.id
						WHERE project.status != \"completed\" ORDER BY project.name ASC" );
					else
						$db->query( "SELECT project.name AS name, 
						project.id AS id,
						client.organization AS organization,
						project_employees.id as employee_id
						FROM project 
						LEFT JOIN client ON project.client_id = client.id
						LEFT JOIN project_employees ON project_employees.project_id = project.id WHERE project_employees.employee_id = ".$employeeArray['id']." AND project.status != \"completed\" ORDER BY project.name ASC" );
						
				?>
				
				<? while( $row = mysql_fetch_assoc( $db->result['ref'] ) ):  ?>
					<option value="<?= $row['id'] ?>">(<?= $row['organization'] ?>) <?= $row['name'] ?></option>
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
