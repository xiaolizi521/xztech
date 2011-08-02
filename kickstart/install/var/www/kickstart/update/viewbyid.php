<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--
Copyright: Daemon Pty Limited 2006, http://www.daemon.com.au
Community: Mollio http://www.mollio.org $
License: Released Under the "Common Public License 1.0", 
http://www.opensource.org/licenses/cpl.php
License: Released Under the "Creative Commons License", 
http://creativecommons.org/licenses/by/2.5/
License: Released Under the "GNU Creative Commons License", 
http://creativecommons.org/licenses/GPL/2.0/
-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Kickstart Database Updates</title>
<link rel="stylesheet" type="text/css" href="css/main.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="css/ie6_or_less.css" />
<![endif]-->
<script type="text/javascript" src="js/common.js"></script>
</head>
<body id="type-a">
<div id="wrap">

	<div id="header">
		<div id="site-name">Kickstart Database Updates</div>
		<div id="search">
		</div>
		<ul id="nav">
		<li class="first"><a href="./kickstartupdate.html">Home</a></li>
		<li class="active"><a href="#">Update</a>
			<ul>
			<li class="first"><a href="add.html">Add an Entry</a></li>
			<li class="first"><a href="delete.php">Delete an Entry</a></li>
			</ul>
		</li>
		<li class="first"><a href="#">Views</a>
			<ul>
			<li class="first"><a href="viewbyid.php">View vlans Table by id</a></li>
			<li class="first"><a href="viewbypub.php">View vlans Table by public network</a></li>
			<li class="first"><a href="viewbypriv.php">View vlans Table by private network</a></li>
			</ul>
		</li>	
		</ul>
	</div>
	
	<div id="content-wrap">
	
		<div id="content">
			
			
			<div class="featurebox"><h3>Table</h3><p>
			<table class="table1">
			<thead>
				<tr>
				<th colspan="3">vlans</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				<th>id</th>
				<th>public_network</th>
				<th>private_network</th>
				</tr>
				<?php
				$db = pg_connect('host=127.0.0.1 dbname=kickstart user=kickstart password=l33tNix') or die("Couldn't Connect.");

				$query = "SELECT * FROM vlans order by id";

				$result = pg_query($query) or die("Couldnt' execute query.");
				$num = pg_numrows($result);
				
						while($row=pg_fetch_array($result)){
							echo "</td><td>";
							echo $row['id'];
							echo "</td><td>";
							echo $row['public_network'];
							echo "</td><td>";
							echo $row['private_network'];
							echo "</td></tr>";
						}
					?>

			</tbody>
			</table>
			</div>
			
			<hr />
		
			<div id="footer">
			<p>ServerBeach - a Peer1 company</p>
			</div>
		
		</div>
		
	</div>

</div>
</body>
</html>