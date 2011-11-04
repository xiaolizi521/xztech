<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !isset ( $user_info['user_id'] ) ) {
header ( 'Location: ' . $site_path . '/login' );
ob_end_flush();
} 

$x = 1;

$limit = $user_B7->getPM_Limit();
?>
<script type="text/javascript">
function DeletePM() {
	if (confirm('Are you sure you want to delete this private message?')) {
		document.pm_form.submit();
		return true;
	}
	else {
	return false;
	}
}

function CheckAll(checkWhat) {
  // Find all the checkboxes...
  var inputs = document.getElementsByTagName("input");

  // Loop through all form elements (input tags)
  for(index = 0; index < inputs.length; index++)
  {
    // ...if it's the type of checkbox we're looking for, toggle its checked status
    if(inputs[index].id == checkWhat)
      if(inputs[index].checked == 0)
      {
        inputs[index].checked = 1;
      }
      else if(inputs[index].checked == 1)
      {
        inputs[index].checked = 0;
      }
  }
}
</script>
<table width='100%' cellpadding='0' cellspacing='0' class='main'>
	<tr>
		<td>
			<form name='pm_form' action="<?php echo $site_path, '/pm_inbox' ?>" method='post'>
<?php

##################################
##		List all PM's			##
##################################

if ( !isset ( $id ) && empty ( $id ) ) {
	$file_title = 'PM Inbox';
	$result = mysql_query( 'SELECT * FROM `pm` WHERE `sent_to` = \'' . $user_info['username'] . '\' ORDER BY `id` DESC' );
	$result_countunread = mysql_query( 'SELECT `status` FROM `pm` WHERE `sent_to` = \'' . $user_info['username'] . '\' AND NOT (`status` = \'1\') ORDER BY `id` DESC' );

	if ( mysql_num_rows ( $result ) > 0 ) {
		if ( mysql_num_rows ( $result ) > $limit ) {
			$delete_pm_select = mysql_query( 'SELECT * FROM `pm` WHERE `sent_to` = \'' . $user_info['username'] . '\' ORDER BY `id` ASC' );
			$delete = mysql_fetch_array( $delete_pm_select );
			$delete_pm = mysql_query ( 'DELETE FROM `pm` WHERE `id` = \'' . $delete['id'] . '\' AND `sent_to` = \'' . $user_info['username'] . '\'' );
			echo '
				<script type="text/javascript">
					alert( "You have exceeded the limit of ', $limit, ' messages, The oldest message will be deleted" )
				</script>
';
			header ( 'Location: ' . $site_path . '/pm_inbox' );
}
?>
				<table cellpadding="3" cellspacing="0" class="main" style="width: 100%; border-bottom: 1px solid #C3C3C3">
					<tr>
						<td style="width: 20px;"><a href="<?php echo $site_path, '/pm_compose' ?>"><img src="<?php echo $site_url, '/', $script_folder, '/images/pm/msg_new.gif' ?>" alt="Compose A PM" border="0" /></a></td>
						<td style="width: 1px;"><input type="checkbox" onClick="CheckAll('pm_delete')" /></td>
						<td style="width: 40%;"><span style="text-decoration:underline;"><b>Subject</b></span></td>
						<td style="width: 20%;"><span style="text-decoration:underline;"><b>From</b></span></td>
						<td><span style="text-decoration:underline;"><b>Date</b></span></td>
					</tr>
				</table>
<?php

			while ( $pm = mysql_fetch_array ( $result ) ) {
				$pm_subject = stripslashes ( $pm['subject'] );
				echo '				<table cellpadding="3" cellspacing="0" class="main" style="width: 100%; border-bottom: 1px solid #C3C3C3">
';
				if ( $user_info['dst'] == 1 ) {
					$pm_id = $pm['id'] + 3600;
				}
				else {
					$pm_id = $pm['id'];
				}
				$date = DisplayDate( $pm_id, 'M d Y, h:i A', '1' );
				echo '					<tr>
						<td style="width: 20px;">';
				if ( $pm[status] > 1 ) {
					echo '<img src="', $site_url, '/', $script_folder, '/images/pm/msg_new.gif" alt="Unread Message" />';
				}
				else {
					echo '<img src="', $site_url, '/', $script_folder, '/images/pm/msg_old.gif" alt="Read Message" />';
				} 
				echo '</td>
						<td style="width: 1px;"><input type="checkbox" name="pm_delete[]" value="', $pm['id'], '" /></td>
						<td style="width: 40%;">';
				if ( $pm[status] > 1 ) {
					echo '<b><a href="', $site_path, '/pm_inbox&amp;id=', $pm['id'], '">', $pm_subject, '</a></b>';
				}
				else {
					echo '<a href="', $site_path, '/pm_inbox&amp;id=', $pm['id'], '">', $pm_subject, '</a>';
				}
				echo '</td>
						<td style="width: 20%;"><b><a href="', $site_path, '/member&amp;id=', $pm['sent_by'], '">', $pm['sent_by'], '</a></b></td>
						<td>', $date, '</td>
					</tr>
';
				$x++;
				echo '				</table>
';
			}
		}
		else {
			echo '				<table cellpadding="3" cellspacing="0" class="main" style="border-bottom: 1px solid #666666; border-top: 1px solid #666666; width: 100%;">
						<tr>
							<td align="center"><b>You have no messages in your inbox</b></td>
						</tr>
					</table>';
		}
		
		// Delete the selected PM's
		if ( isset ( $_POST[submit] ) ) {
			for ( $i = 0; $i <= ( count ( $pm_delete ) - 1 ); $i++ ) {
			$delete_pm = mysql_query ( 'DELETE FROM `pm` WHERE id=\'' . $pm_delete[$i] . '\' AND `sent_to` = \'' . $user_info['username'] . '\'' );
		}
		header ( 'Location: ' . $site_path . '/pm_inbox' );
	}
?>
<table cellpadding="3" cellspacing="0" class="main" style="width: 100%;">
	<tr>
		<td style="height: 5px;"></td>
	</tr>
	<tr>
		<td align="center"><?php echo '<b>' . mysql_num_rows ( $result ) . '</b> Message(s), <b>' . mysql_num_rows ( $result_countunread ) . '</b> Unread out of a maximum <b>' . $limit . '</b>' ?></td>
	</tr>
	<tr>
		<td style="text-align: center;">
			<input type="submit" name="submit" value="Delete PM(s)" class="form" />
			<input type="button" value="Compose PM" class="form" onclick="document.location='<?php echo $site_path, '/pm_compose' ?>'" /></td>
	</tr>
</table>
<?php
}

