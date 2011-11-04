<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( isset ( $user_info['user_id'] ) ) {
	$result_pm_count_cp = mysql_query ( "SELECT sent_to FROM pm WHERE sent_to='$user_info[username]'" );
	$pm_count_cp = mysql_num_rows ( $result_pm_count_cp );
	if ( !ereg ( "/pm_", $GLOBALS['QUERY_STRING'] ) ) {
		$result_pm_status = mysql_query ( "SELECT * FROM pm WHERE sent_to='$user_info[username]' AND status='3'" );
		$pm_count_status = mysql_num_rows ( $result_pm_status );
		while ( $pm_status = mysql_fetch_array ( $result_pm_status ) ) {
			if ( $pm_count_status > 0 ) {
				$pm_status_date = DisplayDate( "$pm_status[id]", "M d Y, h:i A", "0" );
				echo "<script>alert('You have recieved a new PM from $pm_status[sent_by] on $pm_status_date')</script>";
				$update_pm_status_recieved = mysql_query ( "UPDATE pm SET status='2' WHERE id='$pm_status[id]' AND status='3'" );
			}
		}
	}

	echo "					<tr>
";
	if ( $userID_info['view_admin'] == 1 ) {
		echo "						<td><a href=\"$site_url/$script_folder/admin/index.php\" target=\"_blank\">Admin Control Panel</a></td>
";
	}
	if ( $userID_info['can_ban'] == 1 ) {
		echo "						<td><a href=\"?page=member/bancomments\">Ban/Unban</a></td>
						<td><a href=\"?page=member/viewlog\">View Log</a></td>
";
	}
	if ( $userID_info['view_admin'] == 1 || $userID_info['can_ban'] == 1 ) {
		echo "					</tr>
					<tr>
";
	}
	echo "						<td><a href=\"?page=member/usercp&amp;do=editprofile\">Edit Profile</a></td>
						<td><a href=\"?page=member/pm_inbox\">PM Inbox ($pm_count_cp)</a></td>
						<td><a href=\"?page=member/logout\">Logout</a></td>
					</tr>
";
}
?>