<?php
			if ( isset ( $id ) && !empty ( $id ) ) { //delete news with an item selected
				$id = mysql_real_escape_string ( $_GET[id] );
				list ( $archive_id, $archive_username ) = explode ( ",", $id );
				// Aquire news item to be archived
				$result_archive_news = mysql_query ( "SELECT * FROM news WHERE id='$archive_id' AND poster='$archive_username'" );
				if ( $show_archive = mysql_num_rows ( $result_archive_news ) > 0 ) {
					// insert news item into archive
					$insert_archive = mysql_query ( "INSERT INTO archive ( id, headline, poster, news, category ) VALUES ( '$show_archive[id]', '$show_archive[headline]', '$show_archive[username]', '$show_archive[news]', '$show_archive[category]' )" );
					// delete news item from news table
					$delete_news = mysql_query ( "DELETE FROM news WHERE id='$id' AND poster='$archive_username'" );
					echo "<script>alert('News item has been successfully archived')</script>";
				}
				echo "<script>document.location='$PHP_SELF?view=main&amp;type=news&amp;action=archive'</script>";
			}
			// Display items to be archived
			if ( $user_info['type'] == "20" || $user_info['type'] == "21" || $user_info['type'] == "30" || $user_info['type'] == "31" 
				|| $user_info['type'] == "80" || $user_info['type'] == "81" ) {
				$result_delete = mysql_query( "SELECT * FROM news WHERE poster='$user_info[username]' ORDER BY id DESC" );
			} elseif ( $user_info['type'] >= "90" ) {
				$result_delete = mysql_query( "SELECT * FROM news ORDER BY id DESC" );
			}
			$count = 1;
			echo "<table cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 50%;'>
			";
			while ( $archive = mysql_fetch_array( $result_delete ) ) {
				$date = DisplayDate( "$archive[id]", "l, F d, Y \A\\t h:i A", "0" );
				echo "	<tr>
						<td align='left' style='border-bottom: 1px solid #C3C3C3'>$count. <span style='text-decoration: underline;'><b>".stripslashes ( $archive['headline'] )."</b></span><br />
							- <i>Posted By $delete[poster] On $date</i></td>
						<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='#archive' onclick='ArchiveNews( \"$PHP_SELF?view=main&amp;type=news&amp;action=archive&amp;id=$archive[id],$archive[poster]\" )'>Archive</a></td>
					</tr>";
				$count++;
			}
			echo "</table>";
?>