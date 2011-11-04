<?
if ($page) {
if (mysql_num_rows(mysql_query("SELECT * FROM `logging` WHERE `page` = '$page'"))) {
	$q = mysql_fetch_assoc(mysql_query("SELECT * FROM `logging` WHERE `page` = '$page'"));
	$count = $q['count'] + 1;
mysql_query("UPDATE `logging` SET `count` = '$count' WHERE `page` = '$page'");
}
else {
	mysql_query("INSERT INTO `logging` (`page`,`count`) VALUES ('$page','1')") or die(mysql_error());
}
}
?>
</body>
</html>