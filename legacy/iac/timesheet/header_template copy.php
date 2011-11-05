<html>
	<head>
		<title>Time Sheets: IAC Professionals</title>
		<style>
			body
			{
				background: #EEE;
				text-align: center;
				color: #333;
				font-family: "Lucida Grande", Arial, Tahoma, sans-serif;
				margin: 0;
			}
			
			a img
			{
				border: none;
			}
			
			a
			{
				color: #333;
				text-decoration: none
			}
			
			a:hover
			{
				text-decoration: underline;
			}
			
			.center
			{
				margin: 0 auto;
				text-align: left;
				width: 860px;
			}
			
			#header
			{
				height: 90px;
				background: url("images/headerBg.jpg") repeat-x #555;
			}
			
			#navigation
			{
				background: url("images/navigationBg.jpg") repeat-x;
				height: 45px;
				line-height: 47px;
				color: #FFF;
			}
			
			#navigation ul {
				padding: 0;
				margin: 0;
			}
			
			#navigation li {
				display: inline;
				list-style-type: none;
				line-height: 45px;
			}
			
			#navigation a {
				font-weight: bold;
				color: #FFF;
				text-decoration: none;
				padding: 0 8px 0 8px;
			}
			
			#navigation a:hover {
				text-decoration: underline;
			}
			
			#content
			{
				background: #FFF;
				padding: 30px;
				border-left: 5px solid #DDD;
				border-right: 5px solid #DDD;
			}
			
			#footer
			{
				font-size: small;
				font-weight: bold;
				height: 30px;
				line-height: 30px;
				padding-left: 15px;
				border-top: 5px solid #DDD;
				padding-bottom: 20px;
			}
			
			#footer a
			{
				color: #BBB;
				text-decoration: none;
			}
			
			#footer a:hover
			{
				color: #0079d6;
				text-decoration: underline;
			}
			
			h1
			{
				font-size: 1.4em;
				padding: 0px;
				margin: 5px 0 10px 0;
				color: #0079d6;
			}
			
			/*.table_heading
			{
				background: url("images/tableHeaderBg.jpg") repeat-x;
				color: #FFF;
				font-size: small;
				font-weight: bold;
			}*/
			
			.report_table td
			{
				font-size: small;
				padding: 5px 15px 5px 10px;
			}
			
			.report_row_end td
			{
				border-top: 2px solid #333;
			}
			
			.report_heading
			{
				background: #EEE;
				color: #333;
				font-weight: bold;
			}
			
			.report_heading td
			{
				border-bottom: 2px solid #DDD;
			}
			
			.table_heading
			{
				background: #EEE;
				color: #333;
				font-size: small;
				font-weight: bold;
			}
			
			.table_heading td
			{
				border-top: 1px solid #E0E0E0;
				/*border-bottom: 3px solid #79d600;*/
				border-bottom: 3px solid #0079d6;
			}
			
			.data_table td
			{
				font-size: small;
				padding: 5px 15px 5px 10px;
			}
			
			.table_row td
			{
				border-bottom: 2px solid #EEE;
			}
			
			.color_green
			{
				font-weight: bold;
				background-color: #79d600;
				padding: 2px 5px 2px 5px;
				color: #1b5800;
			}
			
			.color_red
			{
				font-weight: bold;
				background-color: #d60000;
				padding: 2px 5px 2px 5px;
				color: #ffc7c7;
			}
			
			.color_blue
			{
				font-weight: bold;
				background-color: #0079d6;
				padding: 2px 5px 2px 5px;
				color: #b6ecf7;
			}
			
			.color_gray
			{
				font-weight: bold;
				background-color: #858585;
				padding: 2px 5px 2px 5px;
				color: #EEE;
			}
			
			.color_yellow
			{
				font-weight: bold;
				background-color: #d6ce00;
				padding: 2px 5px 2px 5px;
				color: #6b6700;
			}
			
			.large_link
			{
				font-size: normal;
				padding: 2px;
			}
			
			a.large_link
			{
				text-decoration: underline;
				color: #0079d6;
			}
			
			a.large_link:hover
			{
				color: #FFF;
				background: #0079d6;
			}
			
			/*.large_link
			{
				font-size: medium;
				background: #EEE;
				border: 1px solid #DDD;
				padding: 2px 4px 2px 4px;
			}
			
			a.large_link
			{
				text-decoration: none;
				color: #666;
			}
			
			a.large_link:hover
			{
				color: #FFF;
				background: #0079d6;
				border: 1px solid #0079d6;
			}*/
			
			#content table a
			{
				color: #333;
				text-decoration: none;
			}
			
			#content table a:hover
			{
				text-decoration: underline;
			}
			
			#content table .link_button a
			{
				color: #858585;
				background: #FFF;
				font-weight: bold;
				font-size: small;
				padding: 2px;
				text-decoration: none;
			}
			
			#content table .link_button a:hover
			{
				color: #FFF;
				background: #858585;
				text-decoration: none;
			}
			
			#newClient legend
			{
				font-size: normal;
				padding: 2px;
				color: #666;
				font-weight: bold;
			}
			#newClient table
			{
				font-size: small;
				color: #333;
			}
			
			#newClient .formTitle
			{
				font-weight: bold;
			}
			
			#newClient .example
			{
				color: #666;
			}
			
			#newClient .formAreaThin
			{
				border: 1px solid #666;
				width: 220px;
			}
			
			.large_button input
			{
				width: 200px;
				height: 30px;
				border-bottom: 2px solid #999;
				border-right: 2px solid #999;
				border-top: 2px solid #DDD;
				border: 2px solid #DDD;
				background: #EEE;
				font-weight: bold;
				font-size: normal;
				color: #444;
			}
		</style>
	</head>
	<body>
		<div id="header">
			<div class="center">
				<img src="images/headerLogo.jpg" alt="Time Sheets">
			</div>
		</div>
		<div id="navigation">
			<? include( "navigation.php" ); ?>
		</div>
		<div class="center">
			<div id="content">