##########################################
##		Display the requested PM		##
##########################################

else {
	$file_title = 'PM Message';
	$id = mysql_real_escape_string ( $_GET['id'] );

	$result = mysql_query( 'SELECT `pm`.*, `users`.* FROM `pm` LEFT JOIN `users` ON (`pm`.`sent_by` = `users`.`username`) WHERE `pm`.`id` = \'' . $id . '\' AND `pm`.`sent_to` = \'' . $user_info['username'] . '\'' );
	$pm = mysql_fetch_array ( $result );
	$pm_User = new B7_User ( $pm );

	if ( isset ( $_POST['pm_delete_submit'] ) ) {
		$delete_pm = mysql_query ( 'DELETE FROM `pm` WHERE id=\'' . $id . '\' AND `sent_to` = \'' . $user_info['username'] . '\'' );
		header ( 'Location: ' . $site_path . '/pm_inbox' );
	}

	if ( mysql_num_rows ( $result ) == 0 ) {
		echo '<p style="text-align: center;"><b>There are no messages with that ID</b></p>';
	}
	else {
		if ( $pm['status'] > 1 ) {
			$result_changestatus = mysql_query ( 'UPDATE `pm` SET `status` = \'1\' WHERE `id` = \'' . $id . '\' AND `sent_to` = \'' . $user_info['username'] . '\'' );
		} 
		if ( $handle = opendir ( $script_folder . '/images/smilies' ) ) {
			while ( false !== ( $file = readdir ( $handle ) ) ) { 
				if ( $file != '.' && $file != '..' && ereg ( '.gif', $file ) ) { 
					$smile_name = str_replace ( '.gif', '', $file );
					$smilies_array[] = $smile_name;
				} 
			}
			closedir( $handle ); 
		}
		$member_username = '<a href="' . $site_path . '/member&amp;id=' . $pm['username'] . '"><span style="text-decoration: underline;"><b>' . $pm['username'] . '</b></span></a>';
		if ( $user_info['dst'] == 1 ) {
			$pm_id = $pm['id'] + 3600;
		}
		else {
			$pm_id = $pm['id'];
		}
		$pm_date = DisplayDate( $pm_id, 'M d Y, h:i A', '1' );
		$pm_options = '<a href="' . $site_path . '/pm_compose&amp;to=' . $pm['sent_by'] . '&amp;reply=' . $pm['id'] . '">Reply</a> | <a href="#deletepm" onclick="DeletePM()">Delete</a>';
		if ( empty ( $pm['avatar'] ) ) {
			$member_avatar = '<img src="' . $site_url . '/' . $script_folder . '/images/avatars/none.gif" alt="none" style="width: 60px; height: 60px" />';
		}
		else {
			list ( $avatar_width, $avatar_height ) = getimagesize ( $pm['avatar'] );
			if ( $avatar_width > 60 || $avatar_height > 60 ) {
				$member_avatar = '<img src="' . $pm['avatar'] . '" alt="' . $pm['username'] . '" style="width: 60px; height: 60px" />';
			}
			else {
				$member_avatar = '<img src="' . $pm['avatar'] . '" alt="' . $pm['username'] . '" />';
			}
		} 
		$member_type = $pm_User->getTitle();
		$joindate = DisplayDate( $pm['registered_on'], 'm/d/y', '0' );
		$member_joindate = 'Joined: ' . $joindate;
		$member_posts = 'Posts: ' . $pm['posts'];
		$member_num = 'Member No. ' . $pm['user_id'];
		if ( ( time() - $pm[last_activity_time] ) <= 300 ) {
			$member_online = 'Status: <span style="color: green;">Online</span>';
		}
		else {
			$member_online = 'Status: <span style="color: red;">Offline</span>';
		}
		$pm_subject = 'Subject: ' . stripslashes ( $pm['subject'] );
		$pm_message = stripslashes ( nl2br ( $pm[message] ) );
		$pm_message = ParseMessage ( $pm_message );

		include ( 'templates/pm.php' );

		echo '				<input type="hidden" name="pm_delete_submit" />
';
	}
	echo '				<p style="text-align: center;">
					<input type="button" value="Back To Inbox" onclick="document.location\'' . $site_path . '/pm_inbox\'" class="form" />
					<input type="button" value="Compose PM" onclick="document.location=\'' . $site_path . '/pm_compose\'" class="form" /></p>
';
}
echo '			</form>
		</td>
	</tr>
</table>
';
?>