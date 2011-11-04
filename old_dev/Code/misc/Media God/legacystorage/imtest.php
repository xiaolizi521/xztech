<?php
header("Content-Type: image/png");
$host = 'localhost';
$user = 'offbea2_update';
$pass = 'pulsestats';
$dbconn = mysql_connect($host,$user,$pass);
mysql_select_db('offbea2_pulsestats',$dbconn);

$query = "select * from whatpulse where user='AgentGreasy'";

$result = mysql_query($query, $dbconn);
$data = mysql_fetch_assoc($result);

$im = imagecreatetruecolor($data['x'],$data['y']);
$im2 = imagecreatefrompng("img/" . $data['theme'] . ".png");
imagecopy($im, $im2,  0, 0, 0, 0, $data['x'], $data['y']);
$fontcolor = imagecolorallocate($im, $data['fontred'], $data['fontgreen'], $data['fontblue']);
$altcolor = imagecolorallocate($im, $data['altred'], $data['altgreen'], $data['altblue']);
$shadow = imagecolorallocate($im, $data['sred'], $data['sgreen'], $data['sblue']);
$blk = imagecolorallocate($im, 0, 0, 0);
imagerectangle($im,0,0,$data['x']-1,$data['y']-1,$blk);
if ($data['usere']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['userx']-$data['users']+1,$data['usery']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"User:");
		imagettftext($im,$data['fontsize'],0,$data['userx']+1,$data['usery']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['user']);
	}
	imagettftext($im,$data['fontsize'],0,$data['userx']-$data['users'],$data['usery'],$altcolor,'fonts/' . $data['font'] . '.ttf',"User:");
	imagettftext($im,$data['fontsize'],0,$data['userx'],$data['usery'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['user']);
}
if ($data['tkce']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['tkcx']-$data['tkcs']+1,$data['tkcy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Keys:");
		imagettftext($im,$data['fontsize'],0,$data['tkcx']+1,$data['tkcy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['tkc']);
	}
	imagettftext($im,$data['fontsize'],0,$data['tkcx']-$data['tkcs'],$data['tkcy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Keys:");
	imagettftext($im,$data['fontsize'],0,$data['tkcx'],$data['tkcy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['tkc']);
}
if ($data['tmce']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['tmcx']-$data['tmcs']+1,$data['tmcy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Clicks:");
		imagettftext($im,$data['fontsize'],0,$data['tmcx']+1,$data['tmcy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['tmc']);
	}
	imagettftext($im,$data['fontsize'],0,$data['tmcx']-$data['tmcs'],$data['tmcy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Clicks:");
	imagettftext($im,$data['fontsize'],0,$data['tmcx'],$data['tmcy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['tmc']);
}
if ($data['ranke']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['rankx']-$data['ranks']+1,$data['ranky']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Rank:");
		imagettftext($im,$data['fontsize'],0,$data['rankx']+1,$data['ranky']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['rank']);
	}
	imagettftext($im,$data['fontsize'],0,$data['rankx']-$data['ranks'],$data['ranky'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Rank:");
	imagettftext($im,$data['fontsize'],0,$data['rankx'],$data['ranky'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['rank']);
}
if ($data['tnamee']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['tnamex']-$data['tnames']+1,$data['tnamey']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Team Name:");
		imagettftext($im,$data['fontsize'],0,$data['tnamex']+1,$data['tnamey']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['tname']);
	}
	imagettftext($im,$data['fontsize'],0,$data['tnamex']-$data['tnames'],$data['tnamey'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team Name:");
	imagettftext($im,$data['fontsize'],0,$data['tnamex'],$data['tnamey'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['tname']);
}
if ($data['kpse']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['kpsx']-$data['kpss']+1,$data['kpsy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"KPS:");
		imagettftext($im,$data['fontsize'],0,$data['kpsx']+1,$data['kpsy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['kps']);
	}
	imagettftext($im,$data['fontsize'],0,$data['kpsx']-$data['kpss'],$data['kpsy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"KPS:");
	imagettftext($im,$data['fontsize'],0,$data['kpsx'],$data['kpsy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['kps']);
}
if ($data['cpse']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['cpsx']-$data['cpss']+1,$data['cpsy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"CPS:");
		imagettftext($im,$data['fontsize'],0,$data['cpsx']+1,$data['cpsy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['cps']);
	}
	imagettftext($im,$data['fontsize'],0,$data['cpsx']-$data['cpss'],$data['cpsy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"CPS:");
	imagettftext($im,$data['fontsize'],0,$data['cpsx'],$data['cpsy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['cps']);
}
if ($data['countrye']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['countryx']-$data['countrys']+1,$data['countryy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Country:");
		imagettftext($im,$data['fontsize'],0,$data['countryx']+1,$data['countryy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['country']);
	}
	imagettftext($im,$data['fontsize'],0,$data['countryx']-$data['countrys'],$data['countryy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Country:");
	imagettftext($im,$data['fontsize'],0,$data['countryx'],$data['countryy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['country']);
}
if ($data['tkeyse']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['tkeysx']-$data['tkeyss']+1,$data['tkeysy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Team Keys:");
		imagettftext($im,$data['fontsize'],0,$data['tkeysx']+1,$data['tkeysy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['tkeys']);
	}
	imagettftext($im,$data['fontsize'],0,$data['tkeysx']-$data['tkeyss'],$data['tkeysy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team Keys:");
	imagettftext($im,$data['fontsize'],0,$data['tkeysx'],$data['tkeysy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['tkeys']);
}
if ($data['tclickse']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['tclicksx']-$data['tclickss']+1,$data['tclicksy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Team Clicks:");
		imagettftext($im,$data['fontsize'],0,$data['tclicksx']+1,$data['tclicksy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['tclicks']);
	}
	imagettftext($im,$data['fontsize'],0,$data['tclicksx']-$data['tclickss'],$data['tclicksy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team Clicks:");
	imagettftext($im,$data['fontsize'],0,$data['tclicksx'],$data['tclicksy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['tclicks']);
}
if ($data['tranke']) {
	if ($data['se']) {
		imagettftext($im,$data['fontsize'],0,$data['trankx']-$data['tranks']+1,$data['tranky']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Team Rank:");
		imagettftext($im,$data['fontsize'],0,$data['trankx']+1,$data['tranky']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['trank'] . " / " . $data['tmembers']);
	}
	imagettftext($im,$data['fontsize'],0,$data['trankx']-$data['tranks'],$data['tranky'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team Rank:");
	imagettftext($im,$data['fontsize'],0,$data['trankx'],$data['tranky'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['trank'] . " / " . $data['tmembers']);
}
echo imagepng($im);
//imagepng($im,"sig/".$data['user'].".png");
imagedestroy($im);
?>
