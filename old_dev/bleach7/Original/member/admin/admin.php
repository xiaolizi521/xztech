<?php 
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime poster - http://animeposter.com  #
# Copyright Anime poster. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

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

/*if ( !isset ( $enter_username ) && !isset ( $enter_password ) ) { 
header ( "Location: index.php" );
exit();
}*/

$view = (isset($_GET['view']) ? $_GET['view'] : '');
$type = (isset($_GET['type']) ? $_GET['type'] : '');
$action = (isset($_GET['action']) ? $_GET['action'] : '');




include ( "../rank.php" );
include ( "../settings.php" );
include ( "../functions.php" );

if ( $view != "footer" ) {
	include ( "../db.php" );
	if ( !isset ( $user_info['user_id'] ) ) {
		header ( "Location: $site_url/$main_filename?$ident=$script_folder/login" );
		exit();
	}
}

include ( "header.php" );

if ( isset ( $_POST['add_news'] ) ) {
	if ( empty ( $headline ) ) {
		$errors[] = "You must enter a headline";
	}
	if ( empty ( $news ) ) {
		$errors[] = "You must enter a news post";
	}
	if ( count ( $errors ) == 0 ) {
		$headline = mysql_real_escape_string ( htmlspecialchars ( $_POST[headline], ENT_QUOTES ) );
		$news = mysql_real_escape_string ( $_POST[news] );
		$insert_news = mysql_query ( "INSERT INTO news ( id, headline, poster, news, category ) VALUES ( $nid, '$headline', '$user_info[username]', '$news', '$category' )" );
		echo "<script>alert('News item has been successfully added')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=news&amp;action=edit'</script>";
	}
}

if ( isset ( $_POST['edit_news'] ) ) {
	if ( empty ( $headline ) ) {
		$errors[] = "You cannot leave the headline empty";
	}
	if ( empty ( $news ) ) {
		$errors[] = "You cannot leave the news post empty";
	}
	if ( count ( $errors ) == 0 ) {
		$headline = mysql_real_escape_string ( htmlspecialchars ( $_POST[headline], ENT_QUOTES ) );
		$news = mysql_real_escape_string ( $_POST[news] );
		$update_news = mysql_query ( "UPDATE news SET headline='$headline', news='$news', category='$category' WHERE id='$id'" );
		echo "<script>alert('News item has been successfully editted')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=news&amp;action=edit&amp;id=$id'</script>";
	}
}

if ( isset ( $_POST['message_add'] ) ) {
	if ( empty ( $message_headline ) ) {
		$errors[] = "You must enter a headline";
	}
	if ( empty ( $message_message ) ) {
		$errors[] = "You must enter a news post";
	}
	if ( count ( $errors ) == 0 ) {
		$message_headline = mysql_real_escape_string ( htmlspecialchars ( $_POST[message_headline], ENT_QUOTES ) );
		$message_message = mysql_real_escape_string ( $_POST[message_message] );
		$insert_news = mysql_query ( "INSERT INTO admin_message ( id, headline, poster, message, rank ) VALUES ( $nid, '$message_headline', '$user_info[username]', '$message_message', '$rank_available' )" );
		echo "<script>alert('Message item has been successfully added')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=messages'</script>";
	}
}

if ( isset ( $_POST['message_edit'] ) ) {
	if ( empty ( $message_headline ) ) {
		$errors[] = "You cannot leave the headline empty";
	}
	if ( empty ( $message_message ) ) {
		$errors[] = "You cannot leave the message part empty";
	}
	if ( count ( $errors ) == 0 ) {
		$update_message = mysql_query ( "UPDATE admin_message SET headline='$message_headline', message='$message_message', rank='$rank_available' WHERE id='$id'" );
		echo "<script>alert('Message has been successfully editted')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=messages'</script>";
	}
}

if ( isset ( $_POST['edit_template'] ) ) {
	$template_array = array ( "news" => "News", "comments" => "Comments", "pm" => "Private Messaging" );
	$template_write = stripslashes ( $_POST[template] );
	( $fp = fopen ( "../templates/$id.php", "w" ) ) or die ( "couldn't open" );
	fwrite ( $fp, "<?php\n" ); 
	fwrite ( $fp, "echo \"\n" ); 
	fwrite ( $fp, "$template_write" ); 
	fwrite ( $fp, "\n\";\n" ); 
	fwrite ( $fp, "?>\n" ); 
	fclose ( $fp );
	echo "<script>alert('The $template_array[$id] template has been successfully editted')</script>";
	echo "<script>document.location='index.php?view=main&amp;type=news&amp;action=templates'</script>";
}

