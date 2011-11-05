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
	<tr>
		<td>Headline</td>
		<td><input type='text' name='headline' style='width: 420px' class='form'></td>
	</tr>
	<tr>
		<td>Category</td>
		<td><select name='category' class=form>";
			if ( $user_info['type'] < 80 ) {
				echo "				<option value='0' selected>Site News</option>
				<option value='1'>Manga News</option>
				<option value='2'>Anime News</option>";
			}
			else {
				echo "				<option value='0' selected>Site News</option>
				<option value='1'>Manga News</option>
				<option value='2'>Anime News</option>
				<option value='3'>Editorial</option>";
			}
			echo"	</tr>
	<tr>
		<td valign='top'>News Post</td>
		<td><textarea name='news' style='width: 420px; height: 320px' class='form'>".stripslashes ( $news )."</textarea></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'><input type='submit' name='add_news' value='Add News' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_news.reset()'></td>
	</tr>
</table>";
?>