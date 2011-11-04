<?php
$page = "register.php";
$action = "On Register.php";
include "access.php";
include "header.php";
include "logonav.php";

//start timer
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];


$content = @file_get_contents("bounceme.xml");

/*
if (isset($_GET['user'])){
$username = prepforpreg($_GET['user']);
}
else {
$username = '.*?';
}*/
if($_POST['submit']) {

	if(empty($_POST['password']) || empty($_POST['username'])) {

		die("You did not enter a username or password. Please click back and try again.");
	}

	$host = 'localhost';
	$user = 'offbea2_update';
	$pass = 'pulsestats';
	$dbconn = mysql_connect($host,$user,$pass);
	mysql_select_db('offbea2_pulsestats',$dbconn);

	$query = "select * from whatpulse where user='" . $_POST['username'] . "'";

	$result = mysql_query($query, $dbconn);
	$d = mysql_fetch_assoc($result);
	if($d['user'] == $_POST['username']) {

		$error = <<<EOT
		<div style="text-align: justified; font-size: 14px; font-family: Garamond,Arial,Verdana;">
I'm sorry, but the username you have entered is already in use.<br /><br />
Please press back and try again.<br /><br />
<blockquote>
If you feel this page is a mistake, or if you have forgotten your password,
please contact either RadarListener or AgentGreasy on http://www.frozenplague.net.

Thank you!
</blockquote></div>
<div style="text-align:center; font-size: 14px; font-weight:bold; font-family: Garamond,Tahoma,Verdana;">
-AgentGreasy- && -RadarListener-</div>
EOT;

		die($error);
	}
	else {

		$username = prepforpreg($_POST['username']);

		$password = md5($_POST['password']);

		$data = getData($username,$content);

		$data[0]['password'] = $password;

		insertData($data,$dbconn);

		if(makeImage($data[0]['username'],$dbconn)) {
			doComplete($data[0]['username']);
		}

	}
}
else {

	doRegister();
}

