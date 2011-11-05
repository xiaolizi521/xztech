<?php
include ("db.php");
include ("functions.php");
$det=isbanned(1);
if (!$det == false)
			{
			
			print "Your are banned from posting comments until".$det['banends'];
			
			}else{
				
			
	?>
	<form name="comment_form" method="post">
	<p align="center"><input type="button" value="Bold" class="form" onclick="InsertBold()">
	<input type="button" value="Italic" class="form" onclick="InsertItalic()">
	<input type="button" value="Underline" class="form" onclick="InsertUnderline()">
	<input type="button" value="Color" class="form" onclick="InsertColor()">
	<input type="button" value="Highlight" class="form" onclick="InsertHL()">
	<input type="button" value="URL" class="form" onclick="InsertURL()">
	<input type="button" value="Spoiler" class="form" onclick="InsertSpoiler()"></p>
	<table cellpadding="0" cellspacing="0" align="center"><tr><td align="center">
	<table cellpadding="5" cellspacing="0"><tr><td valign="top">
	<a name="comment"></a>
	<textarea name="comment_post" id="comment_post" style="width: 330px; height: 185px; overflow: auto" class="form">
<?php
if ( isset ( $edit ) && !empty ( $edit ) ) {
	list ( $edit_id, $edit_username ) = explode ( ",", $edit );
	$result_edit_post = mysql_query ( "SELECT news_comments.id, news_comments.newsid, 			news_comments.comment, users.username FROM news_comments LEFT JOIN users ON (news_comments.poster=users.username) WHERE news_comments.newsid='$id' AND news_comments.id='$edit_id' AND users.username='$edit_username'" );
	if ( mysql_num_rows ( $result_edit_post ) > 0 ) {
		$edit_post = mysql_fetch_array ( $result_edit_post );
		echo stripslashes ( $edit_post[comment] );
	} 
}

if ( isset ( $quote ) && !empty ( $quote ) ) {
	list ( $quote_id, $quote_username ) = explode ( ",", $quote );
	$result_quote_post = mysql_query ( "SELECT news_comments.id, news_comments.newsid, news_comments.comment, users.username FROM news_comments LEFT JOIN users ON (news_comments.poster=users.username) WHERE news_comments.newsid='$id' AND news_comments.id='$quote_id' AND users.username='$quote_username'" );
	if ( mysql_num_rows ( $result_quote_post ) > 0 ) {
		$quote_post = mysql_fetch_array ( $result_quote_post );
		echo "[QUOTE=$quote_username]".stripslashes ( $quote_post[comment] )."[/QUOTE]";
	} 
}
?>
</textarea>
	<?php
	if ( isset ( $edit ) && !empty ( $edit ) && mysql_num_rows ( $result_edit_post ) > 0 ) {
		echo "<input type='hidden' name='edit_comment'>";
		$button_value = "Edit Comment"; 
	}
	else { 
		echo "<input type='hidden' name='submit_comment'>";
		$button_value = "Submit Comment";
	} 
	?>
	</td>
	<td valign="top">
	<fieldset>
	<table cellpadding="0" cellspacing="0"><tr><td height="110">
	<?php
	echo "<table cellpadding='5' cellspacing='0'><tr>";
	sort ( $smilies_array );
	for ( $x = 1; $x <= 18; $x++ ) {
		echo "<td><a href=\"#insertsmile\" onclick=\"InsertSmile( '$smilies_array[$x]' )\"><img src=\"$site_url/$script_folder/images/smilies/$smilies_array[$x].gif\" border=\"0\"></a></td>";
		if ( !is_float ( $x/3 ) ) {
			echo "</tr><tr>";
		}
	}
	echo "</table>";
	echo "<table align='center' class='main'><tr><td align='center'><a href='#viewall' onclick='ViewAllSmilies()'>View All</a></td></tr></table>";
	?>
	</td></tr></table>
	</fieldset>
	</td></tr></table>
	</td></tr></table>
	<p><center><input type="button" value="<?php echo $button_value ?>" class="form" onclick="if ( document.comment_form.comment_post.value=='' ) { alert( 'You must enter a comment' ); return false; } else { javascript:ClickTracker(); }">   <input type="button" value="Reset Comment" class="form" onclick="document.comment_form.reset()"></center>
	</form>
	<?php
}//ban end here

 
?>