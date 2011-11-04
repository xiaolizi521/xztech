<?
include "config.php";
$q = mysql_query("SELECT * FROM `whatpulse`");
while ($a = mysql_fetch_assoc($q)) {
$im = @imagecreate(100, 50)
   or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 255, 255, 255);
$text_color = imagecolorallocate($im, 233, 14, 91);
if (!imagettftext($im,8,0,5,5, $text_color, "fonts/" . $a['font'], "Test")) {
mysql_query("UPDATE `whatpulse` SET `font` = 'tahoma.ttf' WHERE `id` = '$a[id]'");
}
imagedestroy($im);
}
?>