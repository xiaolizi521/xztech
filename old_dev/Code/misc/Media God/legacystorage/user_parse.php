<pre>
<?
/*
*	readUserStats(userid)
*		-	Read WhatPulse user statistics from the webapi into an array.
*
*	Author: wasted@whatpulse.org
*/
function readUserStats(/*$userid*/$username,$dbConn)
{
	
	/*// prepare an array to hold your stats
	$WhatPulseStats = array();

	// types of statistics
	$stat_types = array("UserID", "AccountName", "Country",
	"DateJoined", "Homepage", "LastPulse",
	"Pulses", "TotalKeyCount", "TotalMouseClicks",
	"AvKeysPerPulse", "AvClicksPerPulse",
	"AvKPS", "AvCPS", "Rank", "TeamID",
	"TeamName", "TeamMembers", "TeamKeys",
	"TeamClicks", "TeamDescription",
	"TeamDateFormed", "RankInTeam", "GeneratedTime");

	// init the xml parser and read the data into an array
	$filename = "http://whatpulse.org/api/users/".$userid.".xml";
	$exists = @fopen($filename , "r");
	if ($exists) {
	$data = implode("", file($filename));
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $values, $tags);
	xml_parser_free($parser);

	// loop through the structures
	foreach ($tags as $key => $val)
	{
	// only process stuff between the <UserStats> tags
	if ($key == "UserStats")
	{
	// loop through the tags inside <UserStats>
	$ranges = $val;
	for ($i = 0; $i < count($ranges); $i += 2)
	{
	$offset = $ranges[$i] + 1;
	$len = $ranges[$i + 1] - $offset;
	$statsarray = array_slice($values, $offset, $len);

	// loop through the structure of the xml tag
	foreach($statsarray as $key => $value)
	{
	// match to a stats_type
	for($i = 0; $i < count($stat_types); $i++)
	{
	if($value['tag'] == $stat_types[$i])
	{
	// remember the value of the stats_type
	$type = $stat_types[$i];
	$WhatPulseStats[$type] = $value['value'];
	}
	}
	}
	}
	}
	else {
	continue;
	}
	}

	return $WhatPulseStats;
	}
	$exists = 0;*/
	
	$data = getData($username);
	
	insertData($data,$dbconn);
	
	return $data;
}

function getData($username,$content) {
	$data = array();
	preg_match_all('/<user="('.$username.')">(.*?)<\/user>/s',$content,$matches,PREG_PATTERN_ORDER);

	$users = count($matches[1]);

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

	for ($x = 0; $x < $users; $x++){
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
	}

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

		$query = "INSERT IGNORE INTO `whatpulse` "
		. "(`user` , `tkc` , `tmc` , `rank` , `tname` , `tkeys` , `tclicks` , `trank` , `country`, `tmembers`)"
		. "VALUES ( '" . $data[$x]['username']
		. "', '" . $data[$x]['globalkeys']
		. "', '" . $data[$x]['globalclicks']
		. "', '" . $data[$x]['globalrank']
		. "', '" . $data[$x]['teamname']
		. "', '" . $data[$x]['teamkeys']
		. "', '" . $data[$x]['teamclicks']
		. "', '" . $data[$x]['teamrank']
		. "', '" . $data[$x]['country']
		. "', '" . $data[$x]['teammembers'] . "')";
	}

	else {

		$query = "INSERT IGNORE INTO `whatpulse` "
		. "(`user` , `tkc` , `tmc` , `rank` , `country`)"
		. "VALUES ( '" . $data[$x]['username']
		. "', '" . $data[$x]['globalkeys']
		. "', '" . $data[$x]['globalclicks']
		. "', '" . $data[$x]['globalrank']
		. "', '" . $data[$x]['country'] . "')";
	}

	if(!$result = mysql_query($query,$dbconn)) {

		die("MySQL ERROR: " . mysql_error());
	}

	if(mysql_affected_rows($dbconn) < 1) {

		die("Zero Affected Rows - Query Failed.");
	}

}
?> 
</pre>