<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
	<title>The Mirage Guild Recruitment</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="screen" href="style/stylesheet.css"/>
</head>

<body class="wordpress k2" >

<div id="page">

		<div id="header">

		<h1>Mirage Guild [Alleria] - Recruitment Form</h1>
		<p class="description">Recruitment Form for our guild on Alleria</p>

		<ul class="menu">
						<li class="current_page_item"><a href="#" title="#">NA</a></li>

						<li class="current_page_item"><a href="#" title="#">NA</a></li>
			<li class="admintab"><a href="#">#</a></li>		</ul>

	</div>

		<hr />
<div class="content">
	
	<div id="primary">
		<div id="current-content">

			<div id="primarycontent" class="hfeed">
<table class="prospects">
<tr>
<td class="head">ID</td>
<td class="head">Name</td>
<td class="head">Age</td>
<td class="head">Character Name</td>
<td class="head">Class</td>
<td class="head">Level</td>
<td class="head">Spec</td>
</tr>
		
<?php

require_once "class.db.php";

// Connect to the Database
try {
	$var = new DB('localhost', 'mirageapps', 'mirageapps', 'apps');
}

// Catch any errors thrown if connection fails.

catch(ConnectException $exception) {
	echo "Connection Error\n";
	var_dump($exception->getMessage());
}

// Catch any other errors that may have occured.

catch(Exception $exception) {
		echo "Other Script Error\n";
	var_dump($exception->getMessage());
}


$query = "SELECT `id`,`name`,`age`,`charname`,`class`,`level`,`spec` 
from applications";

try {
	$result = $var->query($query);
}

// Catch any errors thrown if query fails.

catch(QueryException $exception) {
	echo "Query Error\n";
	var_dump($exception->getMessage());
}

// Catch any other errors that may have occured.

catch (Exception $exception) {
		echo "Other Script Error\n";
	var_dump($exception->getMessage());
}

$color1 = "#FFFFFF";
$color2 = "#DDDDD";


while($data = $result->fetch_assoc()) {
	
	echo "<tr>";
	
	foreach($data as $key => $value) {
		if ($key == "id") {
			
			echo "<td><a 
href='prosdetail.php?id=".$value."'>".$value."</a></td>";
		}
		
		else { echo "<td>" . $value . "</td>";}
	}
	
	echo "</tr>";
}

?>
</table>

</div><!-- #primarycontent .hfeed -->

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<hr />

<div class="secondary">
		<div class="sb-latest">

		<h2>Menu</h2>

		<ul>
				<li><a href='#' title='#'>Work in progress</a></li>
		</ul>
	</div>
		<div class="sb-links">
		<ul>
				<li id="linkcat-2" class="linkcat"><h2>Links</h2>
	<ul>
<li><a href="#">Work in Progress</a></li>

	</ul>
</li>
		</ul>
	</div>
</div>
<div class="clear"></div>

	
</div> <!-- .content -->

	<div class="clear"></div>

</div> <!-- Close Page -->

<hr />
<p id="footer">
	<small>
		Mirage is a <a href="http://www.worldofwarcraft.com" title="World of Warcraft">World of Warcraft</a> Guild on the Alleria Server.<br />
		All content Copyright © 2007 Adam Hubscher AKA OffbeatAdam. <br />
		World of Warcraft, The Burning Crusade, Alleria, and all related content is ©2004-2007 Blizzard Entertainment, Inc. All rights reserved.
	</small>
</p>

</body>
</html>