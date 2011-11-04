<?php

class CreateImage extends CallImage {
 	
 	private $db;
 	
	function __constructor($db) {
	 
		$this->db = $db;
	}
	
	function createImage() {
	 // echo "at creation <br />";
	
		try {
		
			$result = $this->db->query('SELECT * FROM `whatpulse` where user="'.$this->user.'"');
		}
		
	/* Catch any query errors or other errors. */
		catch(QueryException $exception) {
			
			echo "Query Error\n";
			var_dump($exception->getMessage());
		}
		
		catch(Exception $exception) {
			
			echo "Other Script Error\n";
			var_dump($exception->getMessage());
		}
	  	
		if (!$result->num_rows()) {

			$im = imagecreate(150,100);
			imagecolorallocate($im,255,255,255);
			$color = imagecolorallocate($im,0,0,0);
			imagestring($im,3,5,5,"Invalid user account.",$color);

			imagestring($im,2,50,40,"Whatpulse",$color);
			imagestring($im,2,50,50,"Signature",$color);
			imagestring($im,2,50,60,"Images",$color);
			
			imagepng($im);
			imagedestroy($im);
			exit(0);
		}
		
		$data = $result->fetch_assoc();		
		$result->close();
		
		$im = imagecreatetruecolor($data[width],$data[height]);
		$im2 = imagecreatefrompng($data['path']);

// ************ MODIFY ********** CREATE EXCEPTION CODE *********** MODIFY *********
		try {
			
			imagecopy($im, $im2, 0, 0, 0, 0, $data['width'], $data['height']))
		}
		
		catch
// ************ MODIFY ********** CREATE EXCEPTION CODE *********** MODIFY *********
// ************ MIRROR CODE BELOW ****************

			if (!imagecopy($im, $im2,  0, 0, 0, 0, $data['width'], $data['height'])) { 
			 
			 	echo $data['user'] . " has an invalid background image of " . $data['path']; 
			}
// *******************************************************************************************************************
		
		$fontcolor = imagecolorallocate($im, $data['fontred'], $data['fontgreen'], $data['fontblue']);
		
		$shadow = imagecolorallocate($im, $data['sred'], $data['sgreen'], $data['sblue']);
		
		$blk = imagecolorallocate($im, 0, 0, 0);
		
		/* Set the Column Names and Lables */
		$columns = array(	"user",
							"tkc",
							"tmc",
							"rank",
							"tname",
							"country",
							"tkeys",
							"tclicks",
							"trank");
							
		$lables = array(	"User",
							"Keys",
							"Clicks",
							"Rank",
							"Team",
							"Country",
							"Team Keys",
							"Team Clicks",
							"Team Rank");

		/* Begin array parsing loop */
		for ($x=0; $x<count($columns); $x++) {
			
			/* Format all large numbers for proper text display (commas for > 999 "1,000" "1,000,000" */
			if ($columns[$x] != "user" || $columns[$x] != "tname" || $columns[$x] != "country") {
			
				$data[$columns[$x]] = number_format($data[$columns[$x]]);
			}		
			
			/* Check to see if the current section is enabled */		
			if ($data[$columns[$x].'e']) {
				
				/* Check for shadows */
			 	if ($data['se']) {
			 	 	
			 	 	/* Draw shadows */
			 	 	imagettftxt($im,
					  $data['fontsize'],
					  0,
					  $data[$columns[$x].'x']+1,
					  $data[$columns[$x].'y']+1,
					  $data['shadow'],
					  'fonts/' . $data['font'],
					  $lables[$x] . ": " . $data[$columns[$x]]);
			 	}
			 	
			 	/* Draw text */
			 	imagettftxt($im,
				 $data['fontsize'],
				 0,
				 $data[$columns[$x].'x'],
				 $data[$columns[$x].'y'],
				 $data['fontcolor'],
				 'fonts/' . $data['font'],
				 $lables[$x] . ": " . $data[$columns[$x]]);
			}
		}
		
		/* Draw border, if desired */
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
		
		// Begin cache process of new/updated image then display the image.
		if($this->storeImage()) {
			
			$this->displayImage();
		}
	}
	
	function storeImage() {
	 		
		// If the user does NOT exist, create row for user to be cached in.
		if(!$this->exists) {
		 	
			$query = "INSERT INTO `images` (`username` , `imagedata` , `lastupdated`) VALUES ('".$this->user."','".addslashes($this->imagedata)."',NOW());";
		}
		// Else, update the user's old image in the cache - and set lastupdated time to NOW.
		else {

			$query = "UPDATE `images` SET imagedata='".addslashes($this->imagedata)."' and lastupdated=NOW() WHERE `username` = '".$this->user."'";
		}
		
		try {
		 
			$result = $this->db->query($query);
		}
		
	/* Catch any query errors or other errors. */
		catch(QueryException $exception) {
			
			echo "Query Error\n";
			var_dump($exception->getMessage());
		}
		
		catch(Exception $exception) {
			
			echo "Other Script Error\n";
			var_dump($exception->getMessage());
		}
		
		if ($this->db->affected_rows() >= 1) {
		
			return 1;
		}
		
		else {
		 
			return 0;
		}
	}
	
}