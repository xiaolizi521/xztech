<?php
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
?>