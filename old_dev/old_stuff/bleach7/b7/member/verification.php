<?php 
header("Content-type: image/jpeg"); 
$im = imagecreate(12,16); 
$white = imagecolorallocate($im,255,255,255); 
$black = imagecolorallocate($im,0,0,0); 

$new_string = $_GET[code];
$new_string = substr($new_string,17,6); 
$new_string = $new_string[$_GET[p]];

imagefill($im,0,0,$black); 
imagestring($im,3,3,1,$new_string,$white); 
imagejpeg($im); 
imagedestroy($im); 
?>
