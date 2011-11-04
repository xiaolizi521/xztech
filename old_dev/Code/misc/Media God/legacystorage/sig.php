<?
include "config.php";
$_GET['u'] = mysql_real_escape_string($_GET['u']);
$a = mysql_fetch_assoc(mysql_query("SELECT `img` FROM `whatpulse` WHERE `user` = '$_GET[u]'")) or die(mysql_error());
	header("Content-type: image/png");
echo $a[img];
$page = "Signature Image";
$action = "Viewing Image";
include "access.php";

?>