<?
session_start();
$page = "custom.php";
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  //Will never cache the image
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //Set date to creation (viewing) of file
header("Cache-Control: no-store, no-cache, must-revalidate"); //Do Not Cache, Store, or otherwise view older copies of the image
header("Cache-Control: post-check=0, pre-check=0", false); //Ditto
header("Pragma: no-cache"); //Duh
echo "<meta http-equiv='cache-control' content='no-cache'/>";
//Above borrowed from us3rX from KillaNet, friend of mine.
//This person was exploiting a known bug in the custom page.
ob_start();
include "config.php";
if ($_POST['submit']) {
	$action = "Updating their image.";
	$userx = $_POST['ux'];
	$usery = $_POST['uy'];
	$usere = $_POST['ue'];
	$rankx = $_POST['rx'];
	$ranky = $_POST['ry'];
	$ranke = $_POST['re'];
	$keysx = $_POST['kx'];
	$keysy = $_POST['ky'];
	$keyse = $_POST['ke'];
	$clicksx = $_POST['cx'];
	$clicksy = $_POST['cy'];
	$clickse = $_POST['ce'];
	$countryx = $_POST['countryx'];
	$countryy = $_POST['countryy'];
	$countrye = $_POST['countrye'];
	$tnamex = $_POST['tnamex'];
	$tnamey = $_POST['tnamey'];
	$tnamee = $_POST['tnamee'];
	$tkeysx = $_POST['tkeysx'];
	$tkeysy = $_POST['tkeysy'];
	$tkeyse = $_POST['tkeyse'];
	$tclicksx = $_POST['tclicksx'];
	$tclicksy = $_POST['tclicksy'];
	$tclickse = $_POST['tclickse'];
	$trankx = $_POST['trankx'];
	$tranky = $_POST['tranky'];
	$tranke = $_POST['tranke'];
	$email = $_POST['email'];
	$transred = $_POST['transred'];
	$transgreen = $_POST['transgreen'];
	$transblue = $_POST['transblue'];
	$be = $_POST['be'];
	if(!$_POST['bg']) {
		$bg = "16";
	}
	else {
		$bg = $_POST['bg'];

	}
	$se = $_POST['se'];
	$width = $_POST['width'];
	if ($width > 500 || $width < 0 ) {
		echo "Width cannot be greater than 500 or less than 0.<br>";
		$x++;
	}
	$height = $_POST['height'];
	if ($height > 100 || $width < 0) {
		echo "Height cannot be greater than 100 or less than 0.<br>";
		$x++;
	}
	if ($width < 1) {
		$width = 1;
	}
	if ($height < 1) {
		$height = 1;
	}
	$font = $_POST['font'];
	if (!$font) {
		$font = "arial.ttf";
	}
	$fontsize = $_POST['fontsize'];
	if ($fontsize > 24) {
		echo "The font size cannot be greater than 24.<br>";
		$x++;
	}
	if (!$x) {
		$a = mysql_fetch_assoc(mysql_query("SELECT * FROM `backgrounds` WHERE `id` = '$bg'"));
		$path = $a['path'];
		mysql_query("UPDATE `whatpulse` SET `width` = '$width', `height` = '$height', `userx` = '$userx' , `usery` = '$usery' ,
		 `usere` = '$usere', `rankx` = '$rankx', `ranky` = '$ranky', `ranke` = '$ranke', `tkcx` = '$keysx' ,
		 `tkcy` = '$keysy',  `tkce` = '$keyse' , `tmcx` = '$clicksx' , `tmcy` = '$clicksy',  `tmce` = '$clickse',
`tnamex` = '$tnamex', `tnamey` = '$tnamey', `tnamee` = '$tnamee', `font` = '$font', `fontsize` = '$fontsize',
`countryx` = '$countryx', `countryy` = '$countryy', `countrye` = '$countrye',
`tkeysx` = '$tkeysx', `tkeysy` = '$tkeysy', `tkeyse` = '$tkeyse', `tclicksx` = '$tclicksx',
`tclicksy` = '$tclicksy', `tclickse` = '$tclickse', `trankx` = '$trankx', `tranky` = '$tranky',
`tranke` = '$tranke', `path` = '$path', `theme` = '$bg', `se` = '$se', `be` = '$be', `email` = '$email', `transred` = '$transred', `transgreen` = '$transgreen', `transblue` = '$transblue'
  WHERE `user` = '$_SESSION[username]'") or die(mysql_error());
	}
	else {
		echo "Your image has not been updated due to the above problems. Please fix this and try again.<br>";
	}
}
include "config.php";
if (!$_SESSION['username']) {
	echo "<meta http-equiv='Refresh' content='0;url=login.php' />";

}
$query = "select * from whatpulse where `user` = '".$_SESSION['username']."'";


$data = mysql_fetch_assoc(mysql_query($query)) or die(mysql_error());
$im = imagecreatetruecolor($data['width'],$data['height']);
//$trans1 = imagecolorallocate($im,255,2,255);
imagefill($im,0,0,$trans1);
//imagecolortransparent($im,$trans1);
//$trans = imagecolorallocate($im,$data['transred'],$data['transgreen'],$data['transblue']);

$im2 = @imagecreatefrompng($data['path']);
//imagecolortransparent($im2,$trans);
@imagecopy($im, $im2,  0, 0, 0, 0, $data['width'], $data['height']);
$fontcolor = imagecolorallocate($im, $data['fontred'], $data['fontgreen'], $data['fontblue']);
$shadow = imagecolorallocate($im, $data['sred'], $data['sgreen'], $data['sblue']);
$blk = imagecolorallocate($im, 0, 0, 0);
if ($data[be]) {
	imagerectangle($im,0,0,$data['width']-1,$data['height']-1,$blk);
}

if ($data['se']) {
	if ($data['usere']) {
		imagettftext($im,$data['fontsize'],0,$data['userx']+1,$data['usery']+1,$shadow,'fonts/' . $data['font'],"User: " . $data['user']);
		imagettftext($im,$data['fontsize'],0,$data['userx'],$data['usery'],$fontcolor,'fonts/' . $data['font'],"User: " . $data['user']);
	}
	if ($data['tkce']) {
		imagettftext($im,$data['fontsize'],0,$data['tkcx']+1,$data['tkcy']+1,$shadow,'fonts/' . $data['font'],"Keys: " . number_format($data['tkc']));
		imagettftext($im,$data['fontsize'],0,$data['tkcx'],$data['tkcy'],$fontcolor,'fonts/' . $data['font'],"Keys: " . number_format($data['tkc']));
	}
	if ($data['tmce']) {
		imagettftext($im,$data['fontsize'],0,$data['tmcx']+1,$data['tmcy']+1,$shadow,'fonts/' . $data['font'],"Clicks: " . number_format($data['tmc']));
		imagettftext($im,$data['fontsize'],0,$data['tmcx'],$data['tmcy'],$fontcolor,'fonts/' . $data['font'],"Clicks: " . number_format($data['tmc']));
	}
	if ($data['ranke']) {
		imagettftext($im,$data['fontsize'],0,$data['rankx']+1,$data['ranky']+1,$shadow,'fonts/' . $data['font'],"Rank: " . number_format($data['rank']));
		imagettftext($im,$data['fontsize'],0,$data['rankx'],$data['ranky'],$fontcolor,'fonts/' . $data['font'],"Rank: " . number_format($data['rank']));
	}
	if ($data['tnamee']) {
		imagettftext($im,$data['fontsize'],0,$data['tnamex']+1,$data['tnamey']+1,$shadow,'fonts/' . $data['font'],"Team: " . $data['tname']);
		imagettftext($im,$data['fontsize'],0,$data['tnamex'],$data['tnamey'],$fontcolor,'fonts/' . $data['font'],"Team: " . $data['tname']);
	}
	if ($data['countrye']) {
		imagettftext($im,$data['fontsize'],0,$data['countryx']+1,$data['countryy']+1,$shadow,'fonts/' . $data['font'],"Country: " . $data['country']);
		imagettftext($im,$data['fontsize'],0,$data['countryx'],$data['countryy'],$fontcolor,'fonts/' . $data['font'],"Country: " . $data['country']);
	}
	if ($data['tkeyse']) {
		imagettftext($im,$data['fontsize'],0,$data['tkeysx']+1,$data['tkeysy']+1,$shadow,'fonts/' . $data['font'],"Team Keys: " . number_format($data['tkeys']));
		imagettftext($im,$data['fontsize'],0,$data['tkeysx'],$data['tkeysy'],$fontcolor,'fonts/' . $data['font'],"Team Keys: " . number_format($data['tkeys']));
	}
	if ($data['tclickse']) {
		imagettftext($im,$data['fontsize'],0,$data['tclicksx']+1,$data['tclicksy']+1,$shadow,'fonts/' . $data['font'],"Team Clicks: " . number_format($data['tclicks']));
		imagettftext($im,$data['fontsize'],0,$data['tclicksx'],$data['tclicksy'],$fontcolor,'fonts/' . $data['font'],"Team Clicks: " . number_format($data['tclicks']));
	}
	if ($data['tranke']) {
		imagettftext($im,$data['fontsize'],0,$data['trankx']+1,$data['tranky']+1,$shadow,'fonts/' . $data['font'],"Team Rank: " . $data['trank'] . "/" . $data['tmembers']);
		imagettftext($im,$data['fontsize'],0,$data['trankx'],$data['tranky'],$fontcolor,'fonts/' . $data['font'],"Team Rank: " . $data['trank'] . "/" . $data['tmembers']);
	}
}

else {
	if ($data['usere']) {
		imagettftext($im,$data['fontsize'],0,$data['userx'],$data['usery'],$fontcolor,'fonts/' . $data['font'],"User: " . $data['user']);
	}
	if ($data['tkce']) {
		imagettftext($im,$data['fontsize'],0,$data['tkcx'],$data['tkcy'],$fontcolor,'fonts/' . $data['font'],"Keys: " .number_format($data['tkc']));
	}
	if ($data['tmce']) {
		imagettftext($im,$data['fontsize'],0,$data['tmcx'],$data['tmcy'],$fontcolor,'fonts/' . $data['font'],"Clicks: " .number_format($data['tmc']));
	}
	if ($data['ranke']) {
		imagettftext($im,$data['fontsize'],0,$data['rankx'],$data['ranky'],$fontcolor,'fonts/' . $data['font'],"Rank: " . number_format($data['rank']));
	}
	if ($data['tnamee']) {
		imagettftext($im,$data['fontsize'],0,$data['tnamex'],$data['tnamey'],$fontcolor,'fonts/' . $data['font'],"Team: " . $data['tname']);
	}
	if ($data['countrye']) {
		imagettftext($im,$data['fontsize'],0,$data['countryx'],$data['countryy'],$fontcolor,'fonts/' . $data['font'],"Country: " . $data['country']);
	}
	if ($data['tkeyse']) {
		imagettftext($im,$data['fontsize'],0,$data['tkeysx'],$data['tkeysy'],$fontcolor,'fonts/' . $data['font'],"Team Keys: " .number_format($data['tkeys']));
	}
	if ($data['tclickse']) {

		imagettftext($im,$data['fontsize'],0,$data['tclicksx'],$data['tclicksy'],$fontcolor,'fonts/' . $data['font'],"Team Clicks: " .number_format($data['tclicks']));
	}
	if ($data['tranke']) {
		imagettftext($im,$data['fontsize'],0,$data['trankx'],$data['tranky'],$fontcolor,'fonts/' . $data['font'],"Team Rank: " . $data['trank'] . "/" . $data['tmembers']);
	}
}
$file = "sig/".$data['user'].".png";

ob_start();
imagepng($im);
$idata = ob_get_contents();
ob_end_clean();
insertimage(mysql_escape_string($idata),$data[user]);
imagedestroy($im);

function insertimage($idata,$user) {
	//check if image data exists
	$query = "select * from images where username = '".$user."'";
	$result = mysql_query($query);

	if(!$result) { die("query failed"); }
	switch (mysql_num_rows($result)) {

		case 0:
		$query = "INSERT INTO `images` (`username` , `imagedata` ) VALUES ('".$user."','".$idata."');";
		$result = mysql_query($query);
		if(!$result) { die("query failed!"); }
		break;
		case 1:
		$query = "UPDATE `images` SET imagedata='".$idata."' WHERE `username` = '".$user."'";
		$result = mysql_query($query);
		if (!$result) { die("query failed: ".mysql_error()); }
		break;
		default:
		break;
	}
}
include "designtop.php";

?><br>
<div>
<br>
<?
$user = $data['user'];
echo "<img src='sig/" . $user . ".png' class='img'>";
echo "<div class='stats'><b>Database Statistics</b><br>
Keys - " . number_format($data[tkc]) . "<br>
Clicks - " . number_format($data[tmc]) . "<br>
Rank - " . number_format($data[rank]) . "<br>
Team Name - $data[tname]<br>
Team Keys - " . number_format($data[tkeys]) . "<br>
Team Clicks - " . number_format($data[tclicks]) . "<br>
Team Rank - " . number_format($data[trank]) . " /" . number_format($data[tmembers]) . "<br>

</div>";
?><br>
<div class='codebox'><b>Forum Code:</b>
[img]http://offbeat-zero.net/pulse/sig/<? echo $user; ?>.png[/img]</div>
<?
if (!$user['email']) {
echo "Please enter an email address into the email field. If you do not enter one, it will make it harder to recover your password.<br>";	
}
?>
<div class='codebox'><b>Want to try out the new custom page?</b><br>
 <a href='custom2.php'>Try it out here</a></div>
<form action='' method='post'>
<table cellspacing='0' cellpadding='0' style='margin-top:10px;'>
<tr><td class='chead'>Image Specifications</td><td width='5px'></td>
<td class='chead'>User</td>
<td width='5px'></td>
<td class='chead'>Keys</td>
<td width='5px'></td>
<td class='chead'>Clicks</td>
<td width='5px'></td>
<td class='chead'>Rank</td>
<td width='5px'></td>
<!--<td class='chead'>Transparency</td>-->
</tr>
<tr>
<td class='cstuff' align='right'>
<?
 echo "Width: <input type='text' name='width' value='$data[width]' maxlength='4' size='4'>"; ?><br>
<? echo "Height: <input type='text' name='height' value='$data[height]' maxlength='3' size='3'>";
 ?><br>
Border Enabled: <select name='be'>
 <? if ($data[be]) {
 	echo "<option>1</option>";
 	echo "--";
 	echo "<option>0</option>";
 }
 else {
 	echo "<option>0</option>";
 	echo "--";
 	echo "<option>1</option>";
 }

 ?>
 </select>
 <br>
Font: <select name='font'>
<?
$q = mysql_query("SELECT * FROM `fonts` ORDER BY `name` ASC");
while ($a = mysql_fetch_array($q)) {
	if ($data['font'] == $a[file]) {
		echo "<option value='$a[file]' selected='selected'>$a[name]</option>";
	}
	else {
		echo "<option value='$a[file]'>$a[name]</option>";
	}
}
?>
</select><br>
Font Size: <? echo "<input type='text' value='$data[fontsize]' name='fontsize' size='2'>" ?><br>
Shadows: <select name='se'>
<?
echo "<option>$data[se]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
<?
echo "Background:<select name='bg'>";

$z = mysql_query("SELECT * FROM `backgrounds` WHERE `userid` = '$data[id]' ORDER BY `name` ASC") or die(mysql_error());
$y = mysql_query("SELECT * FROM `backgrounds` WHERE `userid` = '0' ORDER BY `name` ASC") or die(mysql_error());
while ($a = mysql_fetch_array($z)) {
	if ($a[id] == $data[theme]) {
		echo "<option selected='selected' value='$a[id]'>$a[name]</option>";
	}
	else {
		echo "<option value='$a[id]'>$a[name]</option>";
	}
}
echo "<option value='0'>------</option>";
while ($a = mysql_fetch_array($y)) {
	if ($a[id] == $data[theme]) {
		echo "<option selected='selected' value='$a[id]'>$a[name]</option>";
	}
	else {
		echo "<option value='$a[id]'>$a[name]</option>";
	}
}
echo "</select><br>";
?>

Email: <? echo "<input type='text' value='$data[email]' name='email'>" ?>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>X Co-ordinate: <? echo "<input type='text' value='$data[userx]' name='ux' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[usery]' name='uy' size='3'>" ?><br>
Enabler:
<select name='ue'>
<?
echo "<option>$data[usere]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>X Co-ordinate: <? echo "<input type='text' value='$data[tkcx]' name='kx' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[tkcy]' name='ky' size='3'>" ?><br>
Enabler:
<select name='ke'>
<?
echo "<option>$data[tkce]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>
X Co-ordinate: <? echo "<input type='text' value='$data[tmcx]' name='cx' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[tmcy]' name='cy' size='3'>" ?><br>
Enabler:
<select name='ce'>
<?
echo "<option>$data[tmce]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>
X Co-ordinate: <? echo "<input type='text' value='$data[rankx]' name='rx' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[ranky]' name='ry' size='3'>" ?><br>
Enabler:
<select name='re'>
<?
echo "<option>$data[ranke]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td><td></td>
<!--td class='cstuff' valign='top' align='right'>
Red: <? echo "<input type='text' value='$data[transred]' name='transred' size='3'>"; ?><br>
Green: <? echo "<input type='text' value='$data[transgreen]' name='transgreen' size='3'>"; ?><br>
Blue: <? echo "<input type='text' value='$data[transblue]' name='transblue' size='3'>"; ?><br>-->

</td>
</tr>
<tr><td height='5px'></td></tr>
<tr><td class='chead'>Team Name</td><td width='5px'></td>
<td class='chead'>Team Keys</td>
<td width='5px'></td>
<td class='chead'>Team Clicks</td>
<td width='5px'></td>
<td class='chead'>Team Rank</td>
<td width='5px'></td>
<td class='chead'>Country</td>
</tr>
<tr>
<td class='cstuff' valign='top' align='right'>
X Co-ordinate: <? echo "<input type='text' value='$data[tnamex]' name='tnamex' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[tnamey]' name='tnamey' size='3'>" ?><br>
Enabler:
<select name='tnamee'>
<?
echo "<option>$data[tnamee]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>
X Co-ordinate: <? echo "<input type='text' value='$data[tkeysx]' name='tkeysx' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[tkeysy]' name='tkeysy' size='3'>" ?><br>
Enabler:
<select name='tkeyse'>
<?
echo "<option>$data[tkeyse]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>
X Co-ordinate: <? echo "<input type='text' value='$data[tclicksx]' name='tclicksx' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[tclicksy]' name='tclicksy' size='3'>" ?><br>
Enabler:
<select name='tclickse'>
<?
echo "<option>$data[tclickse]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>
X Co-ordinate: <? echo "<input type='text' value='$data[trankx]' name='trankx' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[tranky]' name='tranky' size='3'>" ?><br>
Enabler:
<select name='tranke'>
<?
echo "<option>$data[tranke]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
<td></td>
<td class='cstuff' valign='top' align='right'>
X Co-ordinate: <? echo "<input type='text' value='$data[countryx]' name='countryx' size='3'>" ?><br>
Y Co-ordinate: <? echo "<input type='text' value='$data[countryy]' name='countryy' size='3'>" ?><br>
Enabler:
<select name='countrye'>
<?
echo "<option>$data[countrye]</option>";
?>
<option>--</option>
<option>0</option>
<option>1</option>
</select><br>
</td>
</tr>
</table>
<input type='submit' name='submit' value='submit'>
</form>
</div>
</body>
<?
ob_end_flush();
include "menu.php";
include "designbottom.php";
?>