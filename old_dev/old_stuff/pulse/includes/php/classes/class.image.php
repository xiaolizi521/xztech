<?php

/*	

Name: Image
Purpose: Image Generating Functionality.
Uses:

1) Recalls Image from Storage and Outputs Appropriately
2) Generates Image Static Layers for Later Use
3) Generates Final Image from Combining of Layers and Dynamic Data
4) Stores Images and various Image Information in specific areas.

*/

class BgFail extends Exception {}
class ImgException extends Exception {}

class Image {
	
	function __construct($user = FALSE, $idType, $var) {
		
		$this->db = new DB(DB_HOST,DB_USER,DB_PASS,DB_NAME);
		
		/* We need to get the user Info first. How are we going to do this?
		** Note: This is only when we are generating information based on a user.

		/* User Info Needed at Minimum:
			UUID/Name
		*/
		
			if(!$user):
				
				switch ($idType):
				
					case(ID):
					
						$this->user = new User(USER_DEF_ID,$var);
						break;
					
					case(NAME):
						
						$this->user = new User(USER_DEF_UUID,User::_genID($var));
						break;
					
					case(UUID):
					
						$this->user = new User(USER_DEF_UUID, $var);
						break;
						
					default:
					
						$this->user = new User(USER_DEF_UUID, $var);
						break;
				
				endswitch;
				
			else:
			
				$this->user = $user;
				
			endif;
			
	}
	
	function __destruct() {
		
		$this->db->close();
	}
	
	
	function destroy() {
		
		$this->__destruct();
	}
	
	function LoadBG($static = FALSE) {
		
		/* This needs to load the image by directory and UUID */
		
		if($static):
		
			
		
		else:
		
			$tmpFileName = PATH_DEF_REAL . "users/" . $this->user->uuid . "/" {BGNAMEVARIABLE};
		
		endif;
	}
	
	function StoreBG() {
		
		/* Same as above */
		
	}
	
	function LoadFields() {
		
		
	}
	
	function StoreFields() {
		
	}
	
	function CreateData() {
		
	}
	
	function MergeLayers() {
		
	}
	
	function StoreImage() {
		
	}
	
	function RecallImage() {
		
	}
	
	function Display() {
		
	}
	
}

?>