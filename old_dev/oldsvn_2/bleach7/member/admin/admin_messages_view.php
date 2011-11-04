<?php
			if ( isset ( $id ) && !empty ( $id ) ) { //delete news with an item selected
				//$id = mysql_real_escape_string ( $_GET[id] );
				$result_message_view = mysql_query ( "SELECT * FROM admin_message WHERE id = '$id'" );
				$message_view = mysql_fetch_array( $result_message_view );
				
				//pull comments
				$result_comments_view = mysql_query ( "SELECT * FROM admin_comments WHERE newsid = '$id' ORDER BY id ASC" );
				
				if ( mysql_num_rows ( $result_message_view ) > 0 ) {
					$message = stripslashes ( nl2br ( $message_view['message'] ) );
					$date = DisplayDate( "$message_view[id]", "l, F d, Y \A\\t h:i A", "0" );
					echo '<table width=\"60%\" cellpadding=\"3\" cellspacing=\"0\" border=\"0\" class=\"main\">
    <tr>
        <td class=\"secondary\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"3\"><b>' . $message_view['headline'] . '</b></font></td>
    </tr>
    <tr>
        <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">' . $message . '</font><p /></td>
    </tr>
    <tr>
        <td align=\"right\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><i>Posted by ' . $message_view['poster'] . ' on ' . $date . '</i></font><br />

<br /><a href="index.php?action=addcomment&id='.$id.'"><b>Click here to add a Comment</b></a>'; 
?>
<br /><br />

<table width="100%" border="1" style="border-style:solid; border:thick; border-spacing:0px; border-width:1px; border-bottom-width:0px;">
<?
if ( mysql_num_rows ( $result_comments_view ) > 0 ) 
{
 $i = 1;
 while ( $comments_view = mysql_fetch_array($result_comments_view) )
 {
 $i++;
 $comment = stripslashes ( nl2br ( $comments_view['comment'] ) );
 $poster = $comments_view['poster'];
 echo "<tr><td style=\"border-bottom-width:0px;\">$comment</td></td>
 <tr><td bgcolor=#eeeeee align=right style=\"border-top-width:0px;\"><small><em>Comment posted by $poster</em></small>
 </td></tr>";
 }
}
echo '</table>';

echo'</td>
    </tr>
</table>';
				}
			}
			else {
				echo '<b>Invalid ID Code</b>';
			}
?>
<br /><a href="index.php?action=addcomment&id='.$id.'"><b>Click here to add a Comment</b></a>