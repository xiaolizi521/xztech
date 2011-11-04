
<?php 
$id = mysql_real_escape_string ( $_GET['id'] );
$pg = mysql_real_escape_string ( $_GET['pg'] ); ?>
<script>
click_count = 0;

function DeleteComment( URL ) {
    if (confirm('Are you sure you want to delete this comment?')) {
        document.location=URL;
        return true;
    }
    else {
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
	document.comment_form.comment_post.value += ' :'+expression+' ';
}

function InsertBold() {
	document.comment_form.comment_post.value += ' [b] [/b] ';
}

function InsertItalic() {
	document.comment_form.comment_post.value += ' [i] [/i] ';
}

function InsertUnderline() {
	document.comment_form.comment_post.value += ' [u] [/u] ';
}

function InsertSpoiler() {
	document.comment_form.comment_post.value += ' [spoiler] [/spoiler] ';
}

function InsertURL() {
	urllink = prompt ("Enter the url you want to insert.");
	urltext = prompt ("Enter the text you want to have in place of the url");
	document.comment_form.comment_post.value += ' [url='+urllink+']'+urltext+'[/url] ';
}

function InsertColor() {
	colorlink = prompt ("Enter the color you want to insert.");
	colortext = prompt ("Enter the text you want to have in place of the color");
	document.comment_form.comment_post.value += ' [color='+colorlink+']'+colortext+'[/color] ';
}

function InsertHL() {
	hllink = prompt ("Enter the highlight you want to insert.");
	hltext = prompt ("Enter the text you want to have in place of the highlight");
	document.comment_form.comment_post.value += ' [hl='+hllink+']'+hltext+'[/hl] ';
}

function ViewAllSmilies() {
	window.open("<?php echo "$site_url/$script_folder/smilies.php"
	?>","legend","width=170,height=500,left=0,top=0,resizable=yes,scrollbars=yes"); 
}
</script>

<?php
function CommentOption( $id, $username, $type ) {
	global $site_path;
	if ( $type == "delete" ) {
		$type_url = "<a href='#delete' onclick='javascript:DeleteComment(\"$site_path/comments&amp;id=$_GET[id]&amp;pg=$_GET[pg]&amp;$type=$id,$username\")'>".ucwords( $type )."</a>";
	}elseif ($type == "ban"){

			$type_url = "<a href='$site_path/bancomments&amp;user=$username'>".ucwords( $type )."</a>";
	}
	else {
		$type_url = "<a href='$site_path/comments&amp;id=$_GET[id]&amp;pg=$_GET[pg]&amp;$type=$id,$username#comment'>".ucwords( $type )."</a>";
	}
	return $type_url;
}

// What happens when someone inserts a post
if ( isset ( $_POST['submit_comment'] ) ) {
	$comment_post = mysql_real_escape_string ( htmlspecialchars ( $_POST[comment_post], ENT_QUOTES ) );
	$update_comment_num = mysql_query ( "UPDATE admin_message SET comments=(comments+1) WHERE id='$id'" );
	$update_user_posts = mysql_query ( "UPDATE users SET posts=(posts+1) WHERE username='$user_info[username]'" );
	$insert_comment = mysql_query ( "INSERT INTO admin_comments ( id, newsid, poster, comment )
		VALUES ( $nid, $id, '$user_info[username]', '$comment_post' )" );
	if ( $pages_num == 0 ) {
		echo "<script>document.location='$site_path/$script_folder/admin/admin.php?view=main&type=messages&action=view&id=$id&pg=1'</script>";
	}
	else {
		echo "<script>document.location='$site_path/$script_folder/admin/admin.php?view=main&type=messages&action=view&id=$id&pg=$pg'</script>";
	}
}

// What happens when someone edits a post
if ( isset ( $_POST['edit_comment'] ) ) {
	$comment_post = mysql_real_escape_string ( htmlspecialchars ( $_POST[comment_post], ENT_QUOTES ) );
	list ( $edit_id ) = explode ( ",", $edit );
	$update_comment = mysql_query ( "UPDATE admin_comments SET comment='$comment_post' WHERE newsid='$id' AND id='$edit_id'" );
	echo "<script>document.location='$site_path/$script_folder/admin/admin.php?view=main&type=messages&action=view&id=$id&pg==$pg'</script>";
}

// What happens when someone deletes a post
if ( isset ( $delete ) && !empty ( $delete ) ) {
	list ( $delete_id, $delete_username ) = explode ( ",", $delete );
	$result_delete_post = mysql_query ( "SELECT admin_comments.id, admin_comments.newsid, admin_comments.comment, users.username FROM admin_comments LEFT JOIN users ON (admin_comments.poster=users.username) WHERE admin_comments.newsid='$id' AND admin_comments.id='$delete_id' AND users.username='$delete_username'" );
	if ( mysql_num_rows ( $result_delete_post ) > 0 ) {
		$delete_post = mysql_query ( "DELETE FROM admin_comments WHERE newsid='$id' AND id='$delete_id'" );
		$update_comments_num = mysql_query ( "UPDATE admin_message SET comments=(comments-1) WHERE id='$id'" );
		if ( $pages_num == 0 ) {
			echo "<script>document.location='$site_path/$script_folder/admin/admin.php?view=main&type=messages&action=view&id=$id&pg=1'</script>";
		}
		else {
			echo "<script>document.location='$site_path/$script_folder/admin/admin.php?view=main&type=messages&action=view&id=$id&pg=$pages_num'</script>";
		}
	} 
}

// Checking if any comments have been made
if ( $comments_num == 0 ) {
	echo "<p align='center'>No comments have been made.";
	if ( isset ( $user_info[user_id] ) ) {
		echo " Be the first by entering a comment below.</p><p>
		";
	}
	else {
		echo "</p><p>";
	}
	echo "</p>";
}
else {
	if ( !isset ( $pg ) || empty ( $pg ) || $pg < 1 ) {
		header ( "Location: $site_path/comments&amp;id=$id&amp;pg=1" );
		ob_end_flush();
	}
	elseif ( $pg > $pages_num ) {
		header ( "Location: $site_path/comments&amp;id=$id&amp;pg=$pages_num" );
		ob_end_flush();
	}
	if ( isset ( $pg ) && !empty ( $pg ) && ( $pg > 0 ) && ( $pg <= $pages_num ) ) {
		$offset = ( ( $pg * $limit ) - $limit );
	}
	else {
		if ( $pg <= 0 ) {
			$offset = 0;
			$pg = 1;
		}
		elseif ( $pg > $pages_num ) {
			$offset = ( ( $pages_num * $limit ) - $limit );
			$pg = $pages_num;
		}
	}
	
	$result_comments = mysql_query ( "SELECT admin_comments.*, users.* FROM admin_comments LEFT JOIN users ON (users.username=admin_comments.poster) WHERE admin_comments.newsid=$id ORDER BY admin_comments.id ASC " );
print_r($result_comments);
	//Paginate( "pg", "$pages_num", "comments&amp;id=$_GET[id]" );

	$comment_num = ( $offset + 1 );
	while ( $show_comments = mysql_fetch_array ( $result_comments ) ) {
		// Gathering each member's information
		
		$member_username = "<a href='$site_path/member&amp;id=$show_comments[username]'><b><u>$show_comments[username]</u></b></a>";
		$comment_date = DisplayDate( "$show_comments[id]", "M d Y, h:i A", "1" );
		$comment_postnum = "Post #$comment_num";
		

        	//check if the user is banned or not.
			    //if the function returns true (if a row is sent back) show the pwned picture
				if (isbanned($show_comments['user_id'] )) {
			  
			  $member_avatar = "<img src='../member/images/avatars/pwnd.jpg' alt='pwnd' width='60' height='60'>";

				} else {


        // displaying each member's image
		if ( empty ( $show_comments['avatar'] ) ) {
			$member_avatar = "<img src='$site_url/$script_folder/images/avatars/none.gif' alt='$show_comments[username]' width='60' height='60' />";
		}
		else{
			list ( $avatar_width, $avatar_height ) = getimagesize ( "$show_comments[avatar]" );
			// resizing images if they are over 60 x 60 pixels.
			if ( $avatar_width > 60 || $avatar_height > 60 ) {
				$member_avatar = "<img src='$show_comments[avatar]' alt='$show_comments[username]' width='60' height='60' />";
			}
			else {
				$member_avatar = "<img src='$show_comments[avatar]' alt='$show_comments[username]' />";
			}
		} 
  }
		// determine the member type
		if ( $show_comments['type'] == 1 || $show_comments['type'] == 2 ) {
			// if member type is either 1 or 2: either a regular "Member" or "Privileged Member"
		    $member_type = "Member";
		}
		elseif ( $show_comments['type'] == 10 ) {
			// if member type is between 10 and 19, they are a "Moderator"
			$member_type = "Moderator";
		}
		elseif ( $show_comments['type'] == 20 ) {
			// if member type is between 20 and 29, they are part of the "Info Team"
			$member_type = "Info Team";
		}
		elseif ( $show_comments['type'] == 21 ) {
			// if member type is between 20 and 29, they are part of the "Info Team"
			$member_type = "Info Team | Mod";
		}
		elseif ( $show_comments['type'] == 30 ) {
			// if member type is between 50 and 59, they are part of the "Max7 Team"
			$member_type = "M7 Team";
		}
		elseif ( $show_comments['type'] == 31 ) {
			// if member type is between 50 and 59, they are part of the "Max7 Team"
			$member_type = "M7 Team | Mod";
		}
		elseif ( $show_comments['type'] == 80 ) {
			// if member type is between 80 and 89, they are a "Staff Member"
			$member_type = "Staff Member";
		}
		elseif ( $show_comments['type'] == 81 ) {
			// if member type is between 80 and 89, they are a "Staff Member"
			$member_type = "Staff Member | M7";
		}
		elseif ( $show_comments['type'] == 90 ) {
			// if member type is between 90 and 97, they are an "Administrator"
			$member_type = "Administrator";
		}
		elseif ( $show_comments['type'] == 91 ) {
			// if member type is between 90 and 97, they are an "Administrator"
			$member_type = "Admin | M7 Team";
		}
		elseif ( $show_comments['type'] == 98 ) {
			// if member type is 98, they are a "Webmaster"
			$member_type = "Webmaster";
		}
		elseif ( $show_comments['type'] == 99 ) {
			// if member type is 99, they are a "Head-Webmaster" or "Sensei"
			$member_type = "Sensei";
		}

		// Figure out what each member's rank is based off of their post count
		if ( $show_comments['posts'] >= 1000 ) {
			// if post count is greater than 1000, rank is "Captain"
			$member_rank = "Captain";
		}
		elseif ( $show_comments['posts'] >= 500 ) {
			// if post count is between 500 and 999, rank is "Vice-Captain"
			$member_rank = "Vice-Captain";
		}
		elseif ( $show_comments['posts'] >= 100 ) {
			// if post count is between 100 and 499, rank is "Shinigami"
			$member_rank = "Shinigami";
		}
		elseif ( $show_comments['posts'] >= 50 ) {
			// if post count is between 50 and 99, rank is "Hollow"
			$member_rank = "Hollow";
		}
		elseif ( $show_comments['posts'] >= 25 ) {
			// if post count is between 25 and 49, rank is "Demi-Hollow"
			$member_rank = "Demi-Hollow";
		}
		elseif ( $show_comments['posts'] >= 10 ) {
			// if post count is between 10 and 24, rank is "Human"
			$member_rank = "Human";
		}
		else {
			// if post count is between 0 and 9, rank is "Spiritless"
			$member_rank = "Spiritless";
		}

		// add "Rank:" to the beginning of the member rank
		$member_rank = "Rank: $member_rank";

		// aquire the join data and add "Joined: " at the beginning
		$joindate = DisplayDate( "$show_comments[registered_on]", "m/d/y", "0" );
		$member_joindate = "Joined: $joindate";

		// add User infomation, such as post count and member number
		$member_posts = "Posts: $show_comments[posts]";
		$member_num = "Member No. $show_comments[user_id]";

		// determine if the member is online or not
		if ( ( time() - $show_comments['last_activity_time'] ) <= 300 ) {
			$member_online = "Status: <font color='green'>Online</font>";
		}
		else {
			$member_online = "Status: <font color='red'>Offline</font>";
		}

		// add the links at the top of each post
		$ban_user = CommentOption( "$show_comments[id]", "$show_comments[username]", "ban" );
		$delete_link = CommentOption( "$show_comments[id]", "$show_comments[username]", "delete" );
		$edit_link = CommentOption( "$show_comments[id]", "$show_comments[username]", "edit");
		$quote_link = CommentOption( "$show_comments[id]", "$show_comments[username]", "quote" );
		
		// if the user is a moderator, staff member, or administrator,
		// they are able to delete or edit any bodies posts
		if ( isset ( $user_info['user_id'] ) ) {
			// information for moderators
			if ( $user_info['type'] == 10 || $user_info['type'] == 21 || $user_info['type'] == 31 ) {
				// if the commenter is also a moderator
				if ( $show_comments['type'] == 10 || $show_comments['type'] == 21 || $show_comments['type'] == 31 ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is a Staff Member or Admin
				elseif ( 80 <= $show_comments['type'] && $show_comments['type'] <= 99) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is the user posting
				elseif ( $show_comments['user_id'] == $user_info['user_id'] ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				else {
					$comment_options1 = "$delete_link | $ban_user | $edit_link |";
				}
			}
			// information for Staff Members
			elseif ( $user_info['type'] == 80 || $user_info['type'] == 81 ) {
				// if the commenter is also a Staff Member
				if ( $show_comments['type'] == 80 || $show_comments['type'] == 81 ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is an Admin
				elseif ( 90 <= $show_comments['type'] && $show_comments['type'] <= 99) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is the user posting
				elseif ( $show_comments['user_id'] == $user_info['user_id'] ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				else {
					$comment_options1 = "$delete_link | $ban_user | $edit_link |";
				}
			}
			// information for Admins
			elseif ( $user_info['type'] == 90 || $user_info['type'] == 91 ) {
				// if the commenter is also a Admin
				if ( $show_comments['type'] == 90 || $show_comments['type'] == 91 ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is a Webmaster or Owner
				elseif ( $show_comments['type'] == 98 || $show_comments['type'] == 99) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is the user posting
				elseif ( $show_comments['user_id'] == $user_info['user_id'] ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				else {
					$comment_options1 = "$delete_link | $ban_user | $edit_link |";
				}
			}
			// information for Webmasters
			elseif ( $user_info['type'] == 98 ) {
				// if the commenter is also an Admin
				if ( $show_comments['type'] == 98 ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is an Owner
				elseif ( $show_comments['type'] == 99 ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is the user posting
				elseif ( $show_comments['user_id'] == $user_info['user_id'] ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				else {
					$comment_options1 = "$delete_link | $ban_user | $edit_link |";
				}
			}
			elseif ( $user_info['type'] == 99 ) {
				// if the commenter is also an Owner
				if ( $show_comments['type'] == 99 ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				// if the commenter is is the user posting
				elseif ( $show_comments['user_id'] == $user_info['user_id'] ) {
					$comment_options1 = "$delete_link | $edit_link | ";
				}
				else {
					$comment_options1 = "$delete_link | $ban_user | $edit_link |";
				}
			}
			

			// else, regular members are only able to delete or edit their own posts.
		else {
			if ( $show_comments['user_id'] == $user_info['user_id'] ) {
				$comment_options1 = "$delete_link | $edit_link | ";
			}
			else {
				$comment_options1 = "";
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

//Paginate( "pg", "$pages_num", "comments&amp;id=$_GET[id]" );

if ( !isset ( $user_info['user_id'] ) ) {
	echo "<center><b>You must be registered to post comments. <a href='$site_path/login'><b>Login</b></a> or <a href='$site_path/register'><b>Register</b></a></b></center>";
	
	
}
else {
			
			//check if the user is banned or not.
			    //if the function returns true (if a row is sent back)
				if (isbanned($user_info['user_id'] )) {
			    //you print this line.
				
				$det=isbanned($user_info['user_id'] );
				print "Your are banned from posting comments ".$det['denban']."";

				} else {
				
	?>
	</form><form name="comment_form" method="post">
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
	$result_edit_post = mysql_query ( "SELECT admin_comments.id, admin_comments.newsid,	admin_comments.comment, users.username FROM admin_comments LEFT JOIN users ON (admin_comments.poster=users.username) WHERE admin_comments.newsid='$id' AND admin_comments.id='$edit_id' AND users.username='$edit_username'" );
	if ( mysql_num_rows ( $result_edit_post ) > 0 ) {
		$edit_post = mysql_fetch_array ( $result_edit_post );
		echo stripslashes ( $edit_post[comment] );
	} 
}

if ( isset ( $quote ) && !empty ( $quote ) ) {
	list ( $quote_id, $quote_username ) = explode ( ",", $quote );
	$result_quote_post = mysql_query ( "SELECT admin_comments.id, admin_comments.newsid, admin_comments.comment, users.username FROM admin_comments LEFT JOIN users ON (admin_comments.poster=users.username) WHERE admin_comments.newsid='$id' AND admin_comments.id='$quote_id' AND users.username='$quote_username'" );
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
	echo "<table>
		<tr>
			";
	sort ( $smilies_array );
	for ( $x = 1; $x <= 28; $x++ ) {
		echo "<td style='width: 20px; height: 20px;'><a href=\"#$smilies_array[$x]\" onclick=\"InsertSmile( '$smilies_array[$x]' )\"><img src=\"$site_url/$script_folder/images/smilies/$smilies_array[$x].gif\" alt=\"$smilies_array[$x]\"></a></td>
				";
		if ( !is_float ( $x/4 ) ) {
			echo "</tr>
				<tr>
				";
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
}
$file_title = "News Comments:split:$headline";
?>