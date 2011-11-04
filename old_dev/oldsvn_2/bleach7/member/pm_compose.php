<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !isset ( $user_info['user_id'] ) ) {
	header ( 'Location: ' . $site_path . '/login' );
	ob_end_flush();
} 

$file_title = 'PM Compose';
$send_limit = 5;
$nid = time();

if (isset( $_GET['to']))
{ $to_u = $_GET['to']; }
else
{ $to_u = ''; }
$to = mysql_real_escape_string ( $to_u );

if (isset( $_GET['reply']))
{ $reply_u = $_GET['reply']; }
else
{ $reply_u = ''; }
$reply = mysql_real_escape_string ( $reply_u );

if (isset( $_GET['pm_sento']))
{ $send_u = $_GET['pm_sendto']; }
else
{ $send_u = ''; }

$pm_sendto = mysql_real_escape_string ( $send_u );
?>
<table cellpadding="0" cellspacing="0" class="main" style="width: 100%;">
	<tr>
		<td>
			<form name="comment_form" method="post" action="<?php echo $site_path, '/pm_compose'; ?>">
<?php

if ( isset ( $reply ) && !empty ( $reply ) ) {
	$result_reply = mysql_query ( 'SELECT `id`, `subject` FROM `pm` WHERE `id` = \'' . $reply . '\' AND `sent_to` = \'' . $user_info['username'] . '\'' );
	$pm_reply = mysql_fetch_array ( $result_reply );
}

function DisplayErrors () {
	global $errors;
	if ( count ( $errors ) == 1 ) {
		echo '<b>The following error was found:</b>';
	}
	else {
		echo '<b>The following errors were found:</b>';
	}
	echo '<ul type="square"> ';
	foreach ( $errors as $var ) {
		echo '<li>', $var, '</li> ';
	}
	echo '</ul>';
}


