<?
session_start();
$page = "backgrounds.php";
if (!$_SESSION['username']) {
header("Location: login.php");
}  
include "config.php";
include "designtop.php";
  $action = "Viewing background input boxes.";
  echo "<b>The service only currently supports three image types, png, jpeg/jpg and gif. You will only be allowed to upload these types of files.</b><br><br>";
$type = $_FILES["file"]["type"];
$q = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `whatpulse` WHERE `user` = '$_SESSION[username]'"));
$r = mysql_query("SELECT * FROM `backgrounds` WHERE `userid` = '$q[id]'") or die(mysql_error());
while ($limit = mysql_fetch_array($r)) {
$total+=$limit[size];
}
if ($_POST['submit']) {
  $action = "Uploading a new image.";
if ($_POST['name']) {
if ($type == "image/png" || $type == "image/jpeg" || $type == "image/gif" || $type == "image/pjpeg" ) {
  echo "Uploading " . $_FILES["file"]["name"];
  echo " (" . $_FILES["file"]["type"] . ", ";
  echo ceil($_FILES["file"]["size"] / 1024) . " Kb).<br />";
@mkdir("users/" . $_SESSION[name]);
@chmod("users/" . $_SESSION[name],0777);
@mkdir("users/" . $_SESSION[name] . "/images");
@chmod("users/" . $_SESSION[name] . "/images",0777);
$size = $_FILES["file"]["size"];
list($width, $height, $type2, $attr) = getimagesize($_FILES["file"]["tmp_name"]);

//Checks to see if it is actually an image and is not just a text file or whatever saved as an image.
if (!$width) {
echo "You have uploaded an invalid image.";
}
//Checks the image to see if it fits within the max range... 500 x 100
elseif ($width > 500 || $height > 100) {
echo "Your image is too large. Please ensure that that height is less than 100 pixels and the width less than 500";
}
else {


$im = imagecreatetruecolor($width,$height);
switch ($type) {
case "image/png":
$im2 = imagecreatefrompng($_FILES["file"]["tmp_name"]);
$filename = basename($_FILES["file"]["name"],"png");
break;
case "image/gif":
$im2 = imagecreatefromgif($_FILES["file"]["tmp_name"]);
$filename = basename($_FILES["file"]["name"],"gif");
break;
case "image/jpeg":
$im2 = imagecreatefromjpeg($_FILES["file"]["tmp_name"]);
$filename = basename($_FILES["file"]["name"],"jpg");
break;
case "image/pjpeg":
$im2 = imagecreatefromjpeg($_FILES["file"]["tmp_name"]);
$filename = basename($_FILES["file"]["name"],"jpg");
break;
}
$path = "users/" . $_SESSION[name] . "/images/" . $filename . "png" ;
imagecopy($im,$im2,0,0,0,0,$width,$height);
imagepng($im,$path);
imagedestroy($im);
imagedestroy($im2);

$short = $_POST['name'];
//Has the image already been uploaded?
$pcheck = mysql_num_rows(mysql_query("SELECT * FROM `backgrounds` WHERE `path` = '$path'"));
if ($pcheck) {
echo "You have already uploaded this image. It is shown below:<br>
<img src='$path' alt='uploaded'>";
}
else {
mysql_query("INSERT INTO `backgrounds` (`path`,`userid`,`name`,`size`,`type`) VALUES ('$path','$q[id]','$short','$size','$type')") or die(mysql_error());

chmod($path,0777);
}
}
}
else {
	echo "Please upload an image that has the extension of png, gif, jpg/jpeg, tiff or bmp.";
echo $type;
}
}
else {
	echo "<b>Please enter a name!</b>";

}
}

?> 
<form action='' method='post' enctype="multipart/form-data">
File Location: <input type='file' name='file'><br>
Name: <input type='text' name='name'><br>
<input type='submit' name='submit' value='submit'>
</form>
<?
include "menu.php";
include "designbottom.php";
?>