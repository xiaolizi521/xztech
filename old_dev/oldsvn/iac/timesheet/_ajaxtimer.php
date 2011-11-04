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
</head>
<body>
	<div id="content">
		<p><strong>Not available</strong></p>
		<p>The pop up timer is not working. It will be available within the hour.</p>			
	</div>
</body>
</html>
