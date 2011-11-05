<?php
include 'callimage.class.php';
header("content-type: image/PNG");

$foo = new CallImage();

$foo->displayImage();

?>
