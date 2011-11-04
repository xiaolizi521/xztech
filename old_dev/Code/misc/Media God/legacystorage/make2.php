#!/usr/bin/php -q
<?php
$page = "make2.php";
$action = "Leet haxx0ring the make2.php";
/* Whatpulse Forum Images Script
/* Created by: RadarListener and AgentGreasy
/* Version: 29.11.2005.12.39.P
/* Last Modiifed By: RadarListener
/* Modification: If there was a shadow, it was putting in the text twice.
/* Modification: Replaced all replacable " with ' and ' with nothing.
/* To be done: Reduce Image Creation, streamline, and make more efficient.

/* Max Execution Time to 360 seconds, just in case. */
ini_set('max_execution_time',360);

// Load Config Settings
include('config.php');

//start timer
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];

/* Query to select all data from whatpulse database.*/
$result = mysql_query('SELECT * FROM `whatpulse`') or die(mysql_error());
$rows = mysql_num_rows($result);

/* Propagate data through image creation algorithms, 1 by 1. To be looked at for better method. */
while($data = mysql_fetch_array($result)) {

	$im = imagecreatetruecolor($data[width],$data[height]);
	$im2 = imagecreatefrompng($data['path']);

	imagecopy($im, $im2,  0, 0, 0, 0, $data[width], $data[height]);
	//$trans = imagecolorallocate($im,$data['transred'],$data['transgreen'],$data['transblue']);
	//imagecolortransparent($im,$trans);
	$fontcolor = imagecolorallocate($im, $data[fontred], $data[fontgreen], $data[fontblue]);
	$shadow = imagecolorallocate($im, $data[sred], $data[sgreen], $data[sblue]);
	$blk = imagecolorallocate($im, 0, 0, 0);


	if ($data[se]) {
		if ($data[usere]) {
			imagettftext($im,$data[fontsize],0,$data[userx]+1,$data[usery]+1,$shadow,'fonts/' . $data['font'],'User: ' . $data[user]);
		}
		if ($data[tkce]) {
			imagettftext($im,$data[fontsize],0,$data[tkcx]+1,$data[tkcy]+1,$shadow,'fonts/' . $data['font'],'Keys: ' . number_format($data[tkc]));
		}
		if ($data[tmce]) {
			imagettftext($im,$data[fontsize],0,$data[tmcx]+1,$data[tmcy]+1,$shadow,'fonts/' . $data['font'],'Clicks: ' . number_format($data[tmc]));
		}
		if ($data[ranke]) {
			imagettftext($im,$data[fontsize],0,$data[rankx]+1,$data[ranky]+1,$shadow,'fonts/' . $data['font'],'Rank: ' . number_format($data[rank]));
		}
		if ($data[tnamee]) {
			imagettftext($im,$data[fontsize],0,$data[tnamex]+1,$data[tnamey]+1,$shadow,'fonts/' . $data['font'],'Team: ' . $data[tname]);
		}
		if ($data[countrye]) {
			imagettftext($im,$data[fontsize],0,$data[countryx]+1,$data[countryy]+1,$shadow,'fonts/' . $data['font'],'Country: ' . $data[country]);
		}
		if ($data[tkeyse]) {
			imagettftext($im,$data[fontsize],0,$data[tkeysx]+1,$data[tkeysy]+1,$shadow,'fonts/' . $data['font'],'Team Keys: ' . number_format($data[tkeys]));
		}
		if ($data[tclickse]) {
			imagettftext($im,$data[fontsize],0,$data[tclicksx]+1,$data[tclicksy]+1,$shadow,'fonts/' . $data['font'],'Team Clicks: ' . number_format($data[tclicks]));
		}
		if ($data[tranke]) {
			imagettftext($im,$data[fontsize],0,$data[trankx]+1,$data[tranky]+1,$shadow,'fonts/' . $data['font'],'Team Rank: ' . number_format($data[trank]) . '/' . number_format($data[tmembers]));
		}
	}
	if ($data[usere]) {
		imagettftext($im,$data[fontsize],0,$data[userx],$data[usery],$fontcolor,'fonts/' . $data['font'],'User: ' . $data[user]);
	}
	if ($data[tkce]) {
		imagettftext($im,$data[fontsize],0,$data[tkcx],$data[tkcy],$fontcolor,'fonts/' . $data['font'],'Keys: ' . number_format($data[tkc]));
	}
	if ($data[tmce]) {
		imagettftext($im,$data[fontsize],0,$data[tmcx],$data[tmcy],$fontcolor,'fonts/' . $data['font'],'Clicks: ' . number_format($data[tmc]));
	}
	if ($data[ranke]) {
		imagettftext($im,$data[fontsize],0,$data[rankx],$data[ranky],$fontcolor,'fonts/' . $data['font'],'Rank: ' . number_format($data[rank]));
	}
	if ($data[tnamee]) {
		imagettftext($im,$data[fontsize],0,$data[tnamex],$data[tnamey],$fontcolor,'fonts/' . $data['font'],'Team: ' . $data[tname]);
	}
	if ($data[countrye]) {
		imagettftext($im,$data[fontsize],0,$data[countryx],$data[countryy],$fontcolor,'fonts/' . $data['font'],'Country: ' . $data[country]);
	}
	if ($data[tkeyse]) {
		imagettftext($im,$data[fontsize],0,$data[tkeysx],$data[tkeysy],$fontcolor,'fonts/' . $data['font'],'Team Keys: ' . number_format($data[tkeys]));
	}
	if ($data[tclickse]) {

		imagettftext($im,$data[fontsize],0,$data[tclicksx],$data[tclicksy],$fontcolor,'fonts/' . $data['font'],'Team Clicks: ' . number_format($data[tclicks]));
	}
	if ($data[tranke]) {
		imagettftext($im,$data[fontsize],0,$data[trankx],$data[tranky],$fontcolor,'fonts/' . $data['font'],'Team Rank: ' . number_format($data[trank]) . '/' . number_format($data[tmembers]));
	}
	$file = 'sig/'.$data[user].'.png';
	if ($data[be]) {
		imagerectangle($im,0,0,$data[width]-1,$data[height]-1,$blk);
	}

	// Store image temporarily at TMP storage
		imagepng($im,$this->filename);
		imagedestroy($im);
	// Store image data into variable for manipulation
		$this->imagedata = file_get_contents($this->filename); 
	// Make sure image is readable later on by browser (unmodified binary)
		$this->image = $this->imagedata;
		mysql_free_result($result);
		mysql_close($this->db);
	// Begin cache process of new/updated image
		$this->imageCache();
	
	function imageCache() {
	  //echo "at imagecache() <br />";
	 	$this->db = mysql_connect($this->db_host,$this->db_user,$this->db_pass);
		mysql_select_db($this->db_name, $this->db);
	
		// If the user does NOT exist, create row for user to be cached in.
		if(!$this->exists) {
		  
			$this->db = mysql_connect($this->db_host,$this->db_user,$this->db_pass);
			mysql_select_db($this->db_name, $this->db);
	
			$query = "INSERT INTO `images` (`username` , `imagedata` , `lastupdated`) VALUES ('".$this->user."','".addslashes($this->imagedata)."',NOW());";
	
			$result = mysql_query($query,$this->db);
		}
		// Else, update the user's old image in the cache - and set lastupdated time to NOW.
		else {

			$query = "UPDATE `images` SET imagedata='".addslashes($this->imagedata)."' and lastupdated=NOW() WHERE `username` = '".$this->user."'";
			
			$result = mysql_query($query,$this->db);
		}
			
		if (!$result) { die("query failed: ".mysql_error($this->db)); }
		
		mysql_close($this->db);
		
		$this->exists=TRUE;
			
	}
}

// Stop Timer and Print Results
$etimer = explode( ' ', microtime() );
$etimer = $etimer[1] + $etimer[0];

echo '<p style="margin:auto; text-align:center">';
$time = $etimer-$stimer;
printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) );
$total = $time / $rows;
echo "Avg time / user: $total seconds.";
echo '</p>';

?> 
