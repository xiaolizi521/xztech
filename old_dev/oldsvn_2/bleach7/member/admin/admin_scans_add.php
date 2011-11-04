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
		<td>Chapter Number</td>
		<td><input type='text' name='number' style='width: 50px' class='form'></td>
	</tr>
	<tr>
		<td>Title</td>
		<td><input type='text' name='title' style='width: 200px' class='form'></td>";
			
			echo"	</tr>
	<tr>
	<td>
	<input type='hidden' name='group' value='0' />
	</td>
	</tr>
	<tr>
		<td></td>
		<td align='center'><input type='submit' name='add_scan' value='Add Scan' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_news.reset()'></td>
	</tr>
</table>";
?>