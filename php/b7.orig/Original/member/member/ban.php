<?php 
if ( isset ( $user_info['user_id'] ) ) {
	if ( $user_info['type'] == 10 || $user_info['type'] == 21 || $user_info['type'] == 31 || $user_info['type'] >= 80 ) {
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
			}else {
				$ban=false;
				echo '<p><font color="red" size="+1">Please enter a ban time!</font></p>';
			}
			
			if (strlen($_POST['reason']) > 0) {
				$reason= stripslashes(htmlentities(trim($_POST['reason'])));
			}
			else {
				$reason=false;
				echo '<p><font color="red" size="+1">Please enter a valid reason!</font></p>';
			}
			if ($user && $ban && $reason) { // If everything's OK.
				$ban_user = getuserdetail($user);

				if ( $user_info['username'] == $ban_user['username'] ) {
					print "You cannot ban yourself.";
				}
				elseif ( $user_info['type'] == 10 || $user_info['type'] == 21 || $user_info['type'] == 31 ) {
					if ( $ban_user ['type'] == 10 || $ban_user ['type'] == 21 || $ban_user ['type'] == 31 ) {
						print "The user is a fellow mod.  You cannot ban them.<br />";
					}
					elseif ( 80 <= $ban_user ['type'] && $ban_user ['type'] <= 99 ) {
						print "The user is either a Staff Member or an Admin.  You cannot ban them.<br />";
					}
					else {
						$query = "INSERT INTO `comments_banned` ( `id` , `user_id` , `ip` ,  `whenbanned` , `banlength` )
							VALUES ('', '".$ban_user['user_id']."', '".$ban_user['ip_address']."',  NOW(), '$ban')";
						$result= mysql_query($query);
					}
				}
				elseif ( 80 <= $user_info['type'] && $user_info['type'] <= 89 ) {
					if ( 80 <= $ban_user ['type'] && $ban_user ['type'] <= 99 ) {
						print "The user is either a fellow Staff Member or an Admin.  You cannot ban them.<br />";
					}
					else {
						$query = "INSERT INTO `comments_banned` ( `id` , `user_id` , `ip` ,  `whenbanned` , `banlength` )
							VALUES ('', '".$ban_user['user_id']."', '".$ban_user['ip_address']."',  NOW(), '$ban')";
						$result= mysql_query($query);
					}
				}
				elseif ( 90 <= $user_info['type'] && $user_info['type'] <= 97 ) {
					if ( 90 <= $ban_user ['type'] && $ban_user ['type'] <= 99 ) {
						print "The user is a fellow Admin.  You cannot ban them.<br />";
					}
					else {
						$query = "INSERT INTO `comments_banned` ( `id` , `user_id` , `ip` ,  `whenbanned` , `banlength` )
							VALUES ('', '".$ban_user['user_id']."', '".$ban_user['ip_address']."',  NOW(), '$ban')";
						$result= mysql_query($query);
					}
				}
				elseif ( $user_info['type'] == 98) {
					if ( $ban_user ['type'] == 99 ) {
						print "The user is the Owner of the site.  You cannot ban them.<br />";
					}
					else {
						$query = "INSERT INTO `comments_banned` ( `id` , `user_id` , `ip` ,  `whenbanned` , `banlength` )
							VALUES ('', '".$ban_user['user_id']."', '".$ban_user['ip_address']."',  NOW(), '$ban')";
						$result= mysql_query($query);
					}
				}
				elseif ( $user_info['type'] == 99 ) {
					$query = "INSERT INTO `comments_banned` ( `id` , `user_id` , `ip` ,  `whenbanned` , `banlength` )
						VALUES ('', '".$ban_user['user_id']."', '".$ban_user['ip_address']."',  NOW(), '$ban')";
					$result= mysql_query($query);
				}

				if ($result) {
					$nid = time();
					$ban_reason = "You have been banned until $ban, because: $reason";
					print "The user <b>$user</b> has been banned until <b>$ban</b> and he/she may not post a comment until that time.";
					log_entry ("message",'Banned the following user: #'.$user.'. <br />The reason: '.$reason.'.',$user_info['username']);
					$query_PM = "INSERT INTO pm ( id, sent_by, sent_to, subject, message, status)
									VALUES ( '$nid', '".$user_info['username']."', '".$ban_user['username']."', 'Banned', '$ban_reason', '3')";
					mysql_query($query_PM);
				} else {
					print "Unable to process request, please try again. If problem persist please get in contact with flk or any other bleach7 coder";
					log_entry ("error",'Error occured while banning user: #'.$user_info['username'].'.',$user_info['username']);
				}

			} else { // If one of the data tests failed.
				echo '<p><font size="+1">Please try again.</font></p>';		
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
						print "The user with the banID:'$user'. has been unbanned.<br />";
					} else {
						print "An error occured while trying to unban the banID: '$user'";
					}
				}
			} else {
				// No users were selected
				print "You have not selected a user to unban.";
			}
		}//end of submit

	}else{ //if user is not logged on
		print "Only moderators and above may access this facility.";
	}
}else{ //if user is not logged on
 print "please log in to use this facility";
}
?>
