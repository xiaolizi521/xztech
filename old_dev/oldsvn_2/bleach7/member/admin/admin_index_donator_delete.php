<?php
			if ( isset ( $id ) && !empty ( $id ) ) { //delete news with an item selected
				$id = mysql_real_escape_string ( $_GET[id] );
				$result_delete_news = mysql_query ( "SELECT id FROM donator WHERE id=$id" );
				if ( mysql_num_rows ( $result_delete_news ) > 0 ) {
					$delete_news = mysql_query ( "DELETE FROM donator WHERE id=$id" );
					echo "<script>alert('Message item has been successfully deleted')</script>";
				}
				echo "<script>document.location='$PHP_SELF?view=main&amp;type=index&amp;action=donator'</script>";
			}
?>
