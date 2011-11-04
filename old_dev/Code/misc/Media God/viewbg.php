<?
session_start();
if (!$_SESSION['username']) {
header("Location: login.php");
}
include "config.php";
include "designtop.php";


?>
<form action='' method='post'>
<table cellspacing='0' cellpadding='0' class='bgtab'>
<tr>
<td class='bgt' align='center'>Path</td>
<td class='bgt' align='center'>Name</td>
<td class='bgt' align='center'>Size</td>
<td class='bgt' align='center'>Delete?</td>
</tr>
<?
$q = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `whatpulse` WHERE `user` = '$_SESSION[username]'"));
if ($_GET['delete']) {
$r = mysql_num_rows(mysql_query("SELECT * FROM `backgrounds` WHERE `userid` = '$q[id]' AND `userid` != '1' AND `id` = '$_GET[id]'"));
if ($r) {
mysql_query("DELETE FROM `backgrounds` WHERE `id` = '$_GET[id]'");
}
else {
echo "That image doesn't belong to you.";
}
}


$r = mysql_query("SELECT * FROM `backgrounds` WHERE `userid` = '$q[id]' AND `userid` != '0' ORDER BY `size` DESC") or die(mysql_error());
$s = mysql_num_rows($r);
if ($s) {
while ($bg = mysql_fetch_array($r)) {
$x++;
list($width, $height, $type, $attr) = @getimagesize($bg['path']);
$z+=$bg[size];
echo "<tr>
<td class='bgpri' align='center' valign='top'><img src='$bg[path]' alt='$bg[name]'><br>($width x $height)</td>
<td class='bgpri' align='center' valign='top'>$bg[name]</td>
<td class='bgpri' align='center' valign='top'>&nbsp;$bg[size]</td>
<td class='bgpri' align='center' valign='top'><a href='$_SERVER[PHP_SELF]?delete=1&id=$bg[id]'>Delete</a></td>
</tr>";
}
}
else {
echo "<td colspan='4' class='bgpri'>You have no backgrounds uploaded</td>";
}
?>
</tr>
</table>
</form>
</body>
</html>
<?
include "menu.php";
include "designbottom.php";
