<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

include ( "news.php" );
$id = mysql_real_escape_string ( $_GET['id'] );
$pg = mysql_real_escape_string ( $_GET['pg'] );
?>
<script>
click_count = 0;

function DeleteComment( URL ) {
if (confirm('Are you sure you want to delete this comment?')) {
document.location=URL;
return true;
} else {
return false;
}
}

function ClickTracker() {
click_count++;
if ( click_count == 1 ) {
document.comment_form.submit();
} 
if ( click_count >= 2 ) {
alert ( "Please do not try to submit the form more than once" );
return false;
}
}

function InsertSmile( expression ) {
document.comment_form.comment_post.value += ':'+expression+' ';
}

function ViewAllSmilies() {
window.open("<?php echo "$site_url/$script_folder/smilies.php" ?>","legend","width=170,height=500,left=0,top=0,resizable=yes,scrollbars=yes"); 
}
</script>
<?php
function CommentOption( $id, $username, $type ) {
global $site_path;
if ( $type == "delete" ) {
$type_url = "<a href='#delete' onclick='DeleteComment(\"$site_path/comments&id=$_GET[id]&pg=$_GET[pg]&$type=$id,$username\")'>".ucwords( $type )."</a>";
} else {
$type_url = "<a href='$site_path/comments&id=$_GET[id]&pg=$_GET[pg]&$type=$id,$username#comment'>".ucwords( $type )."</a>";
}
return $type_url;
}

if ( isset ( $_POST[submit_comment] ) ) {
$comment_post = mysql_real_escape_string ( htmlspecialchars ( $_POST[comment_post], ENT_QUOTES ) );
$update_comment_num = mysql_query ( "UPDATE news SET comments=(comments+1) WHERE id='$id'" );
$update_user_posts = mysql_query ( "UPDATE users SET posts=(posts+1) WHERE username='$user_info[username]'" );
$insert_comment = mysql_query ( "INSERT INTO news_comments ( id, newsid, poster, comment ) VALUES ( $nid, $id, '$user_info[username]', '$comment_post' )" );
if ( $pages_num == 0 ) {
echo "<script>document.location='$site_path/comments&id=$id&pg=1'</script>";
} else {
echo "<script>document.location='$site_path/comments&id=$id&pg=$pages_num'</script>";
}
}

if ( isset ( $_POST[edit_comment] ) ) {
$comment_post = mysql_real_escape_string ( htmlspecialchars ( $_POST[comment_post], ENT_QUOTES ) );
list ( $edit_id ) = explode ( ",", $edit );
$update_comment = mysql_query ( "UPDATE news_comments SET comment='$comment_post' WHERE newsid='$id' AND id='$edit_id'" );
echo "<script>document.location='$site_path/comments&id=$id&pg=$pg'</script>";
}

if ( isset ( $delete ) && !empty ( $delete ) ) {
list ( $delete_id, $delete_username ) = explode ( ",", $delete );
$result_delete_post = mysql_query ( "SELECT news_comments.id, news_comments.newsid, news_comments.comment, users.username FROM news_comments LEFT JOIN users ON (news_comments.poster=users.username) WHERE news_comments.newsid='$id' AND news_comments.id='$delete_id' AND users.username='$delete_username'" );
if ( mysql_num_rows ( $result_delete_post ) > 0 ) {
$delete_post = mysql_query ( "DELETE FROM news_comments WHERE newsid='$id' AND id='$delete_id'" );
$update_comments_num = mysql_query ( "UPDATE news SET comments=(comments-1) WHERE id='$id'" );
if ( $pages_num == 0 ) {
echo "<script>document.location='$site_path/comments&id=$id&pg=1'</script>";
} else {
echo "<script>document.location='$site_path/comments&id=$id&pg=$pages_num'</script>";
}
} 
}

