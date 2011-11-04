<?php
			if ( isset ( $id ) && !empty ( $id ) ) { //delete news with an item selected
				//$id = mysql_real_escape_string ( $_GET[id] );
				list ( $delete_id, $delete_username ) = explode ( ",", $id );
				$result_delete_news = mysql_query ( "SELECT id, poster FROM admin_message WHERE id='$delete_id' AND poster='$delete_username'" );
				if ( mysql_num_rows ( $result_delete_news ) > 0 ) {
					$delete_news = mysql_query ( "DELETE FROM admin_message WHERE id='$id' AND poster='$delete_username'" );
					$delete_comments = mysql_query ( "DELETE FROM admin_comments WHERE newsid='$id'" );
					echo'<h3>Message go boom! zOMG run away!</h3>';
					echo "<script>alert('Message item has been successfully deleted')</script>";
				}
				echo "<script>document.location='index.php?action=viewmessage'</script>";
			}
?>