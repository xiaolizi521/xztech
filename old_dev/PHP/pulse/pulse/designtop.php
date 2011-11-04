<html>
<head>
<link rel='stylesheet' href='style.css'>
<title>WSI - Whatpulse Signature Images</title>
</head>
<body>
<div align='center'>
<div align='center' class='logo'><img src='img2/logo.jpg'></div>
<div class='count'><?
$req = str_replace(strrchr($_SERVER['REQUEST_URI'],"?"),"",$_SERVER['REQUEST_URI']);
if (mysql_num_rows(mysql_query("SELECT * FROM `logging` WHERE `page` = '$req'"))) {
	$q = mysql_fetch_assoc(mysql_query("SELECT * FROM `logging` WHERE `page` = '$req'"));
	$count = $q['count'] + 1;
mysql_query("UPDATE `logging` SET `count` = '$count' WHERE `page` = '$q[page]'");
}
else {
	mysql_query("INSERT INTO `logging` (`page`) VALUES ('$req')") or die(mysql_error());
}
echo  mysql_num_rows(mysql_query("SELECT * FROM `whatpulse`")); ?> users and counting!</div>
</div>
<div class='content'>
