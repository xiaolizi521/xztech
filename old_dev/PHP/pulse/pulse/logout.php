<?
session_start();
$_SESSION = array();
include "config.php";
include "designtop.php";

echo "You are now logged out.";
include "menu.php";
include "designbottom.php";
session_destroy();
?>