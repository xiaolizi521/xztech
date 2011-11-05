<?php
// Set the browser to expect a PNG Image
require "class.wsidb.php";
require "class.createimage.php";

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
  
  // Initiliaze and do initial cache test
	function __construct($user) {
	  //	echo "I AM HERE!!!!";
		try {

			$this->db = new wsiDB('localhost', 'whatpulse', 
			'FU55mwh3CzfZBFSK', 'whatpulse');
			
			#stat tracking is back!
			$this->db->query("UPDATE `logging` SET count=count+1 WHERE `page` = 'Signature'");
		}
		
		/* Catch Errors on Failure */
		catch(ConnectException $exception) {

			echo "Connection Error\n";
			var_dump($exception->getMessage());
		}

		catch(Exception $exception) {

			echo "Other Script Error\n";
			var_dump($exception->getMessage());
		}
		
		$this->user = $user;
		$this->filename = '/tmp/tmp.png';
		
		// Check to see if Image is already cached. If not, begin generation process.
		// Else, begin recall process.
		
		if(!$this->Cached()) {
	 		//echo "image is not cached";
			// Create the image (not cached)
			$this->create = new CreateImage($this->db,$this->exists,$this->user);
			 
			$this->create->createImage();
		}
	
		else {
			// Recall the image (cached and does not need to be updated).
			$this->displayImage($this->image);
			//echo "displaying image!";
		}	
	}
	
	// Create Image Function
	// Takes place of old MAKE file
		
	// Output the image to the browser
	function displayImage($data) {

	  //	echo "at display image <br />";
	  	header("Content-type: image/gif");
		echo $data;
		exit(0);
	}
	
	// Check if image is cached already - both whether or not the image was EVER cached
	// Or if image requires updating.
	function Cached() {
			
		$query = "select id,UNIX_TIMESTAMP(lastupdated) as lastupdated,username,imagedata from images where username='".$this->user."'";
		
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
		//echo $result->num_rows . "<br />";
		if($result->num_rows > 0) {
		 	//echo "exists??";
		 	$this->userdata = $result->fetch_assoc();
		 	//print_r($this->userdata);
		 	//print_r($result->fetch_fields());
		 	$result->close();
		 	
		 	//echo "current time: " . time() . "<br />";
		 	//$time = time();
		 	//$curtime = $time - $this->userdata['lastupdated'];
		 	
		 	
		 	//echo "<br />" . $curtime . "<br />";
			 //If current UNIXTIME minus LASTUPDATED UNIXTIME is greater than or equal to 21600 seconds
			// (6 hours), needs to be updated.

			if((time() - $this->userdata['lastupdated']) > 21600) {
				
				$this->exists = TRUE;
				
				return false;
			}				
			
			else {
			
			  	$this->image = $this->userdata['imagedata'];
			  	
			  	return true;
			}
		}
		
		else {
		
			$this->exists = FALSE;	
		
			return 0;
		}
	}
}
