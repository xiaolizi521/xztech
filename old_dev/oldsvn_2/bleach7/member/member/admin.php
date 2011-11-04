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

if ( !isset ( $PHP_AUTH_USER ) && !isset ( $PHP_AUTH_PW ) ) { 
header ( "Location: index.php" );
exit();
}

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
		$insert_news = mysql_query ( "INSERT INTO news ( id, headline, poster, news ) VALUES ( $nid, '$headline', '$user_info[username]', '$news' )" );
		echo "<script>alert('News item has been successfully added')</script>";
		echo "<script>document.location='$PHP_SELF?view=main&amp;type=news&amp;action=edit'</script>";
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
		$update_news = mysql_query ( "UPDATE news SET headline='$headline', news='$news' WHERE id='$id'" );
		echo "<script>alert('News item has been successfully editted')</script>";
		echo "<script>document.location='$PHP_SELF?view=main&amp;type=news&amp;action=edit&amp;id=$id'</script>";
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
	echo "<script>document.location='$PHP_SELF?view=main&amp;type=news&amp;action=templates'</script>";
}

if ( $view == "top" ) {
	echo "<b>News</b>: 
	<a href='$PHP_SELF?view=main&amp;type=news&amp;action=add' target='main'>Add</a> | 
	<a href='$PHP_SELF?view=main&amp;type=news&amp;action=edit' target='main'>Edit</a> | 
	<a href='$PHP_SELF?view=main&amp;type=news&amp;action=delete' target='main'>Delete</a> ";
	if ( $user_info['type'] == "5" || $user_info['type'] == "90" || $user_info['type'] == "98" || $user_info['type'] == "99" ) {
		echo "| <a href='$PHP_SELF?view=main&amp;type=news&amp;action=templates' target='main'>Templates</a>";
		echo " - <b>Members</b>: 
		<a href='$PHP_SELF?view=main&amp;type=members' target='main'>Manage</a>";
	}
}

if ( $view == "footer" ) {
	echo "<b><a href='http://animereporter.com' target='_blank'>AR Memberscript Admin Panel - Created By Thomas Of Anime Reporter</a></b>";
}

