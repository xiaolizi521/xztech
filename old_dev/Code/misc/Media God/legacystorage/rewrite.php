<?
include "config.php";
$q = mysql_query("SELECT * FROM `whatpulse`");
while ($a = mysql_fetch_assoc($q)) {
$userx = $a[userx] - $a[users];
if ($userx < 0) {
$userx = '1';
}
$tkcx = $a[tkcx] - $a[tkcs];
if ($tkcx < 0) {
$tkcx = '1';
}
$tmcx = $a[tmcx] - $a[tmcs];
if ($tmcx < 0) {
$tmcx = '1';
}
$rankx = $a[rankx] - $a[ranks];
if ($rankx < 0) {
$rankx = '1';
}
$tnamex = $a[tnamex] - $a[tnames];
if ($tnamex < 0) {
$tnamex = '1';
}
$tkeysx = $a[tkeysx] - $a[tkeyss];
if ($tkeysx < 0) {
$tkeysx = '1';
}
$tclicksx = $a[tclicksx] - $a[tclickss];
if ($tclicksx < 0) {
$tclicksx = '1';
}
$trankx = $a[trankx] - $a[tranks];
if ($trankx < 0) {
$trankx = '1';
}
$countryx = $a[countryx] - $a[countrys];
if ($countryx < 0) {
$countryx = '1';
}
mysql_query("UPDATE `whatpulse` SET `userx` = '$userx', `tkcx` = '$tkcx', `tmcx` = '$tmcx', `rankx` = '$rankx', `tnamex` = '$tnamex',
 `tkeysx` = '$tkeysx', `tclicksx` = '$clicksx', `trankx` = '$trankx', `countryx` = '$countryx' WHERE `id` = '$a[id]'");
}
?>