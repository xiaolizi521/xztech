<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

include ( "$_SERVER[DOCUMENT_ROOT]/functions.php" ); 
$id = mysql_real_escape_string ( $_GET['id'] );
$pg = mysql_real_escape_string ( $_GET['pg'] );
$result = mysql_query ( "SELECT news.*, COUNT(news_comments.date) as comments_num FROM news, news_comments WHERE news.id='$id' AND news_comments.id='$id' GROUP BY news.id" );
$show_news = mysql_fetch_array ( $result );
$limit = 10;
$comments_num = $show_news['comments_num'];
$pgs_num = ceil ( $comments_num/$limit );

if ( isset ( $pg ) && !empty ( $pg ) && ( $pg > 0 ) && ( $pg <= $pgs_num ) ) {
$offset = ( ( $pg * $limit ) - $limit );
} else {

if ( $pg <= 0 ) {
$offset = 0;
$pg = 1;
} elseif ( $pg > $pgs_num ) {
$offset = ( ( $pgs_num * $limit ) - $limit );
$pg = $pgs_num;
}

}

$comments_limit = mysql_query ( "SELECT * FROM news_comments WHERE id='$id' AND date IS NOT NULL ORDER BY date ASC LIMIT $offset, $limit" );

$datetime = $show_news['date']; 
$date = date ( 'l, F d, Y', strtotime( $datetime ) ); 

$directory = "$_SERVER[DOCUMENT_ROOT]/news/images/smilies";
if ( $handle = opendir ( $directory ) ) {
while ( false !== ( $file = readdir ( $handle ) ) ) { 
if ( $file != "." && $file != ".." ) { 
$img_array[] = str_replace( ".gif", "", $file ); 
} 
}
closedir( $handle ); 
}
?>

<script language="javascript">
function insert_smile( expression ) {
document.comment_form.comment_post.value += ':'+expression+' ';
}
</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="main">
<tr><td>

<?php
if ( isset ( $id ) && !empty ( $id ) && ( mysql_num_rows ( $result ) > 0 ) && ( eregi ( "^[a-z0-9\-_\.]+$", $id ) ) ) {
?>
<form name="comment_form" method="post">
<table width="100%" cellpadding="0" cellspacing="0" align="center" class="main">

<tr><td valign="top">

<table width="100%" cellpadding="3" cellspacing="0" border="0" class="main">
<tr><td bgcolor="<?php echo $tableheadercolor ?>" class="secondary">
<b><?php echo $show_news['headline'] ?></b>
</td><tr>
<tr><td>
<?php echo nl2br ( $show_news['news'] ) ?>
<p>
</td></tr>
<tr><td align="right"><i>Posted by <a href="#viewmember" onclick="ViewMember( '<?php echo $show_news['reporter'] ?>' )"><?php echo $show_news['reporter'] ?></a> on <?php echo $date ?></i></td></tr>
</table>

</td></tr>

<tr><td>
<hr noshade color="#C3C3C3" size="1" width="100%">
<?php
if ( $comments_num > 0 ) {

echo "Pages: ";
for ( $x = 1; $x <= $pgs_num; $x++ ) {
if ( $pg == $x ) {
echo "<b>[$x] </b>";
} else {
echo "<a href='$site_url/$main_filename?page=news/comments&id=$id&pg=$x'>$x</a> ";
}
}
?>
<hr noshade color="#C3C3C3" size="1" width="100%">
</td></tr>

<tr><td style="text-align: justify">
<?php
$comment_num = ( $offset + 1 );
while ( $show_comment = mysql_fetch_array ( $comments_limit ) ) {
$datetime = $show_comment['date']; 
$date = date ( 'm/d/y - h:i A', strtotime( $datetime ) ); 
$comment_id = date ( 'U', strtotime( $datetime ) );
$comment = nl2br ( $show_comment['comment'] );
$comment = parse_message ( "$comment" );

echo "<table bgcolor='$tableheadercolor' width='100%' cellpadding='2' cellspacing='0' class='secondary'><tr><td align='left'><b>#$comment_num | <a href='#viewmember' onclick='ViewMember( \"$show_comment[username]\" )'>$show_comment[username]</a></b></td><td align='right'><b>";

if ( $user_info[type] == 2 || $user_info[type] == 3 ) {
echo "<a href='$site_url/$main_filename?page=news/comments&id=$id&pg=$pg&edit=$comment_id&do=$show_comment[username]#edit'>Edit</a> | ";
} elseif ( $user_info[type] == 1 ) {
if ( $show_comment[username] == $user_info[username] ) {
echo "<a href='$site_url/$main_filename?page=news/comments&id=$id&pg=$pg&edit=$comment_id&do=$show_comment[username]#edit'>Edit</a> | ";
}
}

echo "<a href='$site_url/$main_filename?page=news/comments&id=$id&pg=$pg&quote=$comment_id&do=$show_comment[username]#quote'>Quote</a> | <a href='$site_url/$main_filename?page=pm_compose&to=$show_comment[username]'>Send PM</a> | $date</b></td></tr></table>";

echo "<table height='1'><tr><td></td></tr></table>";

echo "<table width='100%' cellpadding='2' cellspacing='0' class='main'><tr>";

echo "<td width='1' align='center' valign='top'>";

if ( !empty ( $show_comment['avatar'] ) ) {
echo "<img src='$show_comment[avatar]' width='50' height='50'>";
} else {
echo "<div style='width: 50px; height: 50px; border: 1px solid #4A4D4F'></div>";
}

echo "</td><td valign='top'>";

echo "<table width='100%' cellpadding='1' cellspacing='0' class='main'><tr><td style='text-align: justify'>$comment</td></tr></table>";

echo "</td></tr></table>";

echo "<table height='10'><tr><td></td></tr></table>";

$comment_num++;
}
?>
</td></tr>

<tr><td>
<hr noshade color="#C3C3C3" size="1" width="100%">
<?php
echo "Pages: ";
for ( $x = 1; $x <= $pgs_num; $x++ ) {
if ( $pg == $x ) {
echo "<b>[$x] </b>";
} else {
echo "<a href='$site_url/$main_filename?page=news/comments&id=$id&pg=$x'>$x</a> ";
}
}

} else {
echo "<center>No comments have been posted.</center>";
}
?>
<hr noshade color="#C3C3C3" size="1" width="100%">
</td></tr>

<tr><td valign="top"><a name="last"></a>
<table cellpadding="0" cellspacing="0" border="0" align="center" class="main">
<tr><td>
<?php
if ( isset ( $edit ) ) {
$editpostid = date ( "Y-m-d H:i:s", $edit );
$result_editpost = mysql_query ( "SELECT comment FROM news_comments WHERE id='$id' AND date='$editpostid' AND username='$do'" );
$editpost = mysql_fetch_array ( $result_editpost );
} elseif ( isset ( $quote ) ) {
$quotepostid = date ( "Y-m-d H:i:s", $quote );
$result_quotepost = mysql_query ( "SELECT * FROM news_comments WHERE id='$id' AND date='$quotepostid' AND username='$do'" );
$quotepost = mysql_fetch_array ( $result_quotepost );
}


if ( isset ( $_POST[submit_comment] ) ) {

if ( empty ( $comment_post ) ) {
echo "<center><b><font color='red' size='1'>You must enter a comment!</font></b></center>";
} else {

$result_member = mysql_query ( "SELECT * FROM users WHERE user_id='$user_info[user_id]'" );
$member = mysql_fetch_array ( $result_member );

$stripped_comment_post = strip_tags ( $comment_post );

$result = mysql_query ( "INSERT INTO news_comments ( id, date, username, avatar, comment ) VALUES ( '$id', now(), '$member[username]', '$member[avatar]', '$stripped_comment_post' )" );

if ( $pgs_num == 0 ) {
echo "<script>document.location.href='$site_url/$main_filename?page=news/comments&id=$id&pg=1'</script>";
} else {
echo "<script>document.location.href='$site_url/$main_filename?page=news/comments&id=$id&pg=$pgs_num'</script>";
}

}

}


if ( isset ( $_POST[edit_comment] ) ) {
$stripped_comment_post = strip_tags ( $comment_post );
$update_editpost = mysql_query ( "UPDATE news_comments SET comment='$stripped_comment_post' WHERE id='$id' AND date='$editpostid' AND username='$do'" );

if ( $pgs_num == 0 ) {
echo "<script>document.location.href='$site_url/$main_filename?page=news/comments&id=$id&pg=1'</script>";
} else {
echo "<script>document.location.href='$site_url/$main_filename?page=news/comments&id=$id&pg=$pgs_num'</script>";
}
}
?>
</td></tr>
<?php
if ( isset ( $user_info[user_id] ) ) {
?>
<tr><td align="center"><a name="quote"></a>
<?php
foreach ( $img_array as $var ) {
echo "<a href='#insertsmile' onclick='insert_smile( \"$var\" )'><img src='$site_url/news/images/smilies/$var.gif' border='0'></a>";
echo "   ";
}
?>
</td></tr>
<tr>
<td><a name="edit"></a><a name="quote"></a><textarea name="comment_post" id="comment_post" rows="10" cols="65" class="textbox">
<?php
if ( isset ( $edit ) ) {
if ( $user_info[type] == 2 || $user_info[type] == 3 ) {
echo $editpost[comment];
} elseif ( $user_info[type] == 1 ) {
if ( $do == $user_info[username] ) {
echo $editpost[comment];
}
}
} elseif ( isset ( $quote ) ) {
echo "[QUOTE=$do]".$quotepost[comment]."[/QUOTE]";
} else {
echo $comment_post;
}
?>
</textarea>
</td>
</tr>
<?php
} else {
echo "<tr><td><b>You must be registered to post comments. <a href='$site_url/login.php'>Login</a> or <a href='$site_url/$main_filename?page=register'>Register</a></b></td></tr>";
}
?>
</table>
</td></tr>

<?php
if ( isset ( $user_info[user_id] ) ) {
?>
<tr><td height="10"></td></tr>
<tr><td align="center"><?php
if ( isset ( $edit ) && ( mysql_num_rows ( $result_editpost ) > 0 ) ) {
if ( $user_info[type] == 2 || $user_info[type] == 3 ) {
echo "<input type='submit' name='edit_comment' value='Edit Comment' class='submit_button'>";
} elseif ( $user_info[type] == 1 ) {
if ( $do == $user_info[username] ) {
echo "<input type='submit' name='edit_comment' value='Edit Comment' class='submit_button'>";
} else {
echo "<input type='submit' name='submit_comment' value='Submit Comment' class='submit_button'>";
}
}
} else {
echo "<input type='submit' name='submit_comment' value='Submit Comment' class='submit_button'>";
}
?>   <input type="button" value="Clear Comment" class="submit_button" onclick="document.comment_form.comment_post.value=''"></td></tr>
<tr><td height="7"></td></tr>
<tr><td align="center">*Note: HTML is disabled</td></tr>
<?php
} else {
echo "";
}
?>

</table>
</form>

<?php
} else {
echo "<center>You have either entered an invalid news id or have not entered a news id at all.</center>";
}
?>

<table width="100%" height="20" cellpadding="0" cellspacing="0"><tr><td></td></tr></table> 

</td></tr></table>