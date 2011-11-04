<?php

		if ( isset ( $id ) && !empty ( $id ) ) { //editting news with an item selected
				$m_id = mysql_real_escape_string ( $_GET['id'] );
				$result_edit_id = mysql_query( "SELECT * FROM `admin_message` WHERE `id` = $m_id" );
				$edit_id = mysql_fetch_array( $result_edit_id );
				if ( mysql_num_rows ( $result_edit_id ) > 0 ) { //a valid news id is found
					if ( count ( $errors ) > 1 ) {
						echo "<table cellpadding='0' cellspacing='0' class='main'>
							<tr>
								<td>";
						DisplayErrors();
						echo "</td>
							</tr>
						</table>
						";
					}
					echo "<table cellpadding='5' cellspacing='0' border='0' class='main'>
	<tr>
		<td>Headline</td>
		<td><input type='text' name='message_headline' style='width: 420px' value='".stripslashes ( $edit_id['headline'] )."' class='form'></td>
	</tr>
	<tr>
		<td>Available To</td>
		<td><select name='rank_available' class='form'>
		";
			switch ( $user_info['type'] ) {
				case 20:
				case 21:
					echo "<option value='0' selected>All users</option>";
					break;
				case 30:
				case 31:
					switch ( $edit_id['rank'] ) {
						case 0:
							echo "<option value='0' selected>All users</option>
							<option value='1'>Manga7 or Higher</option>";
							break;
						case 1:
							echo "<option value='0'>All users</option>
							<option value='1' selected>Manga7 or Higher</option>";
							break;
					}
					break;
				case 80:
				case 81:
					switch ( $edit_id['rank'] ) {
						case 0:
							echo "<option value='0' selected>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>";
							break;
						case 1:
							echo "<option value='0'>All users</option>
							<option value='1' selected>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>";
							break;
						case 1:
							echo "<option value='0'>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2' selected>Staff or Higher</option>";
							break;
					}
					break;
				case 90:
				case 91:
					switch ( $edit_id['rank'] ) {
						case 0:
							echo "<option value='0' selected>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>
							<option value='3'>Admin or Higher</option>";
							break;
						case 1:
							echo "<option value='0'>All users</option>
							<option value='1' selected>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>
							<option value='3'>Admin or Higher</option>";
							break;
						case 2:
							echo "<option value='0'>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2' selected>Staff or Higher</option>
							<option value='3'>Admin or Higher</option>";
							break;
						case 3:
							echo "<option value='0'>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>
							<option value='3' selected>Admin or Higher</option>";
							break;
					}
					break;
				case 98:
				case 99:
					switch ( $edit_id['rank'] ) {
						case 0:
							echo "<option value='0' selected>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>
							<option value='3'>Admin or Higher</option>
							<option value='4'>Webmaster/Owner Only</option>";
							break;
						case 1:
							echo "<option value='0'>All users</option>
							<option value='1' selected>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>
							<option value='3'>Admin or Higher</option>
							<option value='4'>Webmaster/Owner Only</option>";
							break;
						case 2:
							echo "<option value='0'>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2' selected>Staff or Higher</option>
							<option value='3'>Admin or Higher</option>
							<option value='4'>Webmaster/Owner Only</option>";
							break;
						case 3:
							echo "<option value='0'>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>
							<option value='3' selected>Admin or Higher</option>
							<option value='4'>Webmaster/Owner Only</option>";
							break;
						case 4:
							echo "<option value='0'>All users</option>
							<option value='1'>Manga7 or Higher</option>
							<option value='2'>Staff or Higher</option>
							<option value='3'>Admin or Higher</option>
							<option value='4' selected>Webmaster/Owner Only</option>";
							break;
					}
					break;
			}
			echo "</select></td>
	</tr>
	<tr>
		<td valign='top'>Message Post</td>
		<td><textarea name='message_message' style='width: 420px; height: 320px' class='form'>".stripslashes ( $edit_id['message'] )."</textarea></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'>	   <input type='hidden' name='id' value='$id'>
<input type='submit' name='message_edit' value='Edit message' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_messages.reset()'></td>
	</tr>";
echo "</table>";
				} else { //no valid news id found
					echo "<p align='center'><b>Invalid Message ID</b></p>";
				}
			}
?>