if ( $comments_num == 0 ) {
echo "<p><center>No comments have been made.";
if ( isset ( $user_info[user_id] ) ) {
echo " Be the first by entering a comment below.</center><p>";
} else {
echo "<p>";
}
echo "</center>";
} else {

if ( !isset ( $pg ) || empty ( $pg ) || $pg < 1 ) {
header ( "Location: $site_path/comments&id=$id&pg=1" );
ob_end_flush();
} elseif ( $pg > $pages_num ) {
header ( "Location: $site_path/comments&id=$id&pg=$pages_num" );
ob_end_flush();
}

if ( isset ( $pg ) && !empty ( $pg ) && ( $pg > 0 ) && ( $pg <= $pages_num ) ) {
$offset = ( ( $pg * $limit ) - $limit );
} else {

if ( $pg <= 0 ) {
$offset = 0;
$pg = 1;
} elseif ( $pg > $pages_num ) {
$offset = ( ( $pages_num * $limit ) - $limit );
$pg = $pages_num;
}

}

$result_comments = mysql_query ( "SELECT users.*, news_comments.* FROM users LEFT JOIN news_comments ON (users.username=news_comments.poster) WHERE news_comments.newsid='$id' ORDER BY news_comments.id ASC LIMIT $offset, $limit" );

Paginate( "pg", "$pages_num", "comments&id=$_GET[id]" );

$comment_num = ( $offset + 1 );
while ( $show_comments = mysql_fetch_array ( $result_comments ) ) {
$member_username = "<a href='$site_path/member&id=$show_comments[username]'><b><u>$show_comments[username]</u></b></a>";
$comment_date = DisplayDate( "$show_comments[id]", "M d Y, h:i A", "1" );
$comment_postnum = "Post #$comment_num";
if ( empty ( $show_comments[avatar] ) ) {
$member_avatar = "<img src='$site_url/$script_folder/images/avatars/none.gif' width='60' height='60'>";
} else {
list ( $avatar_width, $avatar_height ) = getimagesize ( "$show_comments[avatar]" );
if ( $avatar_width > 60 || $avatar_height > 60 ) {
$member_avatar = "<img src='$show_comments[avatar]' width='60' height='60'>";
} else {
$member_avatar = "<img src='$show_comments[avatar]'>";
}
} 
if ( $show_comments[type] == 3 ) {
$member_type = "Administrator";
} elseif ( $show_comments[type] == 2 ) {
$member_type = "Moderator";
} else {
$member_type = "Member";
}

if ( $show_comments[posts] < 9 ) {
$member_rank = "Spiritless";
} elseif ( $show_comments[posts] >= 10 && $show_comments[posts] < 24 ) {
$member_rank = "Human";
} elseif ( $show_comments[posts] >= 25 && $show_comments[posts] < 49 ) {
$member_rank = "Demi-Hollow";
} elseif ( $show_comments[posts] >= 50 && $show_comments[posts] < 99 ) {
$member_rank = "Hollow";
} elseif ( $show_comments[posts] >= 100 && $show_comments[posts] < 499 ) {
$member_rank = "Shingami";
} elseif ( $show_comments[posts] >= 500 && $show_comments[posts] < 999 ) {
$member_rank = "Vice-Captain";
} elseif ( $show_comments[posts] >= 1000 ) {
$member_rank = "Captain";
}

$member_rank = "Rank: $member_rank";

$joindate = DisplayDate( "$show_comments[registered_on]", "m/d/y", "0" );
$member_joindate = "Joined: $joindate";
$member_posts = "Posts: $show_comments[posts]";
$member_num = "Member No. $show_comments[user_id]";
if ( ( time() - $show_comments[last_activity_time] ) <= 300 ) {
$member_online = "Status: <font color='green'>Online</font>";
} else {
$member_online = "Status: <font color='red'>Offline</font>";
}
$delete_link = CommentOption( "$show_comments[id]", "$show_comments[username]", "delete" );
$edit_link = CommentOption( "$show_comments[id]", "$show_comments[username]", "edit");
$quote_link = CommentOption( "$show_comments[id]", "$show_comments[username]", "quote" );
if ( isset ( $user_info[user_id] ) ) {
if ( $user_info[type] > 1 ) {
$comment_options1 = "$delete_link | $edit_link | ";
} else {
if ( $show_comments[user_id] == $user_info[user_id] ) {
$comment_options1 = "$delete_link | $edit_link | ";
}
}
$comment_options2 = "$quote_link | <a href='#comment'>Reply</a> | <a href='$site_path/pm_compose&to=$show_comments[username]'>PM</a>";
$comment_options = "$comment_options1$comment_options2";
}
$comment = stripslashes ( nl2br ( "$show_comments[comment]" ) );
$comment = ParseMessage ( "$comment" );

include ( "templates/comments.php" );
unset ( $comment_options1 );
$comment_num++;
}

}

Paginate( "pg", "$pages_num", "comments&id=$_GET[id]" );

if ( !isset ( $user_info[user_id] ) ) {
echo "<center><b>You must be registered to post comments. <a href='$site_path/login'><b>Login</b></a> or <a href='$site_path/register'><b>Register</b></a></b></center>";
} else {
?>
<form name="comment_form" method="post">
<p>
<table cellpadding="0" cellspacing="0" align="center"><tr><td align="center">
<table cellpadding="5" cellspacing="0"><tr><td valign="top">
<a name="comment"></a>
<textarea name="comment_post" id="comment_post" style="width: 330px; height: 185px; overflow: auto" class="form">
<?php
if ( isset ( $edit ) && !empty ( $edit ) ) {
list ( $edit_id, $edit_username ) = explode ( ",", $edit );
$result_edit_post = mysql_query ( "SELECT news_comments.id, news_comments.newsid, news_comments.comment, users.username FROM news_comments LEFT JOIN users ON (news_comments.poster=users.username) WHERE news_comments.newsid='$id' AND news_comments.id='$edit_id' AND users.username='$edit_username'" );
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
} else { 
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
echo "<td><a href='#insertsmile' onclick='InsertSmile( \"$smilies_array[$x]\" )'><img src='$site_url/$script_folder/images/smilies/$smilies_array[$x].gif' border='0'></a></td>";
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
<p><center><input type="button" value="<?php echo $button_value ?>" class="form" onclick="if ( document.comment_form.comment_post.value=='' ) { alert( 'You must enter a comment' ); return false; } else { ClickTracker(); }">   <input type="button" value="Reset Comment" class="form" onclick="document.comment_form.reset()"></center>
</form>
<?php
}

$file_title = "News Comments:split:$headline";
?>