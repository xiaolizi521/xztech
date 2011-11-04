<?
session_start();
$page = "colour.php";
$action = "Changing colours.";
include "functions.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>WSI - Colors</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
function ec(r,g,b) {
if (r<0) {
r=0;
}
if (g<0) {
g=0;
}
if (b<0) {
b=0;
}
if (r > 255) {
r = 255;
}
if (g > 255) {
g = 255;
}
if (b > 255) {
b = 255;
}
}
function colourChange5() {
r = document.getElementById("sred").value;
g = document.getElementById("sgreen").value;
b = document.getElementById("sblue").value;
ec(r,g,b);
document.getElementById("scolour").style.backgroundColor="RGB("+r+","+g+","+b+")";
}
function colourChange3() {
r = document.getElementById("namered").value;
g = document.getElementById("namegreen").value;
b = document.getElementById("nameblue").value;
ec(r,g,b);
document.getElementById("namecolour").style.backgroundColor="RGB("+r+","+g+","+b+")";
}
function colourChange2() {
r = document.getElementById("selred").value;
g = document.getElementById("selgreen").value;
b = document.getElementById("selblue").value;
ec(r,g,b);
document.getElementById("selcolour").style.backgroundColor="RGB("+r+","+g+","+b+")";
}
function hueChange(hue) {
document.getElementById("hue").value = hue;
}
function colourChange1(r,g,b){
hue=document.getElementById("hue").value;
r2=r-hue;
g2=g-hue;
b2=b-hue;
if (g2 > 255) {
g2 = 255;
}
if (r2 > 255) {
r2 = 255;
}
if (b2 > 255) {
b2 = 255;
}
if (g2 < 0) {
g2 = 0;
}
if (r2 < 0) {
r2 = 0;
}
if (b2 < 0) {
b2 = 0;
}
ec(r,g,b);
document.getElementById("selcolour").style.backgroundColor="RGB("+r2+","+g2+","+b2+")";
document.getElementById("selred").value = r2;
document.getElementById("selgreen").value = g2;
document.getElementById("selblue").value = b2;
}
function sendtoname() {
r = document.getElementById("selred").value;
g = document.getElementById("selgreen").value;
b =  document.getElementById("selblue").value;
document.getElementById("namered").value = r;
document.getElementById("namegreen").value = g;
document.getElementById("nameblue").value = b;
document.getElementById("namecolour").style.backgroundColor="RGB("+r+","+g+","+b+")";
}
function sendtoshadow() {
r = document.getElementById("selred").value;
g = document.getElementById("selgreen").value;
b =  document.getElementById("selblue").value;
document.getElementById("sred").value = r;
document.getElementById("sgreen").value = g;
document.getElementById("sblue").value = b;
document.getElementById("scolour").style.backgroundColor="RGB("+r+","+g+","+b+")";
}
</script>

