<?php
	$conn = pg_connect("host=127.0.0.1 dbname=kickstart user=kickstart password=l33tNix");

			$id = $_POST['id'];		
		
         $prn = $_POST['private_network'];
         
         $pub = $_POST['public_network'];
         
			$pattern = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}.\d{1,2}$/';
			
			$add = "INSERT INTO vlans VALUES ( '$id' , '$pub' , '$prn' )";

      $conf = "/exports/kickstart/bin/vlan_restart";

	if
	 
		(($id == "") || ($prn == "") || ($pub == "")) {
			die("Please enter in all fields! Hit the back button.");
		}
		
	else if 
	
		((preg_match($pattern,$prn) > 0) && (preg_match($pattern, $pub) > 0)){
			pg_query($conn, $add);
      exec($conf);
			header( 'Location: added.html' );
		}
				
	else 

		(
		print "Please enter in correct cidr notation such as 10.0.0.0/24, hit the back button and try again."
		
		)
				
?>
