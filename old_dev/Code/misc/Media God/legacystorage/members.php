<html>
<head>
<title>Whatpulse Thingo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type='text/css'>
.members {
border:1px solid black;
font:7pt Verdana;
}
.row1 {
background:#D3E8F3;
padding:5px;
}
.row2 {
background:#BACED8;
color:black;
padding:5px;
}
.footer {
margin-top:20px;
font:7pt Verdana;
}
.header {
background:black;
color:white;
}
</style>
</head>

<body>
<table cellpadding='0' cellspacing='0' width='50%' class='members'>
<tr><td align='center' class='header'><strong>Rank</strong></td><td align='center' class='header'><strong>Username</strong></td><td align='center' class='header'><strong>Total Key Count</strong></td><td align='center' class='header'><strong>Total Mouse Clicks</strong><td align='center' class='header'><strong>Whatpulse Rank</strong></td></tr>
<?
include "config.php";
$one = mysql_query("SELECT * FROM `whatpulse` ORDER BY `tkc` DESC");
while ($two = mysql_fetch_array($one)) {
$user = $two['user'];
$tkc = $two['tkc'];
$tmc = $two['tmc'];
$rank = $two['rank'];
$x++;
if ($x % 2) {
echo "<tr><td align='center' class='row1'>$x</td><td align='center' class='row1'>$user</td><td align='center' class='row1'>$tkc</td><td align='center' class='row1'>$tmc</td><td align='center' class='row1'>$rank</td></tr>";
}
else {
echo "<tr><td align='center' class='row2'>$x</td><td align='center' class='row2'>$user</td><td align='center' class='row2'>$tkc</td><td align='center' class='row2'>$tmc</td><td align='center' class='row2'>$rank</td></tr>";
}
}
?>
</table>
<div class='footer' align='center'>Credits go to the KillaNet Technology Team!</div>
</body>
</html>
