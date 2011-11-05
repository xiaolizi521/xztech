<?php
require_once("menus.php");
if( empty( $computer_number ) ) {
	ForceReload("hardware.php3?server_type=".urlencode($server_type)."&customer_number=$customer_number");
}
$product_page="software";
require_once("common.php");

$page_title="Configure Software";
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
include($Configurator->getSoftwareInclude());
?>
<?= page_stop() ?>
</html>
