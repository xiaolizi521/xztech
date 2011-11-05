<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( isset ( $user_info['user_id'] ) ) {
	$result_pm_count_cp = mysql_query ( 'SELECT `sent_to` FROM `pm` WHERE `sent_to` = \'' . mysql_real_escape_string ( $user_info['username'] ) . '\'' );
	$pm_count_cp = mysql_num_rows ( $result_pm_count_cp );
	if ( 1==1 ) {
		$result_pm_status = mysql_query ( 'SELECT * FROM `pm` WHERE `sent_to` = \'' . mysql_real_escape_string ( $user_info['username'] ) . '\' AND `status` = \'3\'' );
		$pm_count_status = mysql_num_rows ( $result_pm_status );
		while ( $pm_status = mysql_fetch_array ( $result_pm_status ) ) {
			if ( $pm_count_status > 0 ) {
				$pm_status_date = DisplayDate( $pm_status['id'], 'M d Y, h:i A', '0' );
				echo '			<script type="text/javascript">
				alert ( "You have recieved a new PM from ',  $pm_status['sent_by'], ' on ', $pm_status_date, '" )
			</script>
';
				$update_pm_status_recieved = mysql_query ( 'UPDATE `pm` SET `status` = \'2\' WHERE id=\'' . mysql_real_escape_string ( $pm_status['id'] ) . '\' AND `status` = \'3\'' );
			}
		}
	}

?>
			<div id="member_sec" class="pos">
				<table cellpadding="0" cellspacing="0" id="Member_Sec">
<?php
	echo "					<tr>
";
	if ( $user_B7->getView_Admin () == true ) {
		echo '						<td><a href="', $site_url, '/', $script_folder, '/admin/index.php" target="_blank">Admin Control Panel</a></td>
';
		$view_admin = true;
	}
	else
	{
		$view_admin = false;
	}

	if ( $user_B7->getCan_Ban () == true ) {
		echo '						<td><a href="?page=member/bancomments">Ban/Unban</a></td>
						<td><a href="?page=member/viewlog">View Log</a></td>
';
		$can_ban = true;
	}
	else
	{
		$can_ban = false;
	}
	
	
	if ( $view_admin == true || $can_ban == true ) {
		echo '					</tr>
					<tr>
';
	}
	echo '						<td><a href="?page=member/usercp&amp;do=editprofile">Edit Profile</a></td>
						<td><a href="?page=member/pm_inbox">PM Inbox (', $pm_count_cp, ')</a></td>
						<td><a href="?page=member/logout">Logout</a></td>
					</tr>
';
}
?>
				</table>
			</div>