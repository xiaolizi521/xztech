<?
require_once("CORE_app.php");
require_once("CoreSmarty.php");

$my_smarty = new CoreSmarty();
$my_smarty->assign('name','Sameer');
$my_smarty->display('index.tpl');
?>
