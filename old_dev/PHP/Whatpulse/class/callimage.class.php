<?php
// Set the browser to expect a PNG Image
require "class.wsidb.php";

class BgException extends Exception {}
class ImgException extends Exception {}

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
	function __construct() {
	  	
		try {

			$this->db = new wsiDB('localhost', 'offbea2_whatpuls', 'pulsestats', 'offbea2_pulsestats');
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
		
		$this->user = $_GET['user'];
		$this->filename = '/tmp/tmp.png';
		
		// Check to see if Image is already cached. If not, begin generation process.
		// Else, begin recall process.
		
		if(!$this->Cached()) {
	 
			// Create the image (not cached)
			  $this->createImage();
		}
	
		else {
			// Recall the image (cached and does not need to be updated).
			$this->displayImage();
		}	
	}
	
	// Create Image Function
	// Takes place of old MAKE file
		
	// Output the image to the browser
	function displayImage() {

	  //	echo "at display image <br />";
		echo $this->image;
		exit(0);
	}
	
	// Check if image is cached already - both whether or not the image was EVER cached
	// Or if image requires updating.
	function Cached() {
			
		$query = "select id,UNIX_TIMESTAMP(lastupdated),username,imagedata from images where username='".$this->user."'";
		
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
		if($result->num_rows() > 0) {
		 	
		 	$this->userdata = $result->fetch_assoc();
		 	$result->close();
		 	
			// If current UNIXTIME minus LASTUPDATED UNIXTIME is greater than or equal to 21600 seconds
			// (6 hours), needs to be updated.

			if((time() - $this->userdata['lastupdated']) > 21600) {
				
				$this->exists = TRUE;
				
				return 0;
			}				
			
			else {
			
			  	$this->image = $this->userdata['imagedata'];
			  	
			  	return 1;
			}
		}
		
		else {
		
			$this->exists = FALSE;	
		
			return 0;
		}
	}
}