function getData($username,$content) {
	$data = array();
	$matches = "";
	$newmatch="";
	$newmatch2="";
	preg_match_all('/<user="('.$username.')">(.*?)<\/user>/s',$content,$matches,PREG_PATTERN_ORDER);

	$users = count($matches[1]);
	if($users < 1) {
		$error = <<<EOT
		<div style="text-align: justified; font-size: 14px; font-family: Garamond,Arial,Verdana;"><blockquote>
		I'm sorry, but it seems we cant find your username in the whatpulse database.
		This could mean one of two things. One, that you havent pulsed in 2 months (highly unlikely if you're here).
		The second, is that you registered recently and the system has not updated for you yet. If this is the case,
		simply wait a few hours and try again.<br /><br />
		
		If after 6 hours you are still having the same problem, please contact us @ <a href='http://frozenplague.net/forums'>These Forums</a>!
		</blockquote></div>
EOT;
		die($error);
}
for ($x = 0; $x < $users; $x++) {
	preg_match_all("/<.*>(.*)<\/.*>/",$matches[2][$x],$newmatch,PREG_PATTERN_ORDER);
	$data[$x] = array (
	'username' => $matches[1][$x],
	'country' => $newmatch[1][0],
	'globalrank' => $newmatch[1][1],
	'globalkeys' => preg_replace("/,/","",$newmatch[1][2]),
	'globalclicks' => preg_replace("/,/","",$newmatch[1][3]));


	if(preg_match("/<team=\"(.*)\">/",$matches[2][$x],$newmatch2)){
		$data[$x]['team'] = TRUE;
		$data[$x]['teamname'] = $newmatch2[1];
		$var = explode(" of ", $newmatch[1][4]);
		$data[$x]['teammembers'] = $var[1];
		$data[$x]['teamrank'] = $var[0];
		$data[$x]['teamkeys'] = preg_replace("/,/","",$newmatch[1][5]);
		$data[$x]['teamclicks'] = preg_replace("/,/","",$newmatch[1][6]);
	}
	else{
		$data[$x]['team'] = FALSE;
	}
}

/*	for ($x = 0; $x < $users; $x++){
echo "<pre>User Name : ".$data[$x]['username'] . '<br />';
echo "Country: ".$data[$x]['country'] . '<br />';
echo "Rank: ".$data[$x]['globalrank'] . '<br />';
echo "Keys: ".$data[$x]['globalkeys'] . '<br />';
echo "Clicks: ".$data[$x]['globalclicks'] . '<br />';
if ($data[$x]['team']) {
echo "Team Name: ".$data[$x]['teamname'] . '<br />';
echo "Team Rank: ".$data[$x]['teamrank'] .'/'. $data[$x]['teammembers']  . '<br />';
echo "Team Keys: ".$data[$x]['teamkeys'] . '<br />';
echo "Team Clicks: ".$data[$x]['teamclicks'] . '<br />';
}
}*/

return $data;
}

function prepforpreg($var){

	$var = addcslashes($var,'[,],/,~,\\');
	$var = addcslashes($var,'(,),|,^,-');
	$var = addcslashes($var,'?,!,$,*');

	return $var;
}

function insertData($data,$dbconn) {

	if ($data[0]['team']) {

		$query = "INSERT INTO `whatpulse` "
		. "(`user` , `tkc` , `tmc` , `rank` , `tname` , `tkeys` , `tclicks` , `trank` , `country`, `tmembers`, `password`,`regstamp`)"
		. "VALUES ( '" . $data[0]['username']
		. "', '" . $data[0]['globalkeys']
		. "', '" . $data[0]['globalclicks']
		. "', '" . $data[0]['globalrank']
		. "', '" . mysql_escape_string($data[0]['teamname'])
		. "', '" . $data[0]['teamkeys']
		. "', '" . $data[0]['teamclicks']
		. "', '" . $data[0]['teamrank']
		. "', '" . $data[0]['country']
		. "', '" . $data[0]['teammembers']
		. "', '" . $data[0]['password']
		. "', '" . mktime()
		. "')";
	}

	else {

		$query = "INSERT INTO `whatpulse` "
		. "(`user` , `tkc` , `tmc` , `rank` , `country`, `password`)"
		. "VALUES ( '" . $data[0]['username']
		. "', '" . $data[0]['globalkeys']
		. "', '" . $data[0]['globalclicks']
		. "', '" . $data[0]['globalrank']
		. "', '" . $data[0]['country']
		. "', '" . $data[0]['password'] . "')";
	}

	if(!$result = mysql_query($query,$dbconn)) {

		die("MySQL ERROR: " . mysql_error());
	}

	if(mysql_affected_rows($dbconn) < 1) {

		die("Zero Affected Rows - Query Failed.");
	}

}

function makeImage($username,$dbconn) {

	$query = "select * from whatpulse where user = '".$username."'";

	$result = mysql_query($query,$dbconn);

	$data = mysql_fetch_assoc($result);

	$im = imagecreatetruecolor($data['width'],$data['height']);
	$bg = mysql_fetch_assoc(mysql_query("SELECT * FROM `backgrounds` WHERE `id` = '$data[path]'"));
	$im2 = imagecreatefrompng($bg[path]);
	imagecopy($im, $im2,  0, 0, 0, 0, $data['width'], $data['height']);
	$fontcolor = imagecolorallocate($im, $data['fontred'], $data['fontgreen'], $data['fontblue']);
	$altcolor = imagecolorallocate($im, $data['altred'], $data['altgreen'], $data['altblue']);
	$shadow = imagecolorallocate($im, $data['sred'], $data['sgreen'], $data['sblue']);	
	$blk = imagecolorallocate($im, 0, 0, 0);
	imagerectangle($im,0,0,$data['width']-1,$data['height']-1,$blk);
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
			imagettftext($im,$data['fontsize'],0,$data['tkcx']+1,$data['tkcy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',number_format($data['tkc']));
		}
		imagettftext($im,$data['fontsize'],0,$data['tkcx']-$data['tkcs'],$data['tkcy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Keys:");
		imagettftext($im,$data['fontsize'],0,$data['tkcx'],$data['tkcy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',number_format($data['tkc']));
	}
	if ($data['tmce']) {
		if ($data['se']) {
			imagettftext($im,$data['fontsize'],0,$data['tmcx']-$data['tmcs']+1,$data['tmcy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Clicks:");
			imagettftext($im,$data['fontsize'],0,$data['tmcx']+1,$data['tmcy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',number_format($data['tmc']));
		}
		imagettftext($im,$data['fontsize'],0,$data['tmcx']-$data['tmcs'],$data['tmcy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Clicks:");
		imagettftext($im,$data['fontsize'],0,$data['tmcx'],$data['tmcy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',number_format($data['tmc']));
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
			imagettftext($im,$data['fontsize'],0,$data['tnamex']-$data['tnames']+1,$data['tnamey']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Team:");
			imagettftext($im,$data['fontsize'],0,$data['tnamex']+1,$data['tnamey']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['tname']);
		}
		imagettftext($im,$data['fontsize'],0,$data['tnamex']-$data['tnames'],$data['tnamey'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team:");
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
			imagettftext($im,$data['fontsize'],0,$data['tkeysx']+1,$data['tkeysy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',number_format($data['tkeys']));
		}
		imagettftext($im,$data['fontsize'],0,$data['tkeysx']-$data['tkeyss'],$data['tkeysy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team Keys:");
		imagettftext($im,$data['fontsize'],0,$data['tkeysx'],$data['tkeysy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',number_format($data['tkeys']));
	}
	if ($data['tclickse']) {
		if ($data['se']) {
			imagettftext($im,$data['fontsize'],0,$data['tclicksx']-$data['tclickss']+1,$data['tclicksy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Team Clicks:");
			imagettftext($im,$data['fontsize'],0,$data['tclicksx']+1,$data['tclicksy']+1,$shadow,'fonts/' . $data['font'] . '.ttf',number_format($data['tclicks']));
		}
		imagettftext($im,$data['fontsize'],0,$data['tclicksx']-$data['tclickss'],$data['tclicksy'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team Clicks:");
		imagettftext($im,$data['fontsize'],0,$data['tclicksx'],$data['tclicksy'],$fontcolor,'fonts/' . $data['font'] . '.ttf',number_format($data['tclicks']));
	}
	if ($data['tranke']) {
		if ($data['se']) {
			imagettftext($im,$data['fontsize'],0,$data['trankx']-$data['tranks']+1,$data['tranky']+1,$shadow,'fonts/' . $data['font'] . '.ttf',"Team Rank:");
			imagettftext($im,$data['fontsize'],0,$data['trankx']+1,$data['tranky']+1,$shadow,'fonts/' . $data['font'] . '.ttf',$data['trank'] . " / " . $data['tmembers']);
		}
		imagettftext($im,$data['fontsize'],0,$data['trankx']-$data['tranks'],$data['tranky'],$altcolor,'fonts/' . $data['font'] . '.ttf',"Team Rank:");
		imagettftext($im,$data['fontsize'],0,$data['trankx'],$data['tranky'],$fontcolor,'fonts/' . $data['font'] . '.ttf',$data['trank'] . " / " . $data['tmembers']);
	}
	$file = "sig/$data[user].png";
	imagepng($im,$file);
chmod($file, 0777);
}

function doRegister() {
	$html = <<<HTML
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html>
	<title>Whatpulse Signature Images - Registration</title>
	<body>
		Register page is down whilst we work on the system. Sorry for any troubles caused.
	</body>
	</html>
HTML;
	echo $html;
}

function doComplete($username) {
	$html = <<<HTML
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html>
	<title>Whatpulse Signature Images - Registration</title>
	<body>
		<blockquote><div style="text-align: justified; font-size: 14px; font-family: Garamond,Arial,Verdana;">
		Welcome to the Whatpulse Signature Images Project!<br />
		Thank you for registering {$username}! You can now go <a href="http://pulse.offbeat-zero.net/login.php" alt="Login">Here</a> 
		and start customizing your image!<br />
		</div></blockquote>
	</body>
	</html>
HTML;
	echo $html;
}
//echo "If nothing is shown then it probably means you are registered.";
?>