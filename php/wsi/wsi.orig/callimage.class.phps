<?php
//Include database settings
header("Content: image/png");
include 'config.php';
$q = mysql_fetch_assoc(mysql_query("SELECT * FROM `logging` WHERE `page` = 'sig'"));
$count = $q['count'] + 1;
mysql_query("UPDATE `logging` SET `count` = '$count' WHERE `page` = 'sig'");

// Class to create, cache, and recall user forum images
class CallImage {
  
  // Define variables
  public $user;
  private $image;
  private $filename;
  private $imagedata;
  private $cached;
  private $userdata;
  private $exists;
  private $db;
  private $db_host;
  private $db_user;
  private $db_pass;
  private $db_name;
  
  // Initiliaze and do initial cache test
	function __construct() {
	  
	  	$this->db_host = 'localhost';
	  	$this->db_user = 'offbea2_whatpuls';
	  	$this->db_pass = 'pulsestats';
	  	$this->db_name = 'offbea2_pulsestats';
	  	
		$this->user = $_GET['user'];
		$this->filename = '/tmp/tmp.png';
		// Check to see if Image is already cached. If not, begin generation process.
		// Else, begin recall process.
		$this->cached = $this->Cached();
		
		if(!$this->cached) {
				// Create the image (not cached)
			  $this->createImage();
		}
	
		else {
				// Recall the image (cached and does not need to be updated).
			$this->recallImage();
		}	
	}
	
	// Create Image Function
	// Takes place of old MAKE file
	
	function createImage() {
	 // echo "at creation <br />";
	  	$this->db = mysql_connect($this->db_host,$this->db_user,$this->db_pass);
		mysql_select_db($this->db_name, $this->db);
		
	  	$result = mysql_query('SELECT * FROM `whatpulse` where user="'.$this->user.'"',$this->db) or die(mysql_error());
	  	$rows = mysql_num_rows($result);
				
		$data = mysql_fetch_assoc($result);
		if (!$rows) {
			header("Content-type: image/png");
			$im = imagecreate(150,100);
			imagecolorallocate($im,255,255,255);
			$color = imagecolorallocate($im,0,0,0);
			imagestring($im,3,5,5,"Invalid user account.",$color);

			imagestring($im,2,50,40,"Whatpulse",$color);
			imagestring($im,2,50,50,"Signature",$color);
			imagestring($im,2,50,60,"Images",$color);
			imagepng($im);
			imagedestroy($im);
			die();
		}
		$im = imagecreatetruecolor($data[width],$data[height]);
		$im2 = imagecreatefrompng($data['path']);

		if (!imagecopy($im, $im2,  0, 0, 0, 0, $data[width], $data[height])) { echo $data['user'] . " has an invalid background image of " . $data['path']; }
		//$trans = imagecolorallocate($im,$data['transred'],$data['transgreen'],$data['transblue']);
		//imagecolortransparent($im,$trans);
		$fontcolor = imagecolorallocate($im, $data[fontred], $data[fontgreen], $data[fontblue]);
		$shadow = imagecolorallocate($im, $data[sred], $data[sgreen], $data[sblue]);
		$blk = imagecolorallocate($im, 0, 0, 0);


		if ($data[se]) {
			if ($data[usere]) {
				imagettftext($im,$data[fontsize],0,$data[userx]+1,$data[usery]+1,$shadow,'fonts/' . $data['font'],'User: ' . $data['user']);
			}
			if ($data[tkce]) {
				imagettftext($im,$data[fontsize],0,$data[tkcx]+1,$data[tkcy]+1,$shadow,'fonts/' . $data['font'],'Keys: ' . number_format($data['tkc']));
			}
			if ($data[tmce]) {
				imagettftext($im,$data[fontsize],0,$data[tmcx]+1,$data[tmcy]+1,$shadow,'fonts/' . $data['font'],'Clicks: ' . number_format($data['tmc']));
			}
			if ($data[ranke]) {
				imagettftext($im,$data[fontsize],0,$data[rankx]+1,$data[ranky]+1,$shadow,'fonts/' . $data['font'],'Rank: ' . number_format($data['rank']));
			}
			if ($data[tnamee]) {
				imagettftext($im,$data[fontsize],0,$data[tnamex]+1,$data[tnamey]+1,$shadow,'fonts/' . $data['font'],'Team: ' . $data['tname']);
			}
			if ($data[countrye]) {
				imagettftext($im,$data[fontsize],0,$data[countryx]+1,$data[countryy]+1,$shadow,'fonts/' . $data['font'],'Country: ' . $data['country']);
			}
			if ($data[tkeyse]) {
				imagettftext($im,$data[fontsize],0,$data[tkeysx]+1,$data[tkeysy]+1,$shadow,'fonts/' . $data['font'],'Team Keys: ' . number_format($data['tkeys']));
			}
			if ($data[tclickse]) {
				imagettftext($im,$data[fontsize],0,$data[tclicksx]+1,$data[tclicksy]+1,$shadow,'fonts/' . $data['font'],'Team Clicks: ' . number_format($data['tclicks']));
			}
			if ($data[tranke]) {
				
imagettftext($im,$data[fontsize],0,$data[trankx]+1,$data[tranky]+1,$shadow,'fonts/' . $data['font'],'Team Rank: ' . $data['trank'] . '/' . $data['tmembers']);
			}
		}
		if ($data[usere]) {
			imagettftext($im,$data[fontsize],0,$data[userx],$data[usery],$fontcolor,'fonts/' . $data['font'],'User: ' . $data['user']);
		}
		if ($data[tkce]) {
			imagettftext($im,$data[fontsize],0,$data[tkcx],$data[tkcy],$fontcolor,'fonts/' . $data['font'],'Keys: ' . number_format($data['tkc']));
		}
		if ($data[tmce]) {
			imagettftext($im,$data[fontsize],0,$data[tmcx],$data[tmcy],$fontcolor,'fonts/' . $data['font'],'Clicks: ' . number_format($data['tmc']));
		}
		if ($data[ranke]) {
			imagettftext($im,$data[fontsize],0,$data[rankx],$data[ranky],$fontcolor,'fonts/' . $data['font'],'Rank: ' . number_format($data['rank']));
		}
		if ($data[tnamee]) {
			imagettftext($im,$data[fontsize],0,$data[tnamex],$data[tnamey],$fontcolor,'fonts/' . $data['font'],'Team: ' . $data['tname']);
		}
		if ($data[countrye]) {
			imagettftext($im,$data[fontsize],0,$data[countryx],$data[countryy],$fontcolor,'fonts/' . $data['font'],'Country: ' . $data['country']);
		}
		if ($data[tkeyse]) {
			imagettftext($im,$data[fontsize],0,$data[tkeysx],$data[tkeysy],$fontcolor,'fonts/' . $data['font'],'Team Keys: ' . number_format($data['tkeys']));
		}
		if ($data[tclickse]) {

			imagettftext($im,$data[fontsize],0,$data[tclicksx],$data[tclicksy],$fontcolor,'fonts/' . $data['font'],'Team Clicks: ' . number_format($data['tclicks']));
		}
		if ($data[tranke]) {
			imagettftext($im,$data[fontsize],0,$data[trankx],$data[tranky],$fontcolor,'fonts/' . $data['font'],'Team Rank: ' . $data['trank'] . '/' . $data['tmembers']);
		}
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
	}
	
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
	
	
	// Recall the stored image for user viewing
	
