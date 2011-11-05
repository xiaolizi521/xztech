<?
/**
 * Author:   	Cory Becker
 * Date:   	 	September 21, 2007
 * Company:		Becker Web Solutions, LLC
 * Website:	 	www.beckerwebsolutions.com
 *
 * Description:
 *					Header
 */

// Include
include( "code/config.php" );

session_start();

$_SESSION['username'] == "";
$_SESSION['password'] == "";
$_SESSION['p_level'] == "";

session_destroy();

?>
<html>
	<head>
		<title>Project Management System - IAC Professionals</title>
		<style>
			body
			{
				background: #EEE;
				text-align: center;
				color: #333;
				font-family: "Lucida Grande", Arial, Tahoma, sans-serif;
				margin: 0;
			}
			
			.center
			{
				margin: 0 auto;
				text-align: left;
				width: 820px;
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
				color: #333;
			}
			
			.table_heading
			{
				background: url("images/tableHeaderBg.jpg") repeat-x;
				color: #FFF;
				font-size: small;
				font-weight: bold;
			}
			
			.table_heading td
			{
				border-bottom: 3px solid #79d600;
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
			
			.link_button
			{
				font-size: xx-small;
				font-weight: bold;
				background: #EEE;
				border: 1px solid #DDD;
				padding: 2px 4px 2px 4px;
			}
			
			a.link_button
			{
				text-decoration: none;
				color: #666;
			}
			
			a.link_button:hover
			{
				color: #FFF;
				background: #0079d6;
				border: 1px solid #0079d6;
			}
			
			#content a
			{
				color: #333;
				text-decoration: none;
			}
			
			#content a:hover
			{
				background: url("images/hoverBg.jpg") no-repeat #fffeef;
			}
		</style>
		<meta http-equiv="refresh" content="2;url=index.php" />
	</head>
	<body>
		<div id="header">
			<div class="center">
				<img src="images/headerLogo.jpg" alt="Time Sheets">
			</div>
		</div>
		<div id="navigation">
			<? include( "navigation_login.php" ); ?>
		</div>
		<div class="center">
			<div id="content">
