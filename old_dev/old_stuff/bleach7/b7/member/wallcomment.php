<?php // last edit : flk 05/08/06: fixed submit comment problem
      // once comment is submitted user stays on the same page.
      // the problem was that the links all contained the &amp; instead of just &
      // i think this was caused by an editor since there were over 25 occurences of the problem.

// Connect to the database
$test = mysql_connect( $sql_location, $sql_username, $sql_password );
if(! $test) // check if connected
      die('Could not connect to MySQL'); // error message


//select database
mysql_select_db( $sql_database )
   or die (('could not open $database: ' . mysql_error() )); //error  message
###################################
# Edited and Cleaned by Hinalover #
###################################

$nid = time();

if(empty($_GET['pg']))
{ $pg = 1; }
else
{ $pg = mysql_real_escape_string ( $_GET['pg'] ); }

$art_comments = mysql_query("SELECT * FROM `gallery_comments` WHERE `imageid` =  $id");
$comments_num = mysql_num_rows($art_comments);

$limit = 10;
$pages_num = ceil ( $comments_num/$limit );


if ( $handle = opendir ( $script_folder . '/images/smilies' ) ) {
	while ( false !== ( $file = readdir ( $handle ) ) ) { 
		if ( $file != '.' && $file != '..' && ereg ( '.gif', $file ) ) { 
			$smile_name = str_replace (  '.gif', '', $file );
			$smilies_array[] = $smile_name;
		} 
	}
	closedir( $handle ); 
}
?>
<script type="text/javascript">
click_count = 0;