	function recallImage() {
		//echo "at recall <br />";
		if (!$this->exists) {

		  	$this->db = mysql_connect($this->db_host,$this->db_user,$this->db_pass);
			mysql_select_db($this->db_name, $this->db);		  

			$query = "select imagedata from images where username='".$this->user."'";
		
			if(!$result || mysql_num_rows($result) > 1 || mysql_num_rows($result) < 1) { die('faield'); }

			$this->userdata = mysql_fetch_assoc($result);
				
			mysql_free_result($result);
			mysql_close($this->db);
			
			$this->image = $this->userdata['imagedata'];
		}
		
		else {
		
			$this->image = $this->imagedata;
		
		}
	}
		
	// Output the image to the browser
	function displayImage() {
	  //	echo "at display image <br />";
		echo $this->image;
	}
	
	// Check if image is cached already - both whether or not the image was EVER cached
	// Or if image requires updating.
	function Cached() {
		//echo "at caching <br />";
		$this->exists = FALSE;
	
	  	$this->db = mysql_connect($this->db_host,$this->db_user,$this->db_pass);
		mysql_select_db($this->db_name, $this->db);		
	
		$query = "select id,UNIX_TIMESTAMP(lastupdated),username,imagedata 
from images where username='".$this->user."'";
		$result = mysql_query($query,$this->db) or die("Error: " . mysql_error($this->db));
		
		$num = mysql_num_rows($result);

		mysql_close($this->db);		

		switch ($num) {
			
			case 0:
				return FALSE;
			break;
			case 1:
				// If current UNIXTIME minus LASTUPDATED UNIXTIME is greater than or equal to 21600 seconds
				// (6 hours), needs to be updated.
				$this->userdata = mysql_fetch_assoc($result); $this->exists = TRUE;
				if((time() - 
$this->userdata['lastupdated']) > 21600) {
					$this->imagedata = $this->userdata['imagedata'];
					return FALSE;
					break;
				
				}
				else {
				  	$this->imagedata = $this->userdata['imagedata'];
					return TRUE;
					break;
				}
			break;
			default:
				return FALSE;
			break;
		}
	}
}