<?
include "config.php";
include "designtop.php";
if ($_POST['submit']) {
mysql_query("UPDATE `whatpulse` SET `fontred` = '$_POST[namered]', `fontgreen` = '$_POST[namegreen]', `fontblue` = '$_POST[nameblue]',
 `sred` = '$_POST[sred]', `sgreen` = '$_POST[sgreen]', `sblue` = '$_POST[sblue]' WHERE `User` = '$_SESSION[username]'") or die(mysql_error());
}
?>
<table cellspacing='0' cellpadding='0'>
<tr>
<?
$red = 254;
$green = 0;
$blue = 0;
while ($green < 254) {
$green+=2;
echo "<td class=colour id=y$green onclick='colourChange1($red,$green,$blue)'></td>
";
}
echo "</tr><tr>";
while ($red > 0) {
$red-=2;
echo "<td class=colour id=g$red onclick='colourChange1($red,$green,$blue)'></td>
";
}
echo "</tr><tr>";
while ($blue < 254) {
$blue+=2;
echo "<td class=colour id=c$blue onclick='colourChange1($red,$green,$blue)'></td>
";
}
echo "</tr><tr>";
while ($green > 0) {
$green-=2;
echo "<td class=colour id=b$green onclick='colourChange1($red,$green,$blue)'></td>
";
}
echo "</tr><tr>";
while ($red < 254) {
$red+=2;
echo "<td class=colour id=m$red onclick='colourChange1($red,$green,$blue)'></td>
";
}
echo "</tr><tr>";
while ($blue > 0) {
$blue-=2;
echo "<td class=colour id=p$blue onclick='colourChange1($red,$green,$blue)'></td>
";
}

?>
</tr>
</table><br>
<?

$data = mysql_fetch_assoc(mysql_query("SELECT * FROM `whatpulse` WHERE `user` = '$_SESSION[username]'")) or die(mysql_error());
?>
<form action='' method='post'>
<table cellspacing='0'>
<tr>
<td class='title'>Selected Colour</td>
<td></td>
<td class='title'>Name Colour</td>
<td></td>
<td class='title'>Shadow Colour</td>
</tr>
<tr>
<td align='right'>
Red:<input id='selred' type='text' name='selred' size='3' maxlength='3' onBlur='colourChange2()'><br>
Green:<input id='selgreen' type='text' name='selgreen' size='3' maxlength='3' onBlur='colourChange2()'><br>
Blue:<input id='selblue' type='text' name='selblue' size='3' maxlength='3' onBlur='colourChange2()'><br>
Light/Dark:
<input type='text' maxlength='4' size='4' id='hue' value='0' onchange='update(document.getElementById("hue").value);'>
</td>
<td><div id='selcolour' class='box'>&nbsp;</div></td>
<td align='right' valign='top'>
Red:<? echo "<input id='namered' type='text' name='namered' size='3' maxlength='3' onBlur='colourChange3()' value='$data[fontred]'>"; ?><br>
Green:<? echo "<input id='namegreen' type='text' name='namegreen' size='3' maxlength='3' onBlur='colourChange3()' value='$data[fontgreen]'>"; ?><br>
Blue:<? echo "<input id='nameblue' type='text' name='nameblue' size='3' maxlength='3' onBlur='colourChange3()' value='$data[fontblue]'>"; ?><br>
<input type='button' onclick='sendtoname()' value='Send to Name'>
</td>
<td><div id='namecolour' class='box'>&nbsp;</div></td>
<td align='right' valign='top'>
Red:<? echo "<input id='sred' type='text' name='sred' size='3' maxlength='3' onBlur='colourChange5()' value='$data[sred]'>"; ?><br>
Green:<? echo "<input id='sgreen' type='text' name='sgreen' size='3' maxlength='3' onBlur='colourChange5()' value='$data[sgreen]'>"; ?><br>
Blue:<? echo "<input id='sblue' type='text' name='sblue' size='3' maxlength='3' onBlur='colourChange5()' value='$data[sblue]'>"; ?><br>
<input type='button' onclick='sendtoshadow()' value='Send to Shadow'>
</td>
<td><div id='scolour' class='box'>&nbsp;</div></td>
</tr>
</table>
<input type='submit' value='submit' name='submit'>
</form>
<script>
function update(hue) {
r=254;
g=0;
b=0;
while (g < 254) {
g+=2;
g2=g-hue;
r2=r-hue;
b2=b-hue;
if (g2 > 255) {
g2 = 255;
}
if (r2 > 255) {
r2 = 255;
}
if (b2 > 255) {
b2 = 255;
}
document.getElementById("y"+g).style.backgroundColor="RGB("+r2+","+g2+","+b2+")";
}
while (r > 0) {
r-=2;
g2=g-hue;
r2=r-hue;
b2=b-hue;
if (g2 > 255) {
g2 = 255;
}
if (r2 > 255) {
r2 = 255;
}
if (b2 > 255) {
b2 = 255;
}
document.getElementById("g"+r).style.backgroundColor="RGB("+r2+","+g2+","+b2+")";
}
while (b < 254) {
b+=2;
g2=g-hue;
r2=r-hue;
b2=b-hue;
if (g2 > 255) {
g2 = 255;
}
if (r2 > 255) {
r2 = 255;
}
if (b2 > 255) {
b2 = 255;
}
document.getElementById("c"+b).style.backgroundColor="RGB("+r2+","+g2+","+b2+")";
}
while (g > 0) {
g-=2;
g2=g-hue;
r2=r-hue;
b2=b-hue;
document.getElementById("b"+g).style.backgroundColor="RGB("+r2+","+g2+","+b2+")";
}
while (r < 254) {
r+=2;
g2=g-hue;
r2=r-hue;
b2=b-hue;
if (g2 > 255) {
g2 = 255;
}
if (r2 > 255) {
r2 = 255;
}
if (b2 > 255) {
b2 = 255;
}
document.getElementById("m"+r).style.backgroundColor="RGB("+r2+","+g2+","+b2+")";
}
while (b > 0) {
b-=2;
g2=g-hue;
r2=r-hue;
b2=b-hue;
if (g2 > 255) {
g2 = 255;
}
if (r2 > 255) {
r2 = 255;
}
if (b2 > 255) {
b2 = 255;
}
document.getElementById("p"+b).style.backgroundColor="RGB("+r2+","+g2+","+b2+")";
}
}
update(document.getElementById("hue").value);
colourChange2();
colourChange3();
colourChange5();
</script>
<?
@include "menu.php";
@include "designbottom.php";
?>
</body>
</html>
