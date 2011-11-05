<?php
			if ( isset ( $id ) && !empty ( $id ) ) { //editting news with an item selected
				$id = mysql_real_escape_string ( $_GET['id'] );
				if ( $user_info['type'] == "20" || $user_info['type'] == "21" || $user_info['type'] == "30" || 
					$user_info['type'] == "31" || $user_info['type'] >= "80" ) {
					$result_edit_id = mysql_query( "SELECT * FROM news WHERE id='$id'" );
				}
				$edit_id = mysql_fetch_array( $result_edit_id );
				if ( mysql_num_rows ( $result_edit_id ) > 0 ) { //a valid news id is found
					if ( count ( $errors ) > 0 ) {
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
		<td><input type='text' name='headline' style='width: 420px' value='".stripslashes ( $edit_id['headline'] )."' class='form'></td>
	</tr>
	<tr>
		<td>Category</td>
		<td><select name='category' class=form>
";
					if ( $user_info['type'] < 80 ) {
						switch ( $edit_id['category'] ) {
							case 0:
								echo "				<option value='0' selected>Site News</option>
				<option value='1'>Manga News</option>
				<option value='2'>Anime News</option>
				";
								break;
							case 1:
								echo "				<option value='0'>Site News</option>
				<option value='1' selected>Manga News</option>
				<option value='2'>Anime News</option>
				";
								break;
							case 2:
								echo "				<option value='0'>Site News</option>
				<option value='1'>Manga News</option>
				<option value='2' selected>Anime News</option>
				";
								break;
						}
					}
					else {
						switch ( $edit_id['category'] ) {
							case 0:
								echo "				<option value='0' selected>Site News</option>
				<option value='1'>Manga News</option>
				<option value='2'>Anime News</option>
				<option value='3'>Editorial</option>
				";
								break;
							case 1:
								echo "				<option value='0'>Site News</option>
				<option value='1' selected>Manga News</option>
				<option value='2'>Anime News</option>
				<option value='3'>Editorial</option>
				";
								break;
							case 2:
								echo "				<option value='0'>Site News</option>
				<option value='1'>Manga News</option>
				<option value='2' selected>Anime News</option>
				<option value='3'>Editorial</option>
				";
								break;
							case 3:
								echo "				<option value='0'>Site News</option>
				<option value='1'>Manga News</option>
				<option value='2'>Anime News</option>
				<option value='3' selected>Editorial</option>
				";
								break;
						}
					}
					echo "	</tr>
	<tr>
		<td valign='top'>News Post</td>
		<td><textarea name='news' style='width: 420px; height: 320px' class='form'>".stripslashes ( $edit_id['news'] )."</textarea></td>
	</tr>
	<tr>
		<td></td>
	   <input type='hidden' name='id' value='$id'>
		<td align='center'><input type='submit' name='edit_news' value='Edit News' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_news.reset()'>   <input type='button' value='Go Back' class='form' onclick='document.location=\"index.php?action=editnews\"'></td>
	</tr>
</table>
";
				} else { //no valid news id found
					echo "<p align='center'><b>Invalid News ID</b></p>";
				}
			} else { //end editting news with an item selected
				if ( $user_info['type'] == "20" || $user_info['type'] == "21" || $user_info['type'] == "30" || $user_info['type'] == "31" 
					|| $user_info['type'] >= "80" ) {
					$result_edit = mysql_query( "SELECT * FROM news ORDER BY id DESC" );
				}
				$count = 1;
				echo "<table cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 50%;'>";
				while ( $edit = mysql_fetch_array( $result_edit ) ) {
				$color = ( $count % 2 == 0 ) ? "#eeeeee" : "#ffffff";
					$date = DisplayDate( "$edit[id]", "l, F d, Y \A\\t h:i A", "0" );
					echo "	<tr bgcolor='$color'>
							<td align='left' style='border-bottom: 1px solid #C3C3C3'>$count. <span style='text-decoration: underline;'><b>".stripslashes ( $edit['headline'] )."</b></span><br />
								- <i>Posted By $edit[poster] On $date</i></td>
							<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='index.php?action=editnews&amp;id=$edit[id]'>Edit</a></td>
						</tr>
						";
					$count++;
				}
				echo "</table>";
			}
?>