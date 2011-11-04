<?php

if(isset($_GET['dn']))
{ $dn = $_GET['dn']; }
else
{ $dn = 0; }

if($dn == 1)
{
$delete_news = mysql_query ( "DELETE FROM news WHERE id='$id'" );
$delete_comments = mysql_query ( "DELETE FROM news_comments WHERE newsid='$id'" );
echo "<h3>Lawlz, that newspost was like deleted and stuff.</h3>";
echo "<script>alert('News item ID $id has been successfully deleted')</script>";
echo "<script>document.location='index.php?action=deletenews'</script>";
}

/*
			if ( isset ( $id ) && !empty ( $id ) && $_GET['dn'] == 1 ) { //delete news with an item selected
				$id = mysql_real_escape_string ( $_GET['id'] );
				list ( $delete_id, $delete_username ) = explode ( ",", $id );
				$result_delete_news = mysql_query ( "SELECT id, poster FROM news WHERE id='$delete_id' AND poster='$delete_username'" );
				if ( mysql_num_rows ( $result_delete_news ) > 0 ) {
					$delete_news = mysql_query ( "DELETE FROM news WHERE id='$id' AND poster='$delete_username'" );
					$delete_comments = mysql_query ( "DELETE FROM news_comments WHERE newsid='$id'" );
					echo "<script>alert('News item has been successfully deleted')</script>";
				}
				echo "<script>document.location='index.php?action=deletenews'</script>";
			}
			*/
else{
			if ( $user_info['type'] == "20" || $user_info['type'] == "21" || $user_info['type'] == "30" || $user_info['type'] == "31" 
				|| $user_info['type'] == "80" || $user_info['type'] == "81" ) {
				$result_delete = mysql_query( "SELECT * FROM news WHERE poster='$user_info[username]' ORDER BY id DESC" );
			} elseif ( $user_info['type'] >= "90" ) {
				$result_delete = mysql_query( "SELECT * FROM news ORDER BY id DESC" );
			}
			$count = 1;
			echo "<table cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 50%;'>
			";
			while ( $delete = mysql_fetch_array( $result_delete ) ) {
			$color = ( $count % 2 == 0 ) ? "#eeeeee" : "#ffffff";
			
				$date = DisplayDate( "$delete[id]", "l, F d, Y \A\\t h:i A", "0" );
				echo "	<tr bgcolor='$color'>
						<td align='left' style='border-bottom: 1px solid #C3C3C3'>$count. <span style='text-decoration: underline;'><b>".stripslashes ( $delete['headline'] )."</b></span><br />
							- <i>Posted By $delete[poster] On $date</i></td>
						<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='index.php?action=deletenews&id=$delete[id]&dn=1' onclick='DeleteNews( \"index.php?action=deletenews&amp;id=$delete[id],$delete[poster]\" )'>Delete</a></td>
					</tr>";
				$count++;
			}
			echo "</table>";
}
?>