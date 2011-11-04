<?
$logdate = date(U);
$prevdate = date(U) - 7200;
$q = mysql_query("SELECT * FROM `whatpulse` WHERE `ip` = '$ip' AND `logdate` > '$prevdate' AND `user` NOT LIKE '$_COOKIE[user]'");
$x = mysql_num_rows($q);
if ($x) {
echo "We have detected that you are attempting to use two user accounts. Your attempt has been logged.";
}
?>