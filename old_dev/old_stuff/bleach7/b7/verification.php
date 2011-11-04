<?php 
header("Content-type: image/jpeg"); 
$im = imagecreate(51,21); 
$white = imagecolorallocate($im,255,255,255); 
$black = imagecolorallocate($im,0,0,0); 

$new_string = $_GET[code];
$new_string = substr($new_string,17,5); 

imagefill($im,0,0,$black); 
imagestring($im,5,3,3,$new_string,$white); 
imagejpeg($im); 
imagedestroy($im); 
?>
