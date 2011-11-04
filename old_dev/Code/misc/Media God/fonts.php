<?
session_start();
if (!$_SESSION['username']) {
header("Location: login.php");
}
include "config.php";
include "designtop.php";

if ($_POST['submit']) {
$action = "Submitting new font";
if ($_FILES["file"]["type"] == "application/octet-stream") {
	$name = $_FILES["file"]["name"];

	$ext = strstr($name,".");
if ($ext === ".ttf" || ".TTF") {
	$base = basename($name,$ext);
	}
	$path = "fonts/" . $name;
copy($_FILES["file"]["tmp_name"],$path);
	$im = imagecreatetruecolor(250,100);
	imageantialias($im,"off");
	$wht = imagecolorallocate($im,255,255,255);
	imagefill($im,0,0,$wht);
	imagettftext($im,18,0,5,24,$blk,$path,$base);
	imagettftext($im,8,0,5,42,$blk,$path,"size 8: abcdABCD");
	imagettftext($im,10,0,5,58,$blk,$path,"size 10: abcdABCD");
		imagettftext($im,12,0,5,75,$blk,$path,"size 12: abcdABCD");
		imagettftext($im,14,0,5,94,$blk,$path,"size 14: abcdABCD");
	$path2 = "fonts/images/" . $base . ".png";
$grey = imagecolorallocate($im,204,204,204);
imagerectangle($im,0,0,249,99,$grey);
	imagepng($im,$path2);
	imagedestroy($im);
	mysql_query("INSERT INTO `fonts` (`name`,`file`) VALUES ('$base','$name')") or die(mysql_error());
	echo "<img src='$path2'>";
}
else {
	echo "The font that you have uploaded does not have a .ttf or .TTF extension (yes, it is case sensitive). Please only upload ttf fonts.";
	
}
}
?>
<br>One field, upload a font then use it in your signature. It's as easy as that!
<form action='' method='post' enctype="multipart/form-data">
Font Location: <input type='file' name='file'><br>
<input type='submit' name='submit' value='submit'>
</form>
<?
include "menu.php";
include "designbottom.php";
?>