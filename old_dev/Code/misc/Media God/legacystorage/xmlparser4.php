<?php

// Set error reporting to ignore notices
error_reporting(E_ALL ^ E_NOTICE);

require_once 'XML/Unserializer.php';

$doc = file_get_contents('sigs.xml');

$Unserializer = &new XML_Unserializer();

$status = $Unserializer->unserialize($doc);

if (PEAR::isError($status)) {

	die($status->getMessage());
}

echo '<pre>';
print_r($Unserializer->getUnserializedData());
echo '</pre>';
?>
