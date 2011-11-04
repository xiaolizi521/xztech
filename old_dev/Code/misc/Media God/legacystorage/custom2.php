<?
session_start();
if (!$_SESSION['username']) {
header("Location: login.php");	
}
?>
<html>
<head>
<style type='text/css'>
body {
margin:0px;
spacing:0px;
padding:0px;
font:12pt "Trebuchet MS";
}
.bg {
display:none;
position:absolute;
top:150px;
left:15px;
}
.position {
position:absolute;
border:1px solid red;
}
.red {
position:absolute;
background:red;
width:50px;
height:25px;
}
.image {
position:absolute;
top:210px;
left:15px;
}
.template {

position:absolute;
top:135px;
left:15px;
}
.yourimage {
position:absolute;
top:197px;
left:15px;
}
.coords {
position:absolute;
top:250px;
}
.explanation {
position:absolute;
top:150px;
right:20px;
width:25%;
}
</style>
</head>
<?
include "config.php";
include "designtop.php";

if ($_POST['submit']) {
	mysql_query("UPDATE `whatpulse` SET `userx` = '$_POST[x1]', `usery` = '$_POST[y1]',`tkcx` = '$_POST[x2]', `tkcy` = '$_POST[y2]',`tmcx` = '$_POST[x3]', `tmcy` = '$_POST[y3]',`rankx` = '$_POST[x4]', `ranky` = '$_POST[y4]',`tnamex` = '$_POST[x5]', `tnamey` = '$_POST[y5]',`tkeysx` = '$_POST[x6]', `tkeysy` = '$_POST[y6]',`tclicksx` = '$_POST[x7]', `tclicksy` = '$_POST[y7]',`trankx` = '$_POST[x8]', `tranky` = '$_POST[y8]',`countryx` = '$_POST[x9]', `countryy` = '$_POST[y9]' WHERE `user` = '$_SESSION[name]'") or die(mysql_error());
}
$user = mysql_fetch_assoc(mysql_query("SELECT * FROM `whatpulse` WHERE `user` = '$_SESSION[name]'"));
$username = $user['user'];
if (!file_exists("users/" . $username)) {
mkdir("users/" . $username);
chmod("users/" . $username,0777);
}
//Username
$im = imagecreatetruecolor(200,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","User: " . $user['user']);
imagepng($im,"users/" . $user['user'] . "/user.png");
imagedestroy($im);
//Keys
$im = imagecreatetruecolor(200,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Keys: " . number_format($user['tkc']));
imagepng($im,"users/" . $user['user'] . "/keys.png");
imagedestroy($im);
//Clicks
$im = imagecreatetruecolor(200,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Clicks: " . number_format($user['tmc']));
imagepng($im,"users/" . $user['user'] . "/clicks.png");
imagedestroy($im);
//Rank
$im = imagecreatetruecolor(100,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Rank: " . number_format($user['rank']));
imagepng($im,"users/" . $user['user'] . "/rank.png");
imagedestroy($im);
//Team Name
$im = imagecreatetruecolor(350,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Team Name: " . $user['tname']);
imagepng($im,"users/" . $user['user'] . "/teamname.png");
imagedestroy($im);
//Team Keys
$im = imagecreatetruecolor(200,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Team Keys: " . number_format($user['tkeys']));
imagepng($im,"users/" . $user['user'] . "/teamkeys.png");
imagedestroy($im);
//Team Clicks
$im = imagecreatetruecolor(200,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Team Clicks: " . number_format($user['tclicks']));
imagepng($im,"users/" . $user['user'] . "/teamclicks.png");
imagedestroy($im);
//Team Rank
$im = imagecreatetruecolor(200,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Team Rank: " . $user['trank'] . " / "  . $user['tmembers']);
imagepng($im,"users/" . $user['user'] . "/teamrank.png");
imagedestroy($im);
//Country
$im = imagecreatetruecolor(200,20);
imageantialias($im,1);
$blk = imagecolorallocate($im,0,0,0);
$bg = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$bg);
imagecolortransparent($im,$bg);
$col = imagecolorallocate($im,$user['fontred'],$user['fontgreen'],$user['fontblue']);
imagettftext($im,$user['fontsize'],0,$title[2]+8,13,$col,"fonts/gothic.ttf","Country: " . $user['country']);
imagepng($im,"users/" . $user['user'] . "/country.png");
imagedestroy($im);


//The image itself!
$im = imagecreatetruecolor($user['width'],$user['height']);
$im2 = imagecreatefrompng("img/wp.png");
imagecopy($im,$im2,0,0,0,0,$user['width'],$user['height']);
imagettftext($im,$user['fontsize'],0,$user['userx'],$user['usery'],$col,"fonts/gothic.ttf","Name: " . $user['user']);
imagettftext($im,$user['fontsize'],0,$user['tkcx'],$user['tkcy'],$col,"fonts/gothic.ttf","Keys: " . number_format($user['tkc']));
imagettftext($im,$user['fontsize'],0,$user['tmcx'],$user['tmcy'],$col,"fonts/gothic.ttf","Clicks333: " . number_format($user['tmc']));
imagettftext($im,$user['fontsize'],0,$user['rankx'],$user['ranky'],$col,"fonts/gothic.ttf","Rank: " . number_format($user['rank']));
imagettftext($im,$user['fontsize'],0,$user['tnamex'],$user['tnamey'],$col,"fonts/gothic.ttf","Team Name: " . $user['tname']);
imagettftext($im,$user['fontsize'],0,$user['tkeysx'],$user['tkeysy'],$col,"fonts/gothic.ttf","Team Keys: " . number_format($user['tkeys']));
imagettftext($im,$user['fontsize'],0,$user['tclicksx'],$user['tclicksy'],$col,"fonts/gothic.ttf","Team Clicks: " . number_format($user['tclicks']));
imagettftext($im,$user['fontsize'],0,$user['trankx'],$user['tranky'],$col,"fonts/gothic.ttf","Team Rank: " . number_format($user['trank']));
imagerectangle($im,0,0,$user['width']-1,$user['height']-1,$blk);


ob_start();
	$idata = imagepng($im);
    ob_end_clean();
	insertimage(mysql_escape_string($idata),$user[user]);

	imagedestroy($im);
	
	function insertimage($idata,$user) {
  //check if image data exists
  $query = "select * from images where username = '".$username."'";
  $result = mysql_query($query);
  
  if(!$result) { die("query failed"); }
  switch (mysql_num_rows($result)) {

		case 0: 
			$query = "INSERT INTO `images` (`username` , `imagedata` ) VALUES ('".$username."','".$idata."');";
			$result = mysql_query($query);
			if(!$result) { die("query failed!"); }
			break;
		case 1:
			$query = "UPDATE `images` SET imagedata='".$idata."' WHERE `username` = '".$username."'";
			$result = mysql_query($query);
			if (!$result) { die("query failed: ".mysql_error()); }
			break;
		default:
			break;
	}
}	
?><br><br><br><br>
<div class='explanation'>To move an element, click on it on the top signature and move to the desired position then click again to set it into that position.<br><br>Please report every bug you find on the <a href='http://offbeat-zero.net/pulse/forums'>forums</a>. If you have any suggestions on the improvement, also tell us them.</div>
<div class='template'><b>Template:</b></div>
<img src='img/wp.png' class='bg' border='1'>
<div class='yourimage'><b>Your Image:</b></div>
<?
echo "<img src='sig/$username.png' class='image'>";
?>

<form name="Show" action='' method='post'>
<?
echo "<table cellspacing='5' cellpadding='5' class='coords'>
<tr><td align='center'><b>User</b><br>
X: <input type='text' id='x1' name='x1' value='$user[userx]' size='4'><br>
Y: <input type='text' id='y1' name='y1' value='$user[usery]' size='4'><br>
</td>
<td align='center'><b>Keys</b><br>
X: <input type='text' id='x2' name='x2' value='$user[tkcx]' size='4'><br>
Y: <input type='text' id='y2' name='y2' value='$user[tkcy]' size='4'><br>
</td>
<td align='center'><b>Clicks</b><br>
<input type='text' id='x3' name='x3' value='$user[tmcx]' size='4'> X<br>
<input type='text' id='y3' name='y3' value='$user[tmcy]' size='4'> Y<br>
</td>
<td align='center'><b>Ranks</b><br>
<input type='text' id='x4' name='x4' value='$user[rankx]' size='4'> X<br>
<input type='text' id='y4' name='y4' value='$user[ranky]' size='4'> Y<br>
</td>
<td align='center'><b>Team Name</b><br>

<input type='text' id='x5' name='x5' value='$user[tnamex]' size='4'> X<br>
<input type='text' id='y5' name='y5' value='$user[tnamey]' size='4'> Y<br>
</td>
<td align='center'><b>Team Keys</b><br>
<input type='text' id='x6' name='x6' value='$user[tkeysx]' size='4'> X<br>
<input type='text' id='y6' name='y6' value='$user[tkeysy]' size='4'> Y<br>
</td>
<td align='center'><b>Team Clicks</b><br>
<input type='text' id='x7' name='x7' value='$user[tclicksx]' size='4'> X<br>
<input type='text' id='y7' name='y7' value='$user[tclicksy]' size='4'> Y<br>
</td>
<td align='center'><b>Team Rank</b><br>
<input type='text' id='x8' name='x8' value='$user[trankx]' size='4'> X<br>
<input type='text' id='y8' name='y8' value='$user[tranky]' size='4'> Y<br>
</td>
<td align='center'><b>Country</b><br>
<input type='text' id='x9' name='x9' value='$user[countryx]' size='4'> X<br>
<input type='text' id='y9' name='y9' value='$user[countryy]' size='4'> Y<br>
</td>
</tr>
<tr><td align='center' colspan='9'><input type='submit' name='submit' value='submit'></td></tr>
</table>







";
?>

</form>


<script language="JavaScript1.2">
var IE = document.all?true:false
if (!IE) document.captureEvents(Event.MOUSEMOVE)

document.onmousemove = getMouseXY;
var tempX = 0
var tempY = 0
var down2 = 0;
function down() {
	if (down2 == 0) {
	down2 = 1;
	}
	else {
		down2 = 0;
	}
}

function getMouseXY(e) {
	if (down2 == 1)  {
  if (IE) {
    tempX = event.clientX + document.body.scrollLeft
    tempY = event.clientY + document.body.scrollTop
  } else {
    tempX = e.pageX
    tempY = e.pageY
  }  
  if (tempX < 0){tempX = 0}
  if (tempY < 0){tempY = 0}  
  return tempX;
  return tempY;
}
}

function moveObject(id) {
		getMouseXY;
		document.getElementById('x' + id).value = tempX - 8;
		document.getElementById('y' + id).value = tempY - 143;
		document.getElementById(id).style.top = tempY - 5;
		document.getElementById(id).style.left = tempX - 5;
		if (down2 == 1) {
		setTimeout('moveObject(' + id + ')',1);
		}
}

//-->
</script>
<?
$username = $user['user'];
$y =  $user[usery] + 136;
$x = $user[userx] + 5;
echo "<img src='users/$username/user.png' id='1' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(1)'>";
$y =  $user[tkcy] + 136;
$x = $user[tkcx] + 5;
echo "<img src='users/$username/keys.png' id='2' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(2)'>";
$y =  $user[tmcy] + 136;
$x = $user[tmcx] + 5;
echo "<img src='users/$username/clicks.png' id='3' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(3)'>";
$y =  $user[ranky] + 136;
$x = $user[rankx] + 5;
echo "<img src='users/$username/rank.png' id='4' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(4)'>";
$y =  $user[tnamey] + 136;
$x = $user[tnamex] + 5;
echo "<img src='users/$username/teamname.png' id='5' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(5)'>";
$y =  $user[tkeysy] + 136;
$x = $user[tkeysx] + 5;
echo "<img src='users/$username/teamkeys.png' id='6' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(6)'>";
$y =  $user[tclicksy] + 136;
$x = $user[tclicksx] + 5;
echo "<img src='users/$username/teamclicks.png' id='7' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(7)'>";
$y =  $user[tranky] + 136;
$x = $user[trankx] + 5;
echo "<img src='users/$username/teamrank.png' id='8' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(8)'>";
$y =  $user[countryy] + 136;
$x = $user[countryx] + 5;
echo "<img src='users/$username/country.png' id='9' class='position' style='top:" . $y . ";left:" . $x . "' onmousedown='down();moveObject(9)'>";



include "menu.php";
include "designbottom.php"
?>


