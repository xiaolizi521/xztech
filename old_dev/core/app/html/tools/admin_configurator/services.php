<?
require_once("menus.php");

if($computer_number=="")
{
	ForceReload("hardware.php?server_type=".urlencode($server_type)."&customer_number=$customer_number");
}
$product_page="services";
require_once("common.php");

$page_title="Configure Services";
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
include($Configurator->getServicesInclude());
?>
<?= page_stop() ?>
</html>
