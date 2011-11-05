<?php
header("Content-type: image/png");

$im = imagecreatetruecolor(150,100);
$im2 = imagecreatetruecolor(150,100);
$color = imagecolorallocate($im2,200,200,200);
imagefill($im2, 0, 0, $color);

imagesavealpha($im, true);
$transcolor = imagecolorallocatealpha($im,0,0,0,127);
imagefill($im, 0, 0, $transcolor);


imagestring($im,3,5,5,"Invalid user account.",$color);

imagestring($im,2,50,40,"Whatpulse",$color);
imagestring($im,2,50,50,"Signature",$color);
imagestring($im,2,50,60,"Images",$color);

imagecopy($im2, $im, 0, 0, 0, 0, 150, 100);


imagepng($im2);

imagedestroy($im2);
imagedestroy($im);

?>