if ( isset ( $_POST['edit_index_info'] ) ) {
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
	if ( count ( $errors ) == 0 ) {
		$update_message = mysql_query ( "UPDATE index_info SET anime_raw='$anime_raw', anime_sub='$anime_sub', manga_raw='$manga_raw', manga_sub='$manga_sub'" );
		echo "<script>alert('Release Information has been successfully editted')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=index&amp;action=release'</script>";
	}
}

if ( isset ( $_POST['edit_donation_info'] ) ) {
	if ( empty ( $donations ) ) {
		$errors[] = "You cannot leave the Donations empty";
	}
	if ( empty ( $main_server ) ) {
		$errors[] = "You cannot leave the Main Server empty";
	}
	if ( empty ( $media1 ) ) {
		$errors[] = "You cannot leave the Media 1 empty";
	}
	if ( empty ( $media2 ) ) {
		$errors[] = "You cannot leave the Media 2 empty";
	}
	if ( count ( $errors ) == 0 ) {
		$update_message = mysql_query ( "UPDATE index_info SET goal='$donations', main_server='$main_server', media1='$media1', media2='$media2'" );
		echo "<script>alert('Donation Information has been successfully editted')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=index&amp;action=donations'</script>";
	}
}

if ( isset ( $_POST['new_month'] ) ) {
	if ( empty ( $month ) ) {
		$errors[] = "You cannot leave the Month empty";
	}
	if ( empty ( $year ) ) {
		$errors[] = "You cannot leave the Year empty";
	}
	if ( $month < 12 )
		$month = $month + 1;
	else {
		$month = 1;
		$year = $year + 1;
	}
	if ( count ( $errors ) == 0 ) {
		$update_message = mysql_query ( "UPDATE index_info SET month='$month', year='$year'" );
		echo "<script>alert('New Month has been successfully editted')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=index&amp;action=donations'</script>";
	}
}

if ( isset ( $_POST['add_donator'] ) ) {
	if ( empty ( $donator ) ) {
		$errors[] = "You cannot leave the Donator's Name empty";
	}
	if ( empty ( $amount ) ) {
		$errors[] = "You cannot leave the Amount empty";
	}
	if ( count ( $errors ) == 0 ) {
		$result_donation_info = mysql_query( "SELECT month, year FROM index_info" );
		$donation_info = mysql_fetch_array( $result_donation_info );
		$update_news = mysql_query ( "INSERT INTO donator ( id, month, year, donator, amount ) VALUES ( '$nid', '$donation_info[month]', '$donation_info[year]', '$donator', '$amount' )" );
		echo "<script>alert('New Month has been successfully editted')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=index&amp;action=donations'</script>";
	}
}

if ( isset ( $_POST['edit_donator'] ) ) {
	if ( empty ( $donator ) ) {
		$errors[] = "You cannot leave the Donator's Name empty";
	}
	if ( empty ( $amount ) ) {
		$errors[] = "You cannot leave the Amount empty";
	}
	if ( count ( $errors ) == 0 ) {
		$update_message = mysql_query ( "UPDATE donator SET donator='$donator', amount='$amount' WHERE id='$id'" );
		echo "<script>alert('New Month has been successfully editted')</script>";
		echo "<script>document.location='index.php?view=main&amp;type=index&amp;action=donations'</script>";
	}
}

if ( $view == "top" ) {
	echo "<b>News</b>: 
	<a href='index.php?view=main&amp;type=news&amp;action=add' target='main'>Add</a> | 
	<a href='index.php?view=main&amp;type=news&amp;action=edit' target='main'>Edit</a> | 
	<a href='index.php?view=main&amp;type=news&amp;action=delete' target='main'>Delete</a> - 
	<b>Staff Messages:</b> <a href='index.php?view=main&amp;type=messages' target='main'>View</a>";
	if ( "90" <= $user_info['type'] ) {
		echo " - <b>Admin:</b> <a href='index.php?view=main&amp;type=news&amp;action=templates' target='main'>Templates</a>	| 
		<a href='index.php?view=main&amp;type=index' target='main'>Index Info</a>";
		echo " - <b>Members</b>: 
		<a href='index.php?view=main&amp;type=members' target='main'>Manage</a>";
	}
}

if ( $view == "footer" ) {
	echo "<b><a href='http://animereporter.com' target='_blank'>AR Memberscript Admin Panel - Created By Thomas Of Anime Reporter</a></b>";
}

$type = 'index';
if ( $view == "main" && $type == "news" ) {
	include ( "admin_news.php" );
} elseif ( $view == "main" && $type == "messages" ) {
	include ( "admin_messages.php" );
} elseif ( $view == "main" && $type == "members" && $user_info['type'] >= "90" ) {
	include ( "admin_member.php" );
} elseif ( $view == "main" && $type == "index" ) {
	include ( "admin_index.php" );
}

?> 