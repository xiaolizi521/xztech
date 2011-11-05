<?
function makesig() {
$query = "select * from whatpulse where `user` = '".$_SESSION['name']."'";


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
		imagettftext($im,$data['fontsize'],0,$data['tkcx'],$data['tkcy'],$fontcolor,'fonts/' . $data['font'],"Keys: " . number_format($data['tkc']));
	}
	if ($data['tmce']) {
		imagettftext($im,$data['fontsize'],0,$data['tmcx'],$data['tmcy'],$fontcolor,'fonts/' . $data['font'],"Clicks: " . number_format($data['tmc']));
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
		imagettftext($im,$data['fontsize'],0,$data['tkeysx'],$data['tkeysy'],$fontcolor,'fonts/' . $data['font'],"Team Keys: " . number_format($data['tkeys']));
	}
	if ($data['tclickse']) {
			
			imagettftext($im,$data['fontsize'],0,$data['tclicksx'],$data['tclicksy'],$fontcolor,'fonts/' . $data['font'],"Team Clicks: " . number_format($data['tclicks']));
		}
		if ($data['tranke']) {
			imagettftext($im,$data['fontsize'],0,$data['trankx'],$data['tranky'],$fontcolor,'fonts/' . $data['font'],"Team Rank: " . $data['trank'] . "/" . $data['tmembers']);
		}
	}
	$file = "sig/".$data['user'].".png";

	imagepng($im,$file);

	imagedestroy($im);
chmod($file, 0766);
}