if ( isset ( $_POST['pm_send'] ) ) {

	$pm_sendto = trim ( mysql_real_escape_string ( htmlspecialchars ( $_POST['pm_sendto'], ENT_QUOTES ) ) );
	$pm_subject = mysql_real_escape_string ( htmlspecialchars ( $_POST['pm_subject'], ENT_QUOTES ) );
	$pm_msg = mysql_real_escape_string ( htmlspecialchars ( $_POST['comment_post'], ENT_QUOTES ) );

	if ( !ereg ( ',', $pm_sendto ) ) {
		$pm_array[] = $pm_sendto;
	}
	else if ( ereg ( ',', $pm_sendto ) ) {
		$pm_array = explode ( ',', $pm_sendto );
	}
	else if ( ereg ( ',', $pm_sendto ) ) {
		$pm_array = explode ( ',', $pm_sendto );
	}

	if ( count ( $pm_array ) > $send_limit ) {
		$errors[] = 'You cannot send more than <span style="text-decoration: underline;">' . $send_limit . '</span> messages at a time.';
	}
	else {
		if ( empty ( $pm_sendto ) ) {
			$errors[] = 'You must input a recipient.';
		}
		elseif ( empty ( $pm_msg ) ) {
			$errors[] = 'You must input a message.';
		}
		else {
			for ( $x = 0; $x <= ( count ( $pm_array ) - 1 ); $x++ ) {
				$result = mysql_query( 'SELECT `username` FROM `users` WHERE `username` = \'' . $pm_array[$x] . '\'' );
				if ( mysql_num_rows ( $result ) <= 0 ) {
					$pm_success = false;
					$errors[] = 'Recipient ' . ( $x + 1 ) . ': <span style="text-decoration: underline;">' . $pm_array[$x] . '</span> does not exist.<br />';
					$x = count ( $pm_array );
				}
				else {
					$pm_success = true;
					$pm_array_final[] = $pm_array[$x];
				}
			}
		}
	}
	if(empty($pm_success))
	{ $pm_success = false; }
	
	if ( $pm_success == true ) {
		for ( $x = 0; $x <= ( count ( $pm_array_final ) - 1 ); $x++ ) {
			if ( empty ( $pm_subject ) ) {
				$pm_subject = 'None:';
			}
			$insert_pm = mysql_query ( 'INSERT INTO pm ( id, sent_by, sent_to, subject, message, status ) 
VALUES ( \'' . $nid . '\', \'' . $user_info['username'] . '\', \'' . $pm_array_final[$x] . '\', \'' . $pm_subject . '\', \'' . $pm_msg . '\', \'3\' )' );
		}
		echo '<script type="text/javascript">
	alert( "PM(s) successfully sent" )
</script>
';
		header ( 'Location: ' . $site_path . '/pm_compose' );
	} 
}

if ( $handle = opendir ( $script_folder . '/images/smilies' ) ) {
	while ( false !== ( $file = readdir ( $handle ) ) ) { 
		if ( $file != '.' && $file != '..' && ereg ( '.gif', $file ) ) { 
			$smile_name = str_replace ( '.gif', '', $file );
			$smilies_array[] = $smile_name;
		} 
	}
	closedir( $handle ); 
}
?>
<script type="text/javascript">
click_count = 0;

function InsertSmile( expression ) 
{
	document.comment_form.comment_post.value += ' :'+expression+' ';
}

function ClickTracker() 
{
	click_count++;
	if ( click_count == 1 ) 
	{
		document.comment_form.submit();
	} 
	if ( click_count >= 2 ) 
	{
		alert ( "Please do not try to submit the form more than once" );
	return false;
	}
}

function InsertBold()
{
	document.comment_form.comment_post.value += ' [b] [/b] ';
}

function InsertItalic()
{
	document.comment_form.comment_post.value += ' [i] [/i] ';
}

function InsertUnderline()
{
	document.comment_form.comment_post.value += ' [u] [/u] ';
}

function InsertSpoiler()
{
	document.comment_form.comment_post.value += ' [spoiler] [/spoiler] ';
}

function InsertURL()
{
	urllink = prompt ("Enter the url you want to insert.");
	urltext = prompt ("Enter the text you want to have in place of the url");
	document.comment_form.comment_post.value += ' [url='+urllink+']'+urltext+'[/url] ';
}

function InsertColor()
{
	colorlink = prompt ("Enter the color you want to insert.");
	colortext = prompt ("Enter the text you want to have in place of the color");
	document.comment_form.comment_post.value += ' [color='+colorlink+']'+colortext+'[/color] ';
}

function InsertHL()
{
	hllink = prompt ("Enter the highlight you want to insert.");
	hltext = prompt ("Enter the text you want to have in place of the highlight");
	document.comment_form.comment_post.value += ' [hl='+hllink+']'+hltext+'[/hl] ';
}

function ViewAllSmilies() 
{
	window.open("<?php echo $site_url, '/', $script_folder, '/smilies.php' ?>","legend","width=170,height=500,left=0,top=0,resizable=yes,scrollbars=yes"); 
}
</script>
<?php
if(isset($errors))
{ $errorcount = count($errors); }
else
{ $errorcount = 0; }

if ( $errorcount > 0 ) {
	DisplayErrors();
}
?>
				<table cellpadding="0" cellspacing="0" class="main" style="width: 100%;">
					<tr>
						<td style="vertical-align: top;"><b>Recipient Username(s):</b><br />
							<input type="text" name="pm_sendto" value="<?php 
if ( isset ( $_GET['to'] ) ) {
	echo $_GET['to'];
}
else if ( isset ( $pm_sendto ) ) {
	echo $pm_sendto;
}
?>" style="width: 330px" class="form" /><br />
							You may send up to <b><?php echo $send_limit ?></b> message(s) at a time.<br />
							Seperate each username with a comma.</td>
					</tr>
					<tr>
						<td style="height: 5px;"></td>
					</tr>
					<tr>
						<td style="vertical-align: top;"><b>Subject:</b><br />
							<input type="text" name="pm_subject" value="
<?php
if ( !empty ( $pm_reply['subject'] ) ) {
echo 'RE: ' .stripslashes ( $pm_reply['subject'] );
} else {
if(isset($_GET['subject']))
{ echo $_GET['subject']; }
else
{ echo '';}
}
?>" style="width: 330px" class="form" /></td>
					</tr>
					<tr>
						<td style="height: 5px;"></td>
					</tr>
					<tr>
						<td style="text-align: center;">
							<input type="button" value="Bold" class="form" onclick="InsertBold()" /> 
							<input type="button" value="Italic" class="form" onclick="InsertItalic()" />
							<input type="button" value="Underline" class="form" onclick="InsertUnderline()" />
							<input type="button" value="Color" class="form" onclick="InsertColor()" />
							<input type="button" value="Highlight" class="form" onclick="InsertHL()" />
							<input type="button" value="URL" class="form" onclick="InsertURL()" />
							<input type="button" value="Spoiler" class="form" onclick="InsertSpoiler()" /></td>
					</tr>
					<tr>
						<td valign="top"><b>Message:</b><br />
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td style="vertical-align: top;">
									<?PHP
										if(isset($_POST['comment_post']))
										{ $commentpost = $_POST['comment_post']; }
										else
										{ $commentpost = ''; }
									?>
										<textarea name="comment_post" style="width: 390px; height: 203px; overflow: auto" class="form"><?php echo $commentpost; ?></textarea>
										</td>
									<td style="width: 10px;"></td>
									<td style="vertical-align: top;">
										<fieldset>
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td style="height: 110px;">
<?php
echo '														<table cellpadding="5" cellspacing="0">
															<tr>
';
sort ( $smilies_array );
$last = 28;
for ( $x = 1; $x <= $last; $x++ ) {
	echo '																<td><a href="#insertsmile" onclick="InsertSmile( \'' . $smilies_array[$x] . '\' )"><img src="' . $site_url . '/' . $script_folder . '/images/smilies/' . $smilies_array[$x] . '.gif" alt="' . $smilies_array[$x] . '" /></a></td>
';
	if ( !is_float ( $x/4 ) ) {
		if ( ( $last - $x ) < 4 ) {
			echo '															</tr>
';
		}
		else {
			echo '															</tr>
															<tr>
';
		}
	}
}
echo '														</table>
';
echo '														<table cellpadding="0" cellspacing="0"  class="main" style="width: 100%;">
															<tr>
																<td style="text-align: center;"><a href="#viewall" onclick="ViewAllSmilies()">View All</a></td>
															</tr>
														</table>';
?>
													</td>
												</tr>
											</table>
										</fieldset>
									</td>
								</tr>
							</table>
							<input type="hidden" name="pm_send" />
							<p style="text-align: center;">
								<input type="button" value="Send PM" class="form" onclick="ClickTracker()" />
								<input type="button" value="Back To Inbox" class="form" onclick="document.location='<?php echo $site_path, '/pm_inbox' ?>'" /></p>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>