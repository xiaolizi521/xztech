<?php
	$result_member = mysql_query ( "SELECT * FROM users WHERE username='$username'" );
		if ( mysql_num_rows ( $result_member ) > 0 ) {
			$member = mysql_fetch_array ( $result_member );
			echo "<table cellpadding='5' cellspacing='0' border='0' class='main'>
				<tr>
					<td>Username</td>
					<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $member[username] )."' class='form' readonly='true'></td>
				</tr>
				<tr>
					<td>Status</td>";
			if ( $user_info['type'] == "90" || $user_info['type'] == "91" ) {
				if ( $member['type'] == "98" ) {
					echo "<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $rank98 )."' class='form' readonly='true'></td>
					</tr>
					<tr>
						<td>Posts</td>
						<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form' readonly='true'></td>
					</tr>";
				}elseif ( $member['type'] == "99" ) {
					echo "<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $rank99 )."' class='form' readonly='true'></td>
					</tr>
					<tr>
						<td>Posts</td>
						<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form' readonly='true'></td>
					</tr>";
				}else {
					echo "<td><select name='member_type' class='form'>";
?>
<option value="1" <?php if ( $member['type'] == "1" ) { echo "selected"; } ?>>Member</option>
<option value="2" <?php if ( $member['type'] == "2" ) { echo "selected"; } ?>>Privileged Member</option>
<option value="10" <?php if ( $member['type'] == "10" ) { echo "selected"; } ?>>Moderator</option>
<option value="20" <?php if ( $member['type'] == "20" ) { echo "selected"; } ?>>Info Team</option>
<option value="21" <?php if ( $member['type'] == "21" ) { echo "selected"; } ?>>Info Team | Mod</option>
<option value="30" <?php if ( $member['type'] == "30" ) { echo "selected"; } ?>>M7 Team</option>
<option value="31" <?php if ( $member['type'] == "31" ) { echo "selected"; } ?>>M7 Team | Mod</option>
<option value="80" <?php if ( $member['type'] == "80" ) { echo "selected"; } ?>>Staff Member</option>
<option value="81" <?php if ( $member['type'] == "81" ) { echo "selected"; } ?>>Staff | M7</option>
<option value="90" <?php if ( $member['type'] == "90" ) { echo "selected"; } ?>>Administrator</option>
<option value="91" <?php if ( $member['type'] == "91" ) { echo "selected"; } ?>>Admin | M7</option>
<?php
					echo "</select></td>
				</tr>
				<tr>
					<td>Posts</td>
					<td><input type='text' name='member_posts' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form'></td>
				</tr>";
				}
			}elseif ( $user_info['type'] == "98" ) {
				if ( $member['type'] == "99" ) {
					echo "<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $rank99 )."' class='form' readonly='true'></td>
					</tr>
					<tr>
						<td>Posts</td>
						<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form' readonly='true'></td>
					</tr>";
				}else {
					echo "<td><select name='member_type' class='form'>";
?>
<option value="1" <?php if ( $member['type'] == "1" ) { echo "selected"; } ?>>Member</option>
<option value="2" <?php if ( $member['type'] == "2" ) { echo "selected"; } ?>>Privileged Member</option>
<option value="10" <?php if ( $member['type'] == "10" ) { echo "selected"; } ?>>Moderator</option>
<option value="20" <?php if ( $member['type'] == "20" ) { echo "selected"; } ?>>Info Team</option>
<option value="21" <?php if ( $member['type'] == "21" ) { echo "selected"; } ?>>Info Team | Mod</option>
<option value="30" <?php if ( $member['type'] == "30" ) { echo "selected"; } ?>>M7 Team</option>
<option value="31" <?php if ( $member['type'] == "31" ) { echo "selected"; } ?>>M7 Team | Mod</option>
<option value="80" <?php if ( $member['type'] == "80" ) { echo "selected"; } ?>>Staff Member</option>
<option value="81" <?php if ( $member['type'] == "81" ) { echo "selected"; } ?>>Staff | M7</option>
<option value="90" <?php if ( $member['type'] == "90" ) { echo "selected"; } ?>>Administrator</option>
<option value="91" <?php if ( $member['type'] == "91" ) { echo "selected"; } ?>>Admin | M7</option>
<option value="98" <?php if ( $member['type'] == "98" ) { echo "selected"; } ?>>Webmaster</option>
<?php
					echo "</select></td>
				</tr>
				<tr>
					<td>Posts</td>
					<td><input type='text' name='member_posts' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form'></td>
				</tr>";
				}
			}elseif ( $user_info['type'] == "99" ) {
				echo "<td><select name='member_type' class='form'>";
?>
<option value="1" <?php if ( $member['type'] == "1" ) { echo "selected"; } ?>>Member</option>
<option value="2" <?php if ( $member['type'] == "2" ) { echo "selected"; } ?>>Privileged Member</option>
<option value="10" <?php if ( $member['type'] == "10" ) { echo "selected"; } ?>>Moderator</option>
<option value="20" <?php if ( $member['type'] == "20" ) { echo "selected"; } ?>>Info Team</option>
<option value="21" <?php if ( $member['type'] == "21" ) { echo "selected"; } ?>>Info Team | Mod</option>
<option value="30" <?php if ( $member['type'] == "30" ) { echo "selected"; } ?>>M7 Team</option>
<option value="31" <?php if ( $member['type'] == "31" ) { echo "selected"; } ?>>M7 Team | Mod</option>
<option value="80" <?php if ( $member['type'] == "80" ) { echo "selected"; } ?>>Staff Member</option>
<option value="81" <?php if ( $member['type'] == "81" ) { echo "selected"; } ?>>Staff | M7</option>
<option value="90" <?php if ( $member['type'] == "90" ) { echo "selected"; } ?>>Administrator</option>
<option value="91" <?php if ( $member['type'] == "91" ) { echo "selected"; } ?>>Admin | M7</option>
<option value="98" <?php if ( $member['type'] == "98" ) { echo "selected"; } ?>>Webmaster</option>
<option value="99" <?php if ( $member['type'] == "99" ) { echo "selected"; } ?>>Sensei</option>
<?php
				echo "</select></td>
				</tr>
				<tr>
					<td>Posts</td>
					<td><input type='text' name='member_posts' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form'></td>
				</tr>";
			}
				echo "<tr>
					<td>Registered</td>
					<td>".DisplayDate( "$member[registered_on]", "F d Y, h:i A", "1" )."</td>
				</tr>
				<tr>
					<td>IP Address</td>
					<td>$member[ip_address]</td>
				</tr>
			</table>
			<table style='height: 5px' cellpadding='0' cellspacing='0'>
				<tr>
					<td></td>
				</tr>
			</table>
			<input type='submit' name='member_edit' value='Edit Member' class='form'>   <input type='button' value='New Search' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=members\"'>
			";
		} else {
			echo "There are no members in the database with that username<br />
				<a href='$PHP_SELF?view=main&amp;type=members'>Go back</a>";
		}
?>