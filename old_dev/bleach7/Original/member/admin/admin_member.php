<?php
	$username = mysql_real_escape_string ( $_GET[username] );
	echo "<form method='post' name='form_member'>";
	if ( isset ( $_POST[member_edit] ) ) {
		$member_username = mysql_real_escape_string ( $_POST[member_username] );
		$member_posts = mysql_real_escape_string ( $_POST[member_posts] );
		$update_member = mysql_query ( "UPDATE users SET username='$member_username', type='$member_type', posts='$member_posts' WHERE username='$username'" );
		echo "<script>alert('Member: $username has been successfully editted')</script>";
		echo "<script>document.location='$PHP_SELF?view=main&amp;type=members&username=$username'</script>";
	}

	if ( isset ( $_POST[member_delete] ) ) {
		$member_delete = mysql_query( "DELETE FROM users WHERE username='$username'" );;
		echo "<script>alert('Member: $username has been successfully deleted')</script>";
		echo "<script>document.location='$PHP_SELF?view=main&amp;type=members'</script>";
	}

	if ( isset ( $_POST[member_search] ) ) {
		$member_username = mysql_real_escape_string ( $_POST[member_username] );
		if ( empty ( $member_username ) ) {
			echo "Please enter a member's username";
		} else {
			$result_search = mysql_query ( "SELECT * FROM users WHERE username LIKE '%$member_username%' ORDER BY username" );
			if ( mysql_num_rows ( $result_search ) > 0 ) {
				echo "<table cellpadding='5' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 300px;'>
				";
				while ( $search = mysql_fetch_array ( $result_search ) ) {
					echo "	<tr>
							<td align='left' style='border-bottom: 1px solid #C3C3C3'>$search[username]</td><td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='$PHP_SELF?view=main&amp;type=members&amp;username=$search[username]'>Edit</a></td>
						</tr>
						";
				}
				echo "</table>
				";
			} else {
				echo "There are no members in the database with that username";
			}
		}
	}

	if ( isset ( $username ) && !empty ( $username ) ) {
		include ( "admin_member_select.php" );
	} else {
		include ( "admin_member_search.php" );
	}
	echo "</form>";

?>