<?
$product_page="hardware";
require_once("common.php");
require_once("menus.php");

if (!empty($Continue_x))
{
	//They are moving on to software
	Header("Location: services.php?server_type=".urlencode($server_type)."&first_load=1&customer_number=$customer_number&computer_number=$computer_number\n\n");
	exit();
}
if (!empty($ViewCart_x)) {
    ForceReload("network_map.php?customer_number=$customer_number&computer_number=$computer_number");
}

$page_title="Configure Hardware";
?>
<html id="mainbody">
<head>
<title><?=$page_title?></title>
<link HREF="/css/core2_basic.css" REL="stylesheet">
<link HREF="/css/configurator.css" REL="stylesheet">
<?= menu_headers() ?>
</head>
<?= page_start() ?>
<?php
include($Configurator->getHardwareInclude());
?>
<?= page_stop() ?>
</html>
<?php
/*
Local Variables:
mode: php
c-basic-offset: 4
End:
*/
?>
