<?php 
if ( isset ( $user_info['user_id'] ) ) {
	if ( $user_info['type'] == 10 || $user_info['type'] == 21 || $user_info['type'] == 31 || $user_info['type'] >= 80 ) {
		if (!isbanned($user_info['user_id'] )) {
			if (isset ($_POST['submit'])){
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
						}
						else {
							$ban = false;
						}
				   }
				   else{
						$ban=false;
						echo '<p><font color="red" size="+1">Please enter a ban time!</font></p>';
					}
					if ($user && $ban) { // If everything's OK.

						//is the user being banned higher than the one banning? [permission]
						$userpriv=mysql_query("SELECT * FROM users WHERE username=$user");

                        $ban_user = mysql_fetch_array($userpriv)){
                        if ( $user_info['type'] == 10 || $user_info['type'] == 21 || $user_info['type'] == 31 ) {
							if ( $ban_user['type'] == 10 || $ban_user['type'] == 21 || $ban_user['type'] == 31 || $ban_user['type'] >= 80 ) {
								print 'You may not ban this User.';
							}
							else {
								$query = "INSERT INTO comments_banned ( id, user_id, ip, whenbanned, banlength)
									VALUES ('', ".$ban_user['user_id'].", ".$ban_user['ip_address'].", NOW(), $ban)";
								$result = mysql_query($query);
							}
						}
						elseif ( $user_info['type'] == 80) {
							if ( $ban_user['type'] >= 80 ) {
								print 'You may not ban this User.';
							}
							else {
								$query = "INSERT INTO comments_banned ( id, user_id, ip, whenbanned, banlength)
									VALUES ('', ".$ban_user['user_id'].", ".$ban_user['ip_address'].", NOW(), $ban)";
								$result = mysql_query($query);
							}
						}
						elseif ( $user_info['type'] == 90) {
							if ( $ban_user['type'] >= 90 ) {
								print 'You may not ban this User.';
							}
							else {
								$query = "INSERT INTO comments_banned ( id, user_id, ip, whenbanned, banlength)
									VALUES ('', ".$ban_user['user_id'].", ".$ban_user['ip_address'].", NOW(), $ban)";
								$result = mysql_query($query);
							}
						}
						elseif ( $user_info['type'] == 98) {
							if ( $ban_user['type'] >= 98 ) {
								print 'You may not ban this User.';
							}
							else {
								$query = "INSERT INTO comments_banned ( id, user_id, ip, whenbanned, banlength)
									VALUES ('', ".$ban_user['user_id'].", ".$ban_user['ip_address'].", NOW(), $ban)";
								$result = mysql_query($query);
							}
						}
						elseif ( $user_info['type'] == 99) {
							$query = "INSERT INTO comments_banned ( id, user_id, ip, whenbanned, banlength)
								VALUES ('', ".$ban_user['user_id'].", ".$ban_user['ip_address'].", NOW(), $ban)";
							$result = mysql_query($query);
						}

						if ($result) {
							print "The user <b>$user</b> has been banned until <b>$ban</b> and he/she may not post a comment until that time.";
							log_entry ("message",'Banned the following user: #'.$user.'.',$user_info['username']);
						}
						else {
							print "Unable to process request, please try again. If problem persist please get in contact with flk or any other bleach7 coder";
							log_entry ("error",'Error occured while banning user: #'.$user_info['username'].'.');
						}
						//end of the permission on user banning check.
					}
					else { // If one of the data tests failed.
						echo '<p><font size="+1">Please try again.</font></p>';		
					}
				}
			}
			elseif (isset($_POST['unbanuser'])) { // Check if unbanuser was set
				// Check if it has elements
				if (sizeof($_POST['unbanuser']) > 0) {
					// Loop through each user
					foreach ($_POST['unbanuser'] as $user) {
						// Execute query
						$user = mysql_real_escape_string(stripslashes(htmlentities(trim($user))));

						// Don't need to do all that messy concatenation.. double quotes interpolate variables
						$query = "DELETE FROM comments_banned WHERE id = '$user'";
						$result = mysql_query($query);
						if ($result) {
							print "The user with the banID:'$user'. has been unbanned.";
						} else {
							print "An error occured while trying to unban the banID: '$user'";
						}
					}
				} else {
				// No users were selected
				print "You have not selected a user to unban.";
			  }
			}//end of submit
      	}else{ //if user is a banned mod.
            	print "<div align='centre'><b>You may not use this facility while banned.</b></div>";
		}

    }else{ //if user is not logged on
    	print "<div align='centre'><b>Only moderators and above may access this facility.</b></div>";
   	}
}else{ //if user is not logged on
	print "<div align='centre'><b>please log in to use this facility.</b></div>";
}
?>
