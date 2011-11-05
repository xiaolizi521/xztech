<?php

include "includes/class.mysql.php";
$db = new DB("localhost",'backups','password','backups');

echo <<<HTML

<html>
    <head>
        <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen, projection">
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print">	
        <!--[if lt IE 8]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen, projection"><![endif]-->
        <style type="text/css">
            ul{
                list-style-type: none;
                margin-left: 0px;
                padding-left: 0px;
            }
        </style>
    </head>
    <title>GVO Inventory & Backup Management</title>
    <body>
    
    <div class="container">
    	<div class="span-24 last">
    		<h1>GVO Inventory & Backup Management
    	</div>
    	<div class="span-4">
    		<ul>
    		    <li><a href="index.php">Index</a></li>
        		<li><a href="addhost.php">Add Host</a></li>
        		<li><a href="edithost.php">Edit Host</a></li>
        		<li><a href="addbkup.php">Add Backup Location</a></li>
        		<li><a href="editbkup.php">Edit Backup Location</a></li>
        		<li><a href="list.php">List Hosts</a></li>
    		</ul>
    	</div>
    	<div class="span-16" id='main'>
HTML;
