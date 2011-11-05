<?php 
if ( isset ( $user_info['user_id'] ) ) {
	if ( $user_info['type'] >= 3 ) {
		$user = mysql_real_escape_string ( $_GET['user'] );
		
		
		if (isset($_POST['submit'])) { // Handle the form.
				if (strlen($_POST['username']) > 0) {
					$user= stripslashes(htmlentities(trim($_POST['username'])))	;
				   }else{
					 $user=false;
					 echo '<p><font color="red" size="+1">Please enter a valid location!</font></p>';
					}
				if (strlen($_POST['banlength']) > 0) {
					$ban= stripslashes(htmlentities(trim($_POST['banlength'])));
						
							if (ereg ("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $ban, $regs)) {
									 $ban= "$regs[3]-$regs[1]-$regs[2] $regs[4]:$regs[5]:$regs[6]";
									} else {
									 $ban =false;
									}
				   }else{
					 $ban=false;
					 echo '<p><font color="red" size="+1">Please enter a ban time!</font></p>';
					}
						if ($user && $ban) { // If everything's OK.
											$details=getuserdetail($user);
							
											$query = "INSERT INTO `comments_banned` ( `id` , `user_id` , `ip` ,  `whenbanned` , `banlength` )
																	 VALUES ('', '".$details['user_id']."', '".$details['ip_address']."',  NOW(), '$ban')";
											$result= mysql_query($query);
							
												if ($result)
												{
												print "The user <b>$user</b> has been banned until <b>$ban</b> and he/she may not post a comment until that time.";
												log_entry ("message",'Banned the following user: #'.$user.'.',$user_info['username']);
												
												}else{
												
												print "Unable to process request, please try again. If problem persist please get in contact with flk or any other bleach7 coder";
												log_entry ("error",'Error occured while banning user: #'.$user_info['username'].'.',$user_info['username']);
												}
		 
									
					  } else { // If one of the data tests failed.
						echo '<p><font size="+1">Please try again.</font></p>';		
					  }
		
		}
		
	}else{ //if user is not logged on
	print "Only moderators and above may access this facility.";
	}

}else{ //if user is not logged on
 print "please log in to use this facility";
}
?>
