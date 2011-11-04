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
		<td>Episode Number</td>
		<td><input type='text' name='number' style='width: 50px' class='form'></td>
	</tr>
	<tr>
		<td>Title</td>
		<td><input type='text' name='title' style='width: 200px' class='form'></td>";
			
			echo"	</tr>
	<tr>
		<td>Type</td>
		<td><select name='type' class=form>
		<option value='raw'>Raw</option>
		<option value='sub'>Dattebayo</option>
		<option value='flo'>Flomp-Rumbel</option>
		</select>
		</td>
	</tr>
	<tr>
		<td>CRC</td>
		<td><input type='text' name='crc' style='width: 50px' class='form'></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'><input type='submit' name='add_anime' value='Add Anime' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_news.reset()'></td>
	</tr>
</table>";
?>