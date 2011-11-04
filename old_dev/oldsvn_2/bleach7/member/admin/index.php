<?php
ob_start();
include ( "header.php" );
include ( "../settings.php" );
include ( "../db.php" );
if ( !isset ( $_COOKIE['user_id'] ) && !isset ( $_COOKIE['password'] ) ) {
header ( "Location: $site_url/$main_filename?$ident=$script_folder/login" );
exit();
}

$rank99 = "Sensei";
$rank98 = "Webmaster";
$rank90 = "Administration";
$rank80 = "Staff Member";
$rank31 = "M7 Team | Mod";
$rank30 = "M7 Team";
$rank21 = "Info Team | Mod";
$rank20 = "Info Team";
$rank10 = "Moderator";
$rank2 = "Privileged Member";
$rank1 = "Member";

//check if user has permission to view
if($user_info['type'] < 20)
{
echo 'Unauthorized, Noob...';
exit;
}
else //User has permission, begin CP
{

if(!isset($_GET['id']))
{ $id = ''; }
else
{ $id = mysql_real_escape_string ( $_GET['id'] ); }

if(!isset($errors))
{ $errors = ''; }

include ( "../rank.php" );
include ( "../settings.php" );
include ( "../functions.php" );

//DB QUERY RUNS

include ( "header.php" );

echo "<center><font class='main'>";


function DisplayErrors () {
	global $errors;
	echo "<p align='center'>";
	if ( count ( $errors ) == 1 ) {
		echo "<b>The following error was found:</b>";
	} else {
		echo "<b>The following errors were found:</b>";
	}
	echo "</p>";
	echo "<ul type='square'>";
	foreach ( $errors as $var ) {
		echo "<li>$var</li>";
	}
	echo "</ul>";
}
//ADD NEWS SQL
if ( isset ( $_POST['add_news'] ) ) {
	$headline = $_POST['headline'];
	$category = $_POST['category'];
	$news = $_POST['news'];
	if ( empty ( $headline ) ) {
		$errors[] = "You must enter a headline";
	}
	if ( empty ( $news ) ) {
		$errors[] = "You must enter a news post";
	}
	if ( count ( $errors ) == 1 ) {
		$headline = mysql_real_escape_string ( htmlspecialchars ( $_POST['headline'], ENT_QUOTES ) );
		$news = mysql_real_escape_string ( $_POST['news'] );
		$insert_news = mysql_query ( "INSERT INTO news ( id, headline, poster, news, category ) VALUES ( $nid, '$headline', '$user_info[username]', '$news', '$category' )" );
		include('/home/bleach7/public_html/rss.php');
		echo "<script>alert('News item has been successfully added')</script>";
		echo "<script>document.location='index.php?action=editnews'</script>";
	}
}
//EDIT NEWS SQL
if ( isset ( $_POST['edit_news'] ) ) {
	$category = $_POST['category'];
	$id = $_POST['id'];
	$headline = mysql_real_escape_string ( htmlspecialchars ( $_POST['headline'], ENT_QUOTES ) );
	$news = mysql_real_escape_string ( $_POST['news'] );
	$update_news = mysql_query ( "UPDATE `news` SET `headline`='$headline', `news`='$news', `category`='$category' WHERE `id`='$id'" );
		echo "<script>alert('News item has been successfully editted news. ID $id')</script>";
		echo "<script>document.location='index.php?action=editnews'</script>";
	}

//ADD ADMIN MESSAGE
if ( isset ( $_POST['message_add'] ) ) {
	$rank_available = $_POST['rank_available'];
	$message_headline = $_POST['message_headline'];
	$message_message = $_POST['message_message'];
	if ( empty ( $message_headline ) ) {
		$errors[] = "You must enter a headline";
	}
	if ( empty ( $message_message ) ) {
		$errors[] = "You must enter a news post";
	}
	if ( count ( $errors ) == 1 ) {
		$message_headline = mysql_real_escape_string ( htmlspecialchars ( $_POST['message_headline'], ENT_QUOTES ) );
		$message_message = mysql_real_escape_string ( $_POST['message_message'] );
		$insert_news = mysql_query ( "INSERT INTO admin_message ( id, headline, poster, message, rank ) VALUES ( $nid, '$message_headline', '$user_info[username]', '$message_message', '$rank_available' )" );
		echo "<script>alert('Message item has been successfully added')</script>";
		echo "<script>document.location='index.php?action=viewmessage'</script>";
	}
}

//EDIT ADMIN MESSAGE
if ( isset ( $_POST['message_edit'] ) ) {
		$message_headline = mysql_real_escape_string ( htmlspecialchars ( $_POST['message_headline'], ENT_QUOTES ) );
		$message_message = mysql_real_escape_string ( $_POST['message_message'] );
		$id = $_POST['id'];
		$rank_available = $_POST['rank_available'];
		$update_message = mysql_query ( "UPDATE admin_message SET headline='$message_headline', message='$message_message', rank='$rank_available' WHERE id='$id'" );
		echo "<script>alert('Message has been successfully editted id $id')</script>";
		echo "<script>document.location='index.php?action=viewmessage2&id=$id'</script>";
	}
//ADD ADMIN MESSAGE COMMENT
if ( isset ( $_POST['comment_add'] ) ) {
		$comment_message = mysql_real_escape_string ( $_POST['comment_message'] );
		$id = $_POST['newsid'];
		$update_message = mysql_query ( "INSERT INTO admin_comments ( id, newsid, poster, comment) VALUES ( null, '$id', '$user_info[username]', '$comment_message')" );
		echo "<script>alert('Comment has been successfully been added to message id $id')</script>";
		echo "<script>document.location='index.php?action=viewmessage2&id=$id'</script>";
	}
//ADD SCAN
if ( isset ( $_POST['add_scan'] ) ) {
	$number = $_POST['number'];
	$title = $_POST['title'];
	$ddl = $_POST['group'];
	if ( empty ( $number ) ) {
		$errors[] = "You must enter a chapter number";
	}
	if ( empty ( $title ) ) {
		$errors[] = "You must enter a chapter title";
	}
	if ( count ( $errors ) == 1 ) {
		$title = mysql_real_escape_string ( $_POST['title'] );
		$group = mysql_real_escape_string ( $_POST['group'] );
		$insert_news = mysql_query ( "INSERT INTO manga_chapters ( id, chapternum, chaptertitle, groupid ) VALUES ( null, '$number', '$title', '$group' )" );
		echo "<script>alert('Scan item has been successfully added')</script>";
		echo "<script>document.location='index.php?action=editscans'</script>";
	}
}


//EDIT INDEX INFO, RELEASES
if ( isset ( $_POST['edit_index_info'] ) ) {
	$anime_raw = $_POST['anime_raw'];
	$anime_sub = $_POST['anime_sub'];
	$manga_raw = $_POST['manga_raw'];
	$manga_sub = $_POST['manga_sub'];

	if ( empty ( $anime_raw ) ) {
		$errors[] = "You cannot leave the Anime Raw empty";
	}
	if ( empty ( $anime_sub ) ) {
		$errors[] = "You cannot leave the Anime Sub empty";
	}
	if ( empty ( $manga_raw ) ) {
		$errors[] = "You cannot leave the Manga Raw empty";
	}
	if ( empty ( $manga_sub ) ) {
		$errors[] = "You cannot leave the Manga Sub empty";
	}
	if ( count ( $errors ) == 1 ) {
		$update_message = mysql_query ( "UPDATE index_info SET anime_raw='$anime_raw', anime_sub='$anime_sub', manga_raw='$manga_raw', manga_sub='$manga_sub'" );
		echo "<script>alert('Release Information has been successfully editted')</script>";
		echo "<script>document.location='index.php?action=releases'</script>";
	}
}
//UPADTE CHAPTER STAGES
if (isset($_GET['updatech'])) 
{
	$do = $_GET['updatech'];
	$cid = $_GET['id'];

	if ( empty ( $cid ) ) {
		$errors[] = "You need a chapter to update";
	}
	if ( count ( $errors ) == 1 && $do == 'final') {
		$update_message = mysql_query ( "UPDATE manga_chapters SET `stage` = '2', `groupid` = '0' WHERE `id` = '$cid'" );
		echo "<script>alert('Chapters has been updated to be a M7 release')</script>";
		echo "<script>document.location='index.php?action=editscans'</script>";
	}
	if ( count ( $errors ) == 1 && $do == 'speed') {
		$update_message = mysql_query ( "UPDATE manga_chapters SET `stage` = '1', groupid = '2' WHERE `id` = '$cid'" );
		echo "<script>alert('Chapters has been updated to be a speed release')</script>";
		echo "<script>document.location='index.php?action=editscans'</script>";
	}
}


//ADD ANIME
if ( isset ( $_POST['add_anime'] ) ) {
	$number = $_POST['number'];
	$title = $_POST['title'];
	$type = $_POST['type'];
	$crc = $_POST['crc'];

	if ( empty ( $number ) ) {
		$errors[] = "You must enter an episode number";
	}
	if ( empty ( $title ) ) {
		$errors[] = "You must enter an episode title";
	}
	if($type == 'sub' || $type == 'flo')
	{
	if ( empty ( $crc ) ) {
		$errors[] = "You must enter a CRC check for subs";
	}
	}
	if ( count ( $errors ) == 1 ) {
		$title = mysql_real_escape_string ( $_POST['title'] );
		$type = mysql_real_escape_string ( $_POST['type'] );
		$insert_anime = mysql_query ( "INSERT INTO episodes ( id, episode, title, type, crc ) VALUES ( null, '$number', '$title', '$type', '$crc' )" );
		echo "<script>alert('Scan item has been successfully added')</script>";
		echo "<script>document.location='index.php?action=editscans'</script>";
	}
}


//Table 
?>
<center>
<table border="0" width="80%">
 <tr>
  <td rowspan="2" width="20%" valign="top"><?php include ( "admin_cp_nav.php" ); ?>
</td>
  <td height="25" align="center"><strong>Bleach 7 Staff Control Panel</strong><br />
  <a href="http://www.bleach7.com">Back to Bleach7</a>
  </td>
 </tr>
 <tr>
  <td width="400">
  <form action="index.php" method="post">
  <?PHP
   
   
    if(isset($_GET['action']))
	{ $action = $_GET['action']; }
	else
	{ $action = 'viewmessage'; }
	
	
	if( $action == 'null')
	{
	echo 'front page!';
	}
	elseif($action == 'addnews')
	{ include("admin_news_add.php"); }
	elseif($action == 'editnews')
	{ include("admin_news_edit.php"); }
	elseif($action == 'deletenews')
	{ include("admin_news_delete.php"); }
	
	elseif($action == 'viewmessage')
	{ include("admin_messages.php"); }
	elseif($action == 'viewmessage2')
	{ include("admin_messages_view.php"); }
	elseif($action == 'addmessage')
	{ include("admin_messages_add.php"); }
	elseif($action == 'editmessage')
	{ include("admin_messages_edit.php"); }
	elseif($action == 'deletemessage')
	{ include("admin_messages_delete.php"); }
	elseif($action == 'addcomment')
	{ include("admin_messages_comments.php"); }
	elseif($action == 'addscan')
	{ include("admin_scans_add.php"); }
	elseif($action == 'releases')
	{ include("admin_index_release.php"); }
	elseif($action == 'editscans')
	{ include("admin_scans_manage.php"); }
	elseif($action == 'addanime')
	{ include("admin_anime_add.php"); }
	elseif($action == 'editanime')
	{ include("admin_anime_manage.php"); }
	elseif($action == 'galleryapprove')
	{ include("admin_gallery_approve.php"); }
	
	}
  ?>
  </form>
  </td>
 </tr>

</table>
</center>
