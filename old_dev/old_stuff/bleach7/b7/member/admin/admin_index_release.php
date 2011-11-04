<?php
				$result_index_release = mysql_query( "SELECT * FROM index_info" );
				$index_release = mysql_fetch_array( $result_index_release );
				if ( mysql_num_rows ( $result_index_release ) > 0 ) { // Valid SQL query
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
					echo "<br />
<table cellpadding='0' cellspacing='0' class='main'>
	<tr>
		<td>Anime Raw Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type='text' name='anime_raw' style='width: 35px' value='".stripslashes ( $index_release['anime_raw'] )."' class='form'></td>
	</tr>
	<tr>
		<td>Anime Sub Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type='text' name='anime_sub' style='width: 35px' value='".stripslashes ( $index_release['anime_sub'] )."' class='form'></td>
	</tr>
	<tr>
		<td>Manga Raw Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type='text' name='manga_raw' style='width: 35px' value='".stripslashes ( $index_release['manga_raw'] )."' class='form'></td>
	</tr>
	<tr>
		<td>Manga Sub Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type='text' name='manga_sub' style='width: 35px' value='".stripslashes ( $index_release['manga_sub'] )."' class='form'></td>
	</tr>
</table>
<br />
<table cellpadding='0' cellspacing='0' class='main'>
	<tr>
		<td align='center'><input type='submit' name='edit_index_info' value='Edit Index Info' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_index.reset()'>   <input type='button' value='Go Back' class='form' onclick='document.location=\"index.php\"'></td>
	</tr>
</table>
";
				}
				else {
					echo "<p style='text-align: center;'><b>Invalid SQL Query</b></p>";
				}
?>