if ( $view == "main" && $type == "news" ) {
	echo "<form method='post' name='form_news'>";
	if ( isset ( $action ) && !empty ( $action ) ) { //an action is set
		if ( $action == "add" ) { //add news item
			if ( count ( $errors ) > 0 ) {
				echo "<table cellpadding='0' cellspacing='0' class='main'>
					<tr>
						<td>";
				DisplayErrors();
				echo "</td>
					</tr>
				</table>
				";
			}

			echo "<table cellpadding='5' cellspacing='0' class='main'>
	<tr>
		<td>Headline</td>
		<td><input type='text' name='headline' value='$headline' style='width: 420px' class='form'></td>
	</tr>
	<tr>
		<td valign='top'>News Post</td>
		<td><textarea name='news' style='width: 420px; height: 320px' class='form'>".stripslashes ( $news )."</textarea></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'><input type='submit' name='add_news' value='Add News' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_news.reset()'></td>
	</tr>
</table>
";

		} elseif ( $action == "edit" ) { //editting news main page, display all the news items
			if ( isset ( $id ) && !empty ( $id ) ) { //editting news with an item selected
				$id = mysql_real_escape_string ( $_GET[id] );
				if ( $user_info['type'] == "20" || $user_info['type'] == "21" || $user_info['type'] == "30" || 
					$user_info['type'] == "31" || $user_info['type'] == "80" || $user_info['type'] == "90" || 
					$user_info['type'] == "98" || $user_info['type'] == "99" ) {
					$result_edit_id = mysql_query( "SELECT * FROM news WHERE id='$id'" );
				}
				$edit_id = mysql_fetch_array( $result_edit_id );
				if ( mysql_num_rows ( $result_edit_id ) > 0 ) { //a valid news id is found
					if ( count ( $errors ) > 0 ) {
						echo "<table cellpadding='0' cellspacing='0' class='main'>
							<tr>
								<td>";
						DisplayErrors();
						echo "</td>
							</tr>
						</table>
						";
					}
					echo "<table cellpadding='5' cellspacing='0' border='0' class='main'>
	<tr>
		<td>Headline</td>
		<td><input type='text' name='headline' style='width: 420px' value='".stripslashes ( $edit_id[headline] )."' class='form'></td>
	</tr>
	<tr>
		<td valign='top'>News Post</td>
		<td><textarea name='news' style='width: 420px; height: 320px' class='form'>".stripslashes ( $edit_id[news] )."</textarea></td>
	</tr>
	<tr>
		<td></td>
		<td align='center'><input type='submit' name='edit_news' value='Edit News' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_news.reset()'>   <input type='button' value='Go Back' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=news&amp;action=edit\"'></td>
	</tr>
</table>
";
				} else { //no valid news id found
					echo "<p align='center'><b>Invalid News ID</b></p>";
				}
			} else { //end editting news with an item selected
				if ( $user_info['type'] == "20" || $user_info['type'] == "21" || $user_info['type'] == "30" || $user_info['type'] == "31" 
					|| $user_info['type'] == "80" || $user_info['type'] == "90" || $user_info['type'] == "98" || $user_info['type'] == "99" ) {
					$result_edit = mysql_query( "SELECT * FROM news ORDER BY id DESC" );
				}
				$count = 1;
				echo "<table cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 50%;'>";
				while ( $edit = mysql_fetch_array( $result_edit ) ) {
					$date = DisplayDate( "$edit[id]", "l, F d, Y \A\\t h:i A", "0" );
					echo "	<tr>
							<td align='left' style='border-bottom: 1px solid #C3C3C3'>$count. <span style='text-decoration: underline;'><b>".stripslashes ( $edit['headline'] )."</b></span><br />
								- <i>Posted By $edit[poster] On $date</i></td>
							<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='$PHP_SELF?view=main&amp;type=news&amp;action=edit&amp;id=$edit[id]'>Edit</a></td>
						</tr>
						";
					$count++;
				}
				echo "</table>";
			}
		} elseif ( $action == "delete" ) { //delete news main page, display all the news items
			if ( isset ( $id ) && !empty ( $id ) ) { //delete news with an item selected
				$id = mysql_real_escape_string ( $_GET[id] );
				list ( $delete_id, $delete_username ) = explode ( ",", $id );
				$result_delete_news = mysql_query ( "SELECT id, poster FROM news WHERE id='$delete_id' AND poster='$delete_username'" );
				if ( mysql_num_rows ( $result_delete_news ) > 0 ) {
					$delete_news = mysql_query ( "DELETE FROM news WHERE id='$id' AND poster='$delete_username'" );
					$delete_comments = mysql_query ( "DELETE FROM news_comments WHERE newsid='$id'" );
					echo "<script>alert('News item has been successfully deleted')</script>";
				}
				echo "<script>document.location='$PHP_SELF?view=main&amp;type=news&amp;action=delete'</script>";
			}
			if ( $user_info['type'] == "20" || $user_info['type'] == "21" || $user_info['type'] == "30" || $user_info['type'] == "31" 
				|| $user_info['type'] == "80" ) {
				$result_delete = mysql_query( "SELECT * FROM news WHERE poster='$user_info[username]' ORDER BY id DESC" );
			} elseif ( $user_info['type'] == "5" || $user_info['type'] == "90" || $user_info['type'] == "98" || $user_info['type'] == "99" ) {
				$result_delete = mysql_query( "SELECT * FROM news ORDER BY id DESC" );
			}
			$count = 1;
			echo "<table width='50%' cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3;'>
			";
			while ( $delete = mysql_fetch_array( $result_delete ) ) {
				$date = DisplayDate( "$delete[id]", "l, F d, Y \A\\t h:i A", "0" );
				echo "	<tr>
						<td align='left' style='border-bottom: 1px solid #C3C3C3'>$count. <span style='text-decoration: underline;'><b>".stripslashes ( $delete['headline'] )."</b></span><br />
							- <i>Posted By $delete[poster] On $date</i></td>
						<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='#delete' onclick='DeleteNews( \"$PHP_SELF?view=main&amp;type=news&amp;action=delete&amp;id=$delete[id],$delete[poster]\" )'>Delete</a></td>
					</tr>";
				$count++;
			}
			echo "</table>";
		} elseif ( $action == "templates" && $user_info[type] == 5 ) { //templates main page, display all templates
			if ( isset ( $id ) && !empty ( $id ) ) {
				if ( $id == "news" || $id == "comments" || $id == "pm" ) {
					if ( $restore == "true" ) {
						$template_content = file_get_contents ( "../templates/default/$id.php" );
					} else {
						$template_content = file_get_contents ( "../templates/$id.php" );
					}
				}
				$template_content = str_replace ( "<?php", "", $template_content );
				$template_content = str_replace ( "echo \"", "", $template_content );
				$template_content = str_replace ( "\";", "", $template_content );
				$template_content = str_replace ( "?>", "", $template_content );
				$template_array = array ( "news" => "News", "comments" => "Comments", "pm" => "Private Messaging" );
				echo "Template: $template_array[$id]";
				echo "<table style='height: 15px' cellpadding='0' cellspacing='0'>
					<tr>
						<td></td>
					</tr>
				</table>
				";
				echo "<table cellpadding='5' cellspacing='0' class='main'>
					<tr>
						<td valign='top'>Variables</td>
						<td><i>Place whatever variables you want, wherever you wish.<br />
						Just make sure that you <span style='text-decoration: underline;'><b>only use single quotes</b></span>.</i>
						<table style='height: 5px;' cellpadding='0' cellspacing='0'>
							<tr>
								<td></td>
							</tr>
						</table>";
				if ( $id == "news" ) {
					echo "
						\$headline - The news headline<br />
						\$news - The news post<br />
						\$poster - The username of the person who posted the news<br />
						\$date - The date that the news was posted<br />
						\$comments - The number of comments that the news has<br />
						";
				} elseif ( $id == "comments" ) {
					echo "
						\$member_username - The member's username<br />
						\$comment_date - The date that the comment was posted<br />
						\$comment_postnum - The comment post number<br />
						\$member_avatar - The member's avatar<br />
						\$member_type - The member's status (Webmaster, Administrator, Staff, M7 Team, Info Team, <br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Moderator, Privileged Member, Member)<br />
						\$member_joindate - The member's registered date<br />
						\$member_posts - The member's post count<br />
						\$member_number - The member's number<br />
						\$member_online - The member's online status (Online, Offline)<br />
						\$comment_options - The options for the comments (Delete, Edit, Quote)<br />
						\$comment - The actual comment<br />
						";
				} elseif ( $id == "pm" ) {
					echo "
						\$member_username - The member's username<br />
						\$pm_date - The date that the private message was sent<br />
						\$pm_options - The private message options (Reply, Delete)<br />
						\$member_avatar - The member's avatar<br />
						\$member_type - The member's status (Webmaster, Administrator, Staff, M7 Team, Info Team, <br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Moderator, Privileged Member, Member)<br />
						\$member_joindate - The member's registered date<br />
						\$member_posts - The member's post count<br />
						\$member_number - The member's number<br />
						\$member_online - The member's online status (Online, Offline)<br />
						\$pm_subject - The subject of the private message<br />
						\$pm_message - The actual message<br />
						";
				}
				echo "</td>
					</tr>
					<tr>
						<td valign='top'>Template</td>
						<td><textarea name='template' style='width: 420px; height: 270px' class='form'>".trim ( $template_content )."</textarea></td>
					</tr>
					<tr>
						<td></td>
						<td align='center'><input type='submit' name='edit_template' value='Edit Template' class='form'>   <input type='button' value='Restore Default' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=news&amp;action=templates&amp;id=$id&restore=true\"'>   <input type='button' value='Go Back' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=news&amp;action=templates\"'></td>
					</tr>
				</table>";
			} else {
			echo "<table width='50%' cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3'>
			";
			echo "	<tr>
					<td align='left' style='border-bottom: 1px solid #C3C3C3'>1. <span style='text-decoration: underline;'><b>News</b></span><br />
						- <i>The template for news</i></td><td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='$PHP_SELF?view=main&amp;type=news&amp;action=templates&amp;id=news'>Edit</a></td>
				</tr>
				";
			echo "	<tr>
					<td align='left' style='border-bottom: 1px solid #C3C3C3'>2. <span style='text-decoration: underline;'><b>Comments</b></span><br/>
						- <i>The template for comments</i></td>
					<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='$PHP_SELF?view=main&amp;type=news&amp;action=templates&amp;id=comments'>Edit</a></td>
				</tr>";
			echo "	<tr>
					<td align='left' style='border-bottom: 1px solid #C3C3C3'>3. <span style='text-decoration: underline;'><b>Private Messaging</b></span><br/>
						- <i>The template for recieved private messages</i></td>
					<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='$PHP_SELF?view=main&amp;type=news&amp;action=templates&amp;id=pm'>Edit</a></td>
				</tr>
				";
			echo "</table>";
			}
		}
	} 
	echo "</form>";
} elseif ( $view == "main" && $type == "members" && $user_info['type'] == "5" 
			|| $user_info['type'] == "90" || $user_info['type'] == "98" || $user_info['type'] == "99" ) {

	$username = mysql_real_escape_string ( $_GET[username] );

	echo "<form method='post' name='form_member'>";

	if ( isset ( $_POST[member_edit] ) ) {
		$member_username = mysql_real_escape_string ( $_POST[member_username] );
		$member_posts = mysql_real_escape_string ( $_POST[member_posts] );
		$update_member = mysql_query ( "UPDATE users SET username='$member_username', type='$member_type', posts='$member_posts' WHERE username='$username'" );
		echo "<script>alert('Member: $username has been successfully editted')</script>";
		echo "<script>document.location='$PHP_SELF?view=main&amp;type=members&username=$username'</script>";
	}

	if ( isset ( $_POST[member_delete] ) ) {
		$member_delete = mysql_query( "DELETE FROM users WHERE username='$username'" );;
		echo "<script>alert('Member: $username has been successfully deleted')</script>";
		echo "<script>document.location='$PHP_SELF?view=main&amp;type=members'</script>";
	}

	if ( isset ( $username ) && !empty ( $username ) ) {
		$result_member = mysql_query ( "SELECT * FROM users WHERE username='$username'" );
		if ( mysql_num_rows ( $result_member ) > 0 ) {
			$member = mysql_fetch_array ( $result_member );
			echo "<table cellpadding='5' cellspacing='0' border='0' class='main'>
				<tr>
					<td>Username</td>
					<td><input type='text' name='member_username' style='width: 114px' value='".stripslashes ( $member[username] )."' class='form' readonly='true'></td>
				</tr>
				<tr>
					<td>Status</td>";
			if ( $user_info['type'] == "90" ) {
				if ( $member['type'] == "98" ) {
					echo "<td>$rank98</td>
					</tr>
					<tr>
						<td>Posts</td>
						<td>$member[posts]</td>
					</tr>";
				}elseif ( $member['type'] == "99" ) {
					echo "<td>$rank99</td>
					</tr>
					<tr>
						<td>Posts</td>
						<td>$member[posts]</td>
					</tr>";
				}else {
					echo "<td><select name='member_type' class='form'>";
?>
<option value="1" <?php if ( $member['type'] == "1" ) { echo "selected"; } ?>>Member</option>
<option value="2" <?php if ( $member['type'] == "2" ) { echo "selected"; } ?>>Privileged Member</option>
<option value="10" <?php if ( $member['type'] == "10" ) { echo "selected"; } ?>>Moderator</option>
<option value="20" <?php if ( $member['type'] == "20" ) { echo "selected"; } ?>>Info Team</option>
<option value="21" <?php if ( $member['type'] == "21" ) { echo "selected"; } ?>>Info Team | Mod</option>
<option value="30" <?php if ( $member['type'] == "30" ) { echo "selected"; } ?>>M7 Team</option>
<option value="31" <?php if ( $member['type'] == "31" ) { echo "selected"; } ?>>M7 Team | Mod</option>
<option value="80" <?php if ( $member['type'] == "80" ) { echo "selected"; } ?>>Staff Member</option>
<option value="90" <?php if ( $member['type'] == "90" ) { echo "selected"; } ?>>Administrator</option>
<?php
					echo "</select></td>
				</tr>
				<tr>
					<td>Posts</td>
					<td><input type='text' name='member_posts' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form'></td>
				</tr>";
				}
			}elseif ( $user_info['type'] == "98" ) {
				if ( $member['type'] == "99" ) {
					echo "<td>$rank99</td>
					</tr>
					<tr>
						<td>Posts</td>
						<td>$member[posts]</td>
					</tr>";
				}else {
					echo "<td><select name='member_type' class='form'>";
?>
<option value="1" <?php if ( $member['type'] == "1" ) { echo "selected"; } ?>>Member</option>
<option value="2" <?php if ( $member['type'] == "2" ) { echo "selected"; } ?>>Privileged Member</option>
<option value="10" <?php if ( $member['type'] == "10" ) { echo "selected"; } ?>>Moderator</option>
<option value="20" <?php if ( $member['type'] == "20" ) { echo "selected"; } ?>>Info Team</option>
<option value="21" <?php if ( $member['type'] == "21" ) { echo "selected"; } ?>>Info Team | Mod</option>
<option value="30" <?php if ( $member['type'] == "30" ) { echo "selected"; } ?>>M7 Team</option>
<option value="31" <?php if ( $member['type'] == "31" ) { echo "selected"; } ?>>M7 Team | Mod</option>
<option value="80" <?php if ( $member['type'] == "80" ) { echo "selected"; } ?>>Staff Member</option>
<option value="90" <?php if ( $member['type'] == "90" ) { echo "selected"; } ?>>Administrator</option>
<option value="98" <?php if ( $member['type'] == "98" ) { echo "selected"; } ?>>Webmaster</option>
<?php
					echo "</select></td>
				</tr>
				<tr>
					<td>Posts</td>
					<td><input type='text' name='member_posts' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form'></td>
				</tr>";
				}
			}elseif ( $user_info['type'] == "99" ) {
				echo "<td><select name='member_type' class='form'>";
?>
<option value="1" <?php if ( $member['type'] == "1" ) { echo "selected"; } ?>>Member</option>
<option value="2" <?php if ( $member['type'] == "2" ) { echo "selected"; } ?>>Privileged Member</option>
<option value="10" <?php if ( $member['type'] == "10" ) { echo "selected"; } ?>>Moderator</option>
<option value="20" <?php if ( $member['type'] == "20" ) { echo "selected"; } ?>>Info Team</option>
<option value="21" <?php if ( $member['type'] == "21" ) { echo "selected"; } ?>>Info Team | Mod</option>
<option value="30" <?php if ( $member['type'] == "30" ) { echo "selected"; } ?>>M7 Team</option>
<option value="31" <?php if ( $member['type'] == "31" ) { echo "selected"; } ?>>M7 Team | Mod</option>
<option value="80" <?php if ( $member['type'] == "80" ) { echo "selected"; } ?>>Staff Member</option>
<option value="90" <?php if ( $member['type'] == "90" ) { echo "selected"; } ?>>Administrator</option>
<option value="98" <?php if ( $member['type'] == "98" ) { echo "selected"; } ?>>Webmaster</option>
<option value="99" <?php if ( $member['type'] == "99" ) { echo "selected"; } ?>>Sensei</option>
<?php
				echo "</select></td>
				</tr>
				<tr>
					<td>Posts</td>
					<td><input type='text' name='member_posts' style='width: 114px' value='".stripslashes ( $member[posts] )."' class='form'></td>
				</tr>";
			}
				echo "<tr>
					<td>Registered</td>
					<td>".DisplayDate( "$member[registered_on]", "F d Y, h:i A", "1" )."</td>
				</tr>
				<tr>
					<td>IP Address</td>
					<td>$member[ip_address]</td>
				</tr>
			</table>
			<table style='height: 5px' cellpadding='0' cellspacing='0'>
				<tr>
					<td></td>
				</tr>
			</table>
			<input type='submit' name='member_edit' value='Edit Member' class='form'>   <input type='button' value='New Search' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=members\"'>
			";
		} else {
			echo "There are no members in the database with that username<br />
				<a href='$PHP_SELF?view=main&amp;type=members'>Go back</a>";
		}
	} else {
		echo "Enter Member's Username
		<table style='height: 5px' cellpadding='0' cellspacing='0'>
			<tr>
				<td></td>
			</tr>
		</table>
		<input type='text' name='member_username' class='form' style='width: 300px'>
		<table style='height: 5px' cellpadding='0' cellspacing='0'>
				<tr>
				<td></td>
			</tr>
		</table>
		<input type='button' value='Search Member' class='form' onclick='document.form_member.submit()'>   <input type='button' value='New Search' class='form' onclick='document.form_member.reset()'>
		";
		echo "<input type='hidden' name='member_search'>
		";
		echo "<table height='20' cellpadding='0' cellspacing='0'>
			<tr>
				<td></td>
			</tr>
		</table>
		";
	}

	if ( isset ( $_POST[member_search] ) ) {
		$member_username = mysql_real_escape_string ( $_POST[member_username] );
		if ( empty ( $member_username ) ) {
			echo "Please enter a member's username";
		} else {
			$result_search = mysql_query ( "SELECT * FROM users WHERE username LIKE '%$member_username%' ORDER BY username" );
			if ( mysql_num_rows ( $result_search ) > 0 ) {
				echo "<table cellpadding='5' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 300px;'>
				";
				while ( $search = mysql_fetch_array ( $result_search ) ) {
					echo "	<tr>
							<td align='left' style='border-bottom: 1px solid #C3C3C3'>$search[username]</td><td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='$PHP_SELF?view=main&amp;type=members&amp;username=$search[username]'>Edit</a></td>
						</tr>
						";
				}
				echo "</table>
				";
			} else {
				echo "There are no members in the database with that username";
			}
		}
	}
	echo "</form>";
}
?> 