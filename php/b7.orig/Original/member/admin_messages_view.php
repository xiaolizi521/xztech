<?php
			if ( isset ( $id ) && !empty ( $id ) ) { //delete news with an item selected
				$id = mysql_real_escape_string ( $_GET[id] );
				$result_message_view = mysql_query ( "SELECT * FROM admin_message WHERE id = '$id'" );
				$message_view = mysql_fetch_array( $result_message_view );
				if ( mysql_num_rows ( $result_message_view ) > 0 ) {
					$date = DisplayDate( "$message_view[id]", "l, F d, Y \A\\t h:i A", "0" );
					echo "<table width='60%' cellpadding='3' cellspacing='0' border='0' class='main'>
    <tr>
        <td class='secondary'><b>$message_view[headline]</b></td>
    </tr>
    <tr>
        <td>$message_view[message]<p /></td>
    </tr>
    <tr>
        <td align='right'><i>Posted by $message_view[poster] on $date</i><br />
$comments</td>
    </tr>
</table>";
				}
			}
			else {
				echo "<b>Invalid ID Code</b>";
			}
?>
