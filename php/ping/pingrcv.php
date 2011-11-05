<?php

define("HOSTNAME", "onms-1.dfw1");
if(empty($_POST)):

	die("This script can only be called by CORE. Please utilize the ping tool from CORE.");
endif;
?>
<HTML>
<HEAD>
<title>Rackwatch Ping Utility for <? echo HOSTNAME; ?></title>
</HEAD>
<body>
<h1>Connectivity Test</h1>
<h2>Poller: <? echo HOSTNAME; ?></h2>
<p>
<?
	system("/bin/ping -c 5 $_POST['host']");
?>
</p>
</body>