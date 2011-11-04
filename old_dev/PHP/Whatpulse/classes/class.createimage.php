<?php

class BgFail extends Exception {}
class ImgException extends Exception {}

class CreateImage extends CallImage {
  	
	private $db;
	private $exists;
	
	function __construct($db,$exists,$user) {
	 	
		$this->db = $db;
		$this->exists = $exists;
		$this->user = $user;
	 
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
	  	
		if (!$result->num_rows) {
			$this->non_existantuser();
		}
		
		$this->data = $result->fetch_assoc();		
		$result->close();
		
		try {
			
			$this->generate_bg();
		}
		
		catch(BgFail $exception) {
			
			$this->query = "UPDATE whatpulse SET `path` = 'img/banana.png' where `user` = " . $this->data['user'];
				
			try {
			
				$result = $this->db->query($this->query);
			}
			
			catch(QueryException $exception) {
				
				echo "Query Error\n";
				var_dump($exception->getMessage());
			}
			
			catch(Exception $exception) {
				
				echo "Other Script Error\n";
				var_dump($exception->getMessage());
			}
			
			$result->close();
			
			$this->query = "DELETE * from `images` WHERE `user` = " . $this->data['user'];
				
			try {
			
				$result = $this->db->query($this->query);
			}
			
			catch(QueryException $exception) {
				
				echo "Query Error\n";
				var_dump($exception->getMessage());
			}
			
			catch(Exception $exception) {
				
				echo "Other Script Error\n";
				var_dump($exception->getMessage());
			}
			
			$result->close();
			
			header('Location: http://pulse.offbeat-zero.net/sig/'.$this->data['user'].'.png');
			
		}
		
		catch(Exception $exception) {
			echo "Ohter Script Error\n";
			var_dump($exception->getMessage());
		}
		
		$this->data['fontcolor'] = imagecolorallocate($this->im, $this->data['fontred'], $this->data['fontgreen'], $this->data['fontblue']);
		
		$this->data['shadow'] = imagecolorallocate($this->im, $this->data['sred'], $this->data['sgreen'], $this->data['sblue']);
		
		$this->data['blk'] = imagecolorallocate($this->im, 0, 0, 0);
		
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
		$format = array(0,1,1,1,0,0,1,1,1);
		
		/* Begin array parsing loop */
		for ($x=0; $x<count($columns); $x++) {
			
			/* Format all large numbers for proper text display (commas for > 999 "1,000" "1,000,000" */
			if ($format[$x]) {
			
				$this->data[$columns[$x]] = number_format($this->data[$columns[$x]]);
			}		
			
			/* Check to see if the current section is enabled */		
			if ($this->data[$columns[$x].'e']) {
				
				/* Check for shadows */
			 	if ($this->data['se']) {
			 	 	
			 	 	/* Draw shadows */
			 	 	imagettftext($this->im,
					  $this->data['fontsize'],
					  0,
					  $this->data[$columns[$x].'x']+1,
					  $this->data[$columns[$x].'y']+1,
					  $this->data['shadow'],
					  'fonts/' . $this->data['font'],
					  $lables[$x] . ": " . $this->data[$columns[$x]]);
			 	}
			 	
			 	/* Draw text */
			 	imagettftext($this->im,
				 $this->data['fontsize'],
				 0,
				 $this->data[$columns[$x].'x'],
				 $this->data[$columns[$x].'y'],
				 $this->data['fontcolor'],
				 'fonts/' . $this->data['font'],
				 $lables[$x] . ": " . $this->data[$columns[$x]]);
			}
		}
		
		/* Draw border, if desired */
		if ($this->data[be]) {
			imagerectangle($this->im,0,0,$this->data['width']-1,$this->data['height']-1,$blk);
		}
		$this->filename='/tmp/tmp2.png';
		// Store image temporarily at TMP storage
		imagepng($this->im,$this->filename);
		imagedestroy($this->im);
	
		// Store image data into variable for manipulation
		$this->imagedata = file_get_contents($this->filename); 
	
		// Make sure image is readable later on by browser (unmodified binary)
		$this->image = $this->imagedata;
		
		// Begin cache process of new/updated image then display the image.
		if($this->storeImage()) {
			parent::displayImage($this->image);
		}
	}
	
	function storeImage() {
	 	
		// Check to see if user exists
		
		//$query = "SELECT id FROM images WHERE username = '" .$this->user."'";
		
		
		// If the user does NOT exist, create row for user to be cached in.
		 	
		$query = "INSERT INTO `images` (`username` , `imagedata` , `lastupdated`) VALUES ('".$this->user."','".addslashes($this->imagedata)."',NOW());";
	
		if($this->exists) {
		
			$querydeleted = "DELETE * FROM `images` WHERE `username`='".$this->user."'";
			
			try {
			 
				$result2 = $this->db->query($querydeleted);
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
		
		if ($this->db->affected_rows >= 1) {
		
			return 1;
		}
		
		else {
		 
			return 0;
		}
	}
	
	function nonexistantuser() {
		
		$this->im = imagecreate(150,100);
		imagecolorallocate($this->im,255,255,255);
		$color = imagecolorallocate($this->im,0,0,0);
		imagestring($this->im,3,5,5,"Invalid user account.",$color);

		imagestring($this->im,2,50,40,"Whatpulse",$color);
		imagestring($this->im,2,50,50,"Signature",$color);
		imagestring($this->im,2,50,60,"Images",$color);
			
		imagepng($this->im);
		imagedestroy($this->im);
		exit(0);
		
	}
	
	function generate_bg() {
		
		$path = 'http://pulse.offbeat-zero.net/' . $this->data['path'];
		$this->im = imagecreatetruecolor($this->data['width'],$this->data['height']);
		$this->im2 = imagecreatefrompng($path);
		
		error_reporting(0);
		if(!imagecopy($this->im, $this->im2, 0, 0, 0, 0, $this->data['width'], $this->data['height'])) {	
			throw new BgFail();
		}
		error_reporting(E_WARNING);
	}
}