<?php
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

			echo "<table cellpadding='5' cellspacing='0' class='main'>
			";
			echo "	<tr>
		<td>Headline</td>
		<td><input type='text' name='message_headline' value='' style='width: 420px' class='form'></td>
	</tr>
	<tr>
		<td>Available To</td>
		<td><select name='rank_available' class='form'>
		";
		$usertype = $user_info['type'];
		if($usertype == 20 || $usertype == 21)
		{
					echo "<option value='0' selected>All users</option>";
		}
		elseif($usertype == 30 || $usertype == 31)
		{
					echo "<option value='0' selected>All users</option>
					<option value='1'>Manga7 or Higher</option>";
		}
		elseif($usertype == 80 || $usertype == 81)
		{
					echo "<option value='0' selected>All users</option>
					<option value='1'>Manga7 or Higher</option>
					<option value='2'>Staff or Higher</option>";
		}			
		elseif($usertype == 90 || $usertype == 91)
		{
					echo "<option value='0' selected>All users</option>
					<option value='1'>Manga7 or Higher</option>
					<option value='2'>Staff or Higher</option>
					<option value='3'>Admin or Higher</option>";
		}
		elseif($usertype == 99 || $usertype == 98)
			{
					echo "<option value='0' selected>All users</option>
					<option value='1'>Manga7 or Higher</option>
					<option value='2'>Staff or Higher</option>
					<option value='3'>Admin or Higher</option>
					<option value='4'>Webmaster/Owner Only</option>";
			}
			echo "</select></td>
	</tr>
	<tr>
		<td valign='top'>Message Post</td>
		<td><textarea name='message_message' style='width: 420px; height: 320px' class='form'>".stripslashes ( $message_message )."</textarea></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'><input type='submit' name='message_add' value='Add message' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_messages.reset()'></td>
	</tr>";
echo "</table>";

?>