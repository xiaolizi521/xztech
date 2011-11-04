<?php
/* Whatpulse Forum Images Script
/* Created by: RadarListener and AgentGreasy
/* Version: 19.11.2005.5.39.P
/* Last Modiifed By: RadarListener
/* Modification: Removed $dbconn. I had to upload a new config, overwrit the old one.
*/
error_reporting(E_ALL);

/* Whatpulse Forum Images Script*/
ini_set('max_execution_time',360);$content = file_get_contents("bounceme.xml");

include('config.php');
//start timer
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];


$query = 'select user from whatpulse';

$result = mysql_query($query);

while($row = mysql_fetch_assoc($result)) {

	$data = getData(prepforpreg($row['user']),$content);
	if($data) {
	updateData($data);
	}
}

$etimer = explode( ' ', microtime() );
$etimer = $etimer[1] + $etimer[0];

echo '<p style="margin:auto; text-align:center">';
printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) );
echo '</p>';

function getData($username,$content) {

	$data = array();
	$matches = array();
	$newmatch = array();
	$newmatch2 = array();
	//echo "getting " . $username . "'s data <br /><br />";
	preg_match_all('/<user="('.$username.')">(.*?)<\/user>/s',$content,$matches,PREG_PATTERN_ORDER);

	$users = count($matches[1]);
	if ($users > 0) {
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
		return $data;
	}
	}
	/*else {
		
		echo "<strong>USER " . htmlspecialchars($username) . " NOT FOUND <br /><br />DELETING....<br /><br /></strong>";
	
		$query = "DELETE FROM whatpulse WHERE user = '".mysql_real_escape_string(stripcslashes($username))."'";
		$result = mysql_query($query);
		if($result){echo "<STRONG>USER ". $username ." NOW DELETED<br /><br /></strong>";}else{echo "<strong>DELETE FAILURE</strong><br /><br />";}
		
		return false;
	}*/

	/*for ($x = 0; $x < $users; $x++){
	echo "<pre>User Name (". ($x + 1) ."): ".$data[$x]['username'] . '<br />';
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

	

}

function prepforpreg($var){

	$var = addcslashes($var,'[,],/,~,\\');
	$var = addcslashes($var,'(,),|,^,-');
	$var = addcslashes($var,'?,!,$,*');

	return $var;
}

function updateData($data) {

	if ($data[0]['team']) {
		$query = "UPDATE `whatpulse` SET "
		. "tkc = '" . $data[0]['globalkeys']
		. "', tmc = '" . $data[0]['globalclicks']
		. "', rank ='" . $data[0]['globalrank']
		. "', tname ='" . mysql_escape_string($data[0]['teamname'])
		. "', tkeys ='" . $data[0]['teamkeys']
		. "', tclicks ='" . $data[0]['teamclicks']
		. "', trank ='" . $data[0]['teamrank']
		. "', country ='" . mysql_escape_string($data[0]['country'])
		. "', tmembers = '" . $data[0]['teammembers'] ."' where user = '" . $data[0]['username'] . "'";

	}

	else {

		$query = "UPDATE `whatpulse` SET "
		. "tkc = '" . $data[0]['globalkeys']
		. "', tmc = '" . $data[0]['globalclicks']
		. "', rank ='" . $data[0]['globalrank']
		. "', country ='" . $data[0]['country']."' where user = '" . $data[0]['username'] . "'";
	}

	$result = mysql_query($query);

	if(!$result) {

		die("MySQL ERROR: " . mysql_error());
	}

	/*if(mysql_affected_rows($dbconn) < 1) {

	die("Zero Affected Rows - Query Failed.");
	}*/
}

?>

