<?
session_start();
 ?>
<html>
<head>
<link rel='stylesheet' href='style.css'>
<title>WSI - Whatpulse Signature Images</title>
<script src="javascripts/prototype.js" type="text/javascript"></script>
<script src="javascripts/effects.js" type="text/javascript"></script>
<script src="javascripts/dragdrop.js" type="text/javascript"></script>


<script>
function counter() {
var count = parseFloat(document.getElementById('sig-hits').innerHTML) + 1
document.getElementById('sig-hits').innerHTML = count + " views and counting!"
start();
}
function start() {
setTimeout("counter()",650);
}
</script>
</head>
<body onLoad="start()">
<div align='center'>
<div align='center' class='logo'><a href='index.php'><img src='img2/logo.jpg' border='0'></a></div>
<div class='count'>
<?
echo  mysql_num_rows(mysql_query("SELECT * FROM `whatpulse`")); ?> users and
<span id='sig-hits'><?
$sig = mysql_fetch_assoc(mysql_query("SELECT * FROM `logging` WHERE `page` = 'Signature'"));
echo $sig['count'];
?> views and counting!</span></div>
</div>
<div class='content'>