function DeleteComment( URL ) {
    if (confirm('Are you sure you want to delete this comment?')) {
        document.location = URL;
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
	document.comment_form.comment_post.value += ' :' + expression + ' ';
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
	document.comment_form.comment_post.value += ' [url=' + urllink + ']' + urltext + '[/url] ';
}

function InsertColor() {
	colorlink = prompt ("Enter the color you want to insert.");
	colortext = prompt ("Enter the text you want to have in place of the color");
	document.comment_form.comment_post.value += ' [color=' + colorlink + ']' + colortext + '[/color] ';
}

function InsertHL() {
	hllink = prompt ("Enter the highlight you want to insert.");
	hltext = prompt ("Enter the text you want to have in place of the highlight");
	document.comment_form.comment_post.value += ' [hl=' + hllink + ']' + hltext + '[/hl] ';
}

function ViewAllSmilies() {
	window.open("<?php echo $site_url, '/', $script_folder, '/smilies.php'
	?>", "legend", "width=170,height=500,left=0,top=0,resizable=yes,scrollbars=yes"); 
}
</script>

<?php
function CommentOption( $id, $username, $type ) {
	global $site_path;
    if(empty($_GET['pg']))
    { $pg = 1; }
    else
    { $pg = mysql_real_escape_string ( $_GET['pg'] ); }
	if ( $type == 'delete' ) {
		$type_url = '<a href="http://www.bleach7.com?page=media/wallpaperview&amp;id=' . $_GET['id'] . '&amp;pg=' . $pg . '&amp;' . $type . '=' . $id . ',' . $username . '" onclick="javascript:DeleteComment (\'http://www.bleach7.com?page=media/wallpaperview&amp;id=' . $_GET['id'] . '&amp;pg=' . $pg . '&amp;' . $type . '=' . $id . ',' . $username . '\')">' . ucwords( $type ) . '</a>';
	}elseif ( $type == 'ban' ){

			$type_url = '<a href="http://www.bleach7.com?page=member/bancomments&amp;user=' . $username . '">' . ucwords( $type ) . '</a>';
	}
	else {
		$type_url = '<a href="http://www.bleach7.com?page=media/wallpaperview&amp;id=' . $_GET['id'] . '&amp;pg=' . $pg . '&amp;' . $type . '=' . $id . ',' . $username . '#comment">' . ucwords( $type ) . '</a>';
	}
	return $type_url;
}

// What happens when someone inserts a post
if ( isset ( $_POST['submit_comment'] ) ) {
	if ( !isbanned ( $user_info['user_id'] ) ) {
		$comment_post = mysql_real_escape_string ( htmlspecialchars ( $_POST['comment_post'], ENT_QUOTES ) );
		$update_comment_num = mysql_query ( 'UPDATE `gallery` SET `comments` = ( `comments` + 1 ) WHERE `id`=\'' . mysql_real_escape_string ( $id ) . '\'' );
		$update_user_posts = mysql_query ( 'UPDATE `users` SET `posts` = ( `posts` + 1 ) WHERE `username`=\'' . mysql_real_escape_string ( $user_info['username'] ) . '\'' );
		$insert_post= mysql_query ( 'INSERT INTO `gallery_comments` ( `id`, `imageid`, `poster`, `post` )
		VALUES ( ' . mysql_real_escape_string ( $nid) . ', ' . mysql_real_escape_string ( $id ) . ', \'' . mysql_real_escape_string ( $user_info['username'] ) . '\', \'' . $comment_post . '\' )' );
		if ( $pages_num == 0 ) {
			header ( 'Location: http://www.bleach7.com?page=media/wallpaperview&id=' . $id . '&pg=1' );
		}
		else {
			header ( 'Location: http://www.bleach7.com?page=media/wallpaperview&id=' . $id . '&pg=' . $pages_num );
		}
	}
}

// What happens when someone edits a post
if ( isset ( $_POST['edit_comment'] ) ) {
	$comment = mysql_real_escape_string ( htmlspecialchars ( $_POST['comment_post'], ENT_QUOTES ) );
	$edit = $_POST['editid']; 
	list ( $edit_id, $edit_username ) = explode ( ",", $edit );
	$update_post= mysql_query ( "UPDATE gallery_comments SET post='$comment' WHERE imageid='$id' AND id='$edit_id'" );
	echo "<script>document.location='index.php?page=media/wallpaperview&id=$id&pg=$pg'</script>";

}

// What happens when someone deletes a post

if ( isset ( $_GET['delete'] ) && !empty ( $_GET['delete'] ) ) {
	list ( $delete_id, $delete_username ) = explode ( ',', $_GET['delete'] );
	$result_delete_post = mysql_query ( 'SELECT `gallery_comments`.`id`, `gallery_comments`.`imageid`, `gallery_comments`.`post`, `users`.`username` FROM `gallery_comments` LEFT JOIN `users` ON ( `gallery_comments`.`poster` = `users`.`username` ) WHERE `gallery_comments`.`imageid` = \'' . mysql_real_escape_string ( $id ) . '\' AND `gallery_comments`.`id` = \'' . mysql_real_escape_string ( $delete_id ) . '\' AND `users`.`username` = \'' . mysql_real_escape_string ( $delete_username ) . '\'' );
	if ( mysql_num_rows ( $result_delete_post ) > 0 ) {
		$delete_post = mysql_query ( 'DELETE FROM `gallery_comments` WHERE `imageid` = \'' . mysql_real_escape_string ( $id ) . '\' AND `id` = \'' . mysql_real_escape_string ( $delete_id ) . '\'' );

		///////////////////////////////////////////////
		//record who deletes what when and from where//
		//////////////////////////////////////////////
		if ($delete_post) {
			log_entry ( 'message','deleted the following msg : #' . $delete_id . ' from the news post ' . $id . ' by ' .$delete_username . '.', $user_info['username'] );
		}
		else {
			log_entry ( 'error', 'could not delete the following msg : #' . $delete_id . ' from the news post ' . $id . ' by ' . $delete_username . '.', $user_info['username'] );
		}
		
		
		//$update_comments_num = mysql_query ( 'UPDATE `news` SET `comments` = ( `comments` - 1 ) WHERE `id` = \'' . mysql_real_escape_string ( $id ) . '\'' );
		$update_user_post = mysql_query ( 'UPDATE `users` SET `posts` = ( `posts` - 1 ) WHERE `username` = \'' . mysql_real_escape_string ( $delete_username ) . '\'' );

		if ( $pages_num == 0 ) {
			header ( 'Location: http://www.bleach7.com?page=media/wallpaperview&id=' . $id . '&pg=1' );
		}
		else {
			header ( 'Location: http://www.bleach7.com?page=media/wallpaperview&id=' . $id . '&pg=' . $pages_num );
		}
	} 
}

// Checking if any comments have been made
if ( $comments_num == 0 ) {
	echo '<p style="text-align: center;">No comments have been made.';
	if ( isset ( $user_info['user_id'] ) ) {
		echo ' Be the first by entering a comment below.</p><p>
		';
	}
	else {
		echo '</p><p>';
	}
	echo '</p>';
}
else {
	if ( !isset ( $pg ) || empty ( $pg ) || $pg < 1 ) {
		header ( 'Location: http://www.bleach7.com?page=media/wallpaperview&id=' . $id . '&pg=1' );
		ob_end_flush();
	}
	elseif ( $pg > $pages_num ) {
		header ( 'Location: http://www.bleach7.com?page=media/wallpaperview&id=' . $id . '&pg=' . $pages_num );
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
	
	
	// Gather the list of postings for this page
	$result_comments = mysql_query ( 'SELECT `gallery_comments`.*, `users`.* FROM `gallery_comments` LEFT JOIN `users` ON ( `users`.`username` = `gallery_comments`.`poster`) WHERE `gallery_comments`.`imageid`=\'' . mysql_real_escape_string ( $id ) .'\' ORDER BY `gallery_comments`.`id` ASC LIMIT ' . mysql_real_escape_string ( $offset ) . ', ' . mysql_real_escape_string ( $limit ) );

	// Input the top page changes bar
	PaginateGallery( 'pg', $pages_num, 'media/wallpaperview&amp;id=' . $_GET['id'] );

	$comment_num = ( $offset + 1 );
	while ( $show_comments = mysql_fetch_array ( $result_comments ) ) {

		// Create a B7 User class for the current comment user
		$comment_B7 = new B7_User ( $show_comments );

		// Gathering each member's information
		$member_username = '<a href="' . $site_path . 'member/member&amp;id=' . $show_comments['username'] . '"><b><u>' . $show_comments['username'] . '</u></b></a>';
		$comment_date = DisplayDate( $show_comments['id'], 'M d Y, h:i A', '1' );
		$comment_postnum = 'Post #' . $comment_num;
		

    	//check if the user is banned or not.
		//if the function returns true (if a row is sent back) show the pwned picture
		if ( isbanned ( $show_comments['user_id'] ) ) {
		  $member_avatar = '<img src="member/images/avatars/pwnd.jpg" alt="pwnd" width="90" height="90" />';
		}
		else {

	        // displaying each member's image
			if ( empty ( $show_comments['avatar'] ) ) {
				$member_avatar = '<img src="' . $site_url . '/' . $script_folder . '/images/avatars/none.gif" alt="' . $show_comments['username'] . '" width="90" height="90" />';
			}
			else{
				list ( $avatar_width, $avatar_height ) = getimagesize ( $show_comments['avatar'] );
				// resizing images if they are over 90 x 90 pixels.
				if ( $avatar_width > 90 || $avatar_height > 90 ) {
					$member_avatar = '<img src="' . $show_comments['avatar'] . '" alt="' . $show_comments['username'] . '" width="90" height="90" />';
				}
				else {
					$member_avatar = '<img src="' . $show_comments['avatar'] . '" alt="' . $show_comments['username'] . '" />';
				}
			} 
		}
	// determine the member type
    $member_type = $comment_B7->getTitle();

	// Figure out what each member's rank is based off of their post count
	$member_rank = $comment_B7->getPost_rank( $show_comments['posts'] );

	// add "Rank:" to the beginning of the member rank
	$member_rank = 'Rank: ' . $member_rank;

	// aquire the join data and add "Joined: " at the beginning
	$joindate = DisplayDate( $show_comments['registered_on'], 'm/d/y', '0' );
	$member_joindate = 'Joined: ' . $joindate;

	// add User infomation, such as post count and member number
	$member_posts = 'Posts: ' . $show_comments['posts'];
	$member_num = 'Member No.' . $show_comments['user_id'];

	// determine if the member is online or not<br />
	if ( ( time() - $show_comments['last_activity_time'] ) <= 300 ) {
		$member_online = 'Status: <span style="color: green;">Online</span>';
	}
	else {
		$member_online = 'Status: <span style="color: red;">Offline</span>';
	}

	// add the links at the top of each post
	$ban_user = CommentOption( $show_comments['id'], $show_comments['username'], 'ban' );
	$delete_link = CommentOption( $show_comments['id'], $show_comments['username'], 'delete' );
	$edit_link = CommentOption( $show_comments['id'], $show_comments['username'], 'edit');
	$quote_link = CommentOption( $show_comments['id'], $show_comments['username'], 'quote' );
		
	// Determin if the user is a registered user, and determine the type of things they can delete, edit, quote, etc.
	if ( isset ( $user_B7 ) ) {
		$comment_options1 = $user_B7->comment_option ( $comment_B7, $ban_user, $delete_link, $edit_link );

		$comment_options2 = $quote_link . ' | <a href="#comment">Reply</a> | <a href="' . $site_path . '/pm_compose&amp;to=' . $show_comments['username'] . '">PM</a>';
		$comment_options = $comment_options1 . $comment_options2;
	}
		$comment= stripslashes ( nl2br ( $show_comments['post'] ) );
//		$comment= htmlentities ( $comment, ENT_QUOTES );
		$comment= ParseMessage ( $comment );

		include ( './member/templates/comments.php' );
		unset ( $comment_options1 );
		$comment_num++;
	}
}

// Input the bottom page changes bar
PaginateGallery( 'pg', $pages_num, 'media/wallpaperview&amp;id=' . $_GET['id'] );

if ( !isset ( $user_info['user_id'] ) ) {
	echo '<p style="text-align: center;"><b>You must be registered to post comments.</b> <a href="', $site_path, '/login"><b>Login</b></a><b> or </b><a href="', $site_path, '/register"><b>Register</b></a></p>';
	
	
}
else {
	
	//check if the user is banned or not.
    //if the function returns true (if a row is sent back)
	if ( isbanned ( $user_info['user_id'] ) ) {
	    //you print this line.
				
		$det = isbanned($user_info['user_id'] );
		print 'Your are banned from posting comments ' . $det['denban'];
	}
	else {
?>

<form name="comment_form" method="post" action="">
	<p style="text-align: center;"><input type="button" value="Bold" class="form" onClick="InsertBold()" />
		<input type="button" value="Italic" class="form" onClick="InsertItalic()" />
		<input type="button" value="Underline" class="form" onClick="InsertUnderline()" />
		<input type="button" value="Color" class="form" onClick="InsertColor()" />
		<input type="button" value="Highlight" class="form" onClick="InsertHL()" />
		<input type="button" value="URL" class="form" onClick="InsertURL()" />
		<input type="button" value="Spoiler" class="form" onClick="InsertSpoiler()" /></p>
	<table cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td align="center">
				<table cellpadding="5" cellspacing="0">
					<tr>
						<td valign="top">
							<a name="comment"></a>
							<textarea name="comment_post" id="comment_post" style="width: 330px; height: 185px; overflow: auto" class="form">
<?php
if(isset($_GET['edit']))
{ $edit = $_GET['edit']; }
else
{ $edit = 'null'; }
if ( $edit != 'null' && !empty ( $edit ) ) {
	list ( $edit_id, $edit_username ) = explode ( ",", $edit );
	$result_edit_post = mysql_query ( 'SELECT `gallery_comments`.`id`, `gallery_comments`.`imageid`, 			`gallery_comments`.`post`, `users`.`username` FROM `gallery_comments` LEFT JOIN `users` ON ( `gallery_comments`.`poster` = `users`.`username` ) WHERE `gallery_comments`.`imageid` = \'' . mysql_real_escape_string ( $id ) . '\' AND `gallery_comments`.`id` = \'' . mysql_real_escape_string ( $edit_id ) . '\' AND `users`.`username` = \'' . mysql_real_escape_string ( $edit_username ) . '\'' );
	if ( mysql_num_rows ( $result_edit_post ) > 0 ) {
		$edit_post = mysql_fetch_array ( $result_edit_post );
		echo stripslashes ( $edit_post['post'] );
	} 
}

if(isset($_GET['quote']))
{ $quote = $_GET['quote']; }
else
{ $quote = 'null'; }
if ( $quote != 'null' && !empty ( $quote ) ) {
	list ( $quote_id, $quote_username ) = explode ( ",", $quote );
	$result_quote_post = mysql_query ( 'SELECT `gallery_comments`.`id`, `gallery_comments`.`imageid`, `gallery_comments`.`post`, `users`.`username` FROM `gallery_comments` LEFT JOIN `users` ON ( `gallery_comments`.`poster` = `users`.`username` ) WHERE `gallery_comments`.`imageid` = \'' . mysql_real_escape_string ( $id ) . '\' AND `gallery_comments`.`id` = \'' . mysql_real_escape_string ( $quote_id ) . '\' AND `users`.`username` = \'' . mysql_real_escape_string ( $quote_username ) . '\'' );
	if ( mysql_num_rows ( $result_quote_post ) > 0 ) {
		$quote_post = mysql_fetch_array ( $result_quote_post );
		echo '[QUOTE=' . $quote_username. ']' . stripslashes ( $quote_post['post'] ) . '[/QUOTE]';
	} 
}
?>
</textarea>
	<?php
	if ( $edit != 'null' && !empty ( $edit ) && mysql_num_rows ( $result_edit_post ) > 0 ) {
		$editid = $_GET['edit'];
		echo '						
		<input type="hidden" name="edit_comment" />		
		<input type="hidden" name="editid" value="'.$editid.'" />
';
		$button_value = 'Edit Comment'; 
	}
	else { 
		echo '						<input type="hidden" name="submit_comment" />
';
		$button_value = 'Submit Comment';
	} 
	?>
						</td>
						<td valign="top">
							<fieldset>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td style="height: 110px;">
	<?php
	echo '										<table>
											<tr>
';
	sort ( $smilies_array );
	$last = 28;
	for ( $x = 1; $x <= $last; $x++ ) {
		echo '												<td style="width: 20px; height: 20px;"><a href="#', $smilies_array[$x], '" onclick="InsertSmile( \'', $smilies_array[$x], '\' )"><img src="', $site_url, '/', $script_folder, '/images/smilies/', $smilies_array[$x], '.gif" alt="', $smilies_array[$x], '" /></a></td>
';
		if ( !is_float ( $x/4 ) ) {
			if ( ( $last - $x ) < 4 ) {
				echo '											</tr>
';
			}
			else {
				echo '											</tr>
											<tr>
';
			}
		}
	}
	echo '										</table>';
	echo '										<table align="center" class="main">
											<tr>
												<td align="center"><a href="#viewall" onclick="ViewAllSmilies()">View All</a></td>
											</tr>
										</table>
';
	?>
									</td>
								</tr>
							</table>
							</fieldset>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<p  style="text-align: center;">
		<?
		if(isset($_GET['edit']))
		{ echo '<input type="hidden" name="id" value="' . mysql_real_escape_string ( $id ) . '">
		<input type="hidden" name="edit_comment" value="edit_comment">
		'; }
		?>
		<input type="button" value="<?php echo $button_value ?>" class="form" onClick="if ( document.comment_form.comment_post.value=='' ) { alert( 'You must enter a comment' ); return false; } else { javascript:ClickTracker(); }" />
		<input type="button" value="Reset Comment" class="form" onClick="document.comment_form.reset()" /></p>
</form>
	<?php
}//ban end here
}
?>
