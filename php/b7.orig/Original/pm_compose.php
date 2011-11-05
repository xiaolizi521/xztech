<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !isset ( $user_info[user_id] ) ) {
echo "<script>document.location.href='$site_url/login.php'</script>";
} 

$send_limit = 5;
$nid = date( "YmdHis" );
$to = mysql_real_escape_string ( $_GET['to'] );
$pm_sendto = mysql_real_escape_string ( $_POST['pm_sendto'] );

if ( isset ( $_POST[pm_send] ) ) {

if ( !ereg ( ",", $pm_sendto ) ) {
$pm_array[] = $pm_sendto;
} elseif ( ereg ( ", ", $pm_sendto ) ) {
$pm_array = explode ( ", ", $pm_sendto );
} elseif ( ereg ( ",", $pm_sendto ) ) {
$pm_array = explode ( ",", $pm_sendto );
}

if ( count ( $pm_array ) > $send_limit ) {
$error_msg = "You cannot send more than <u>$send_limit</u> messages at a time.";
} else {
if ( empty ( $pm_sendto ) ) {
$error_msg = "You must input a recipient.";
} elseif ( empty ( $pm_msg ) ) {
$error_msg = "You must input a message.";
} else {
for ( $x = 0; $x <= ( count ( $pm_array ) - 1 ); $x++ ) {
$result = mysql_query( "SELECT username FROM users WHERE username='$pm_array[$x]'" );
if ( mysql_num_rows ( $result ) <= 0 ) {
$pm_success = "false";
$error_msg .= "Recipient ".($x+1).": <u>$pm_array[$x]</u> does not exist.<br>";
$x = count ( $pm_array );
} else {
$pm_success = "true";
$pm_array_final[] = "$pm_array[$x]";
}
}
}
}

if ( $pm_success == "true" ) {
for ( $x = 0; $x <= ( count ( $pm_array_final ) - 1 ); $x++ ) {
if ( empty ( $pm_subject ) ) {
$pm_subject = "None:";
}
$pm_stripped = strip_tags( $pm_msg );
$pm_post = str_replace( "\n", "<br>", $pm_stripped );
$insert_pm = mysql_query ( "INSERT INTO pm ( id, sent_by, sent_to, sent_on, subject, message, status ) VALUES ( '$nid', '$user_info[username]', '$pm_array_final[$x]', now(), '$pm_subject', '$pm_post', '3' )" );
}
echo "<script>alert( 'PM(s) successfully sent' )</script>";
echo "<script>document.location='$site_url/$main_filename?page=pm_inbox'</script>";
} 


/*
for ( $x = 0; $x <= ( count ( $pm_array ) - 1 ); $x++ ) {

$result = mysql_query( "SELECT username FROM users WHERE username='$pm_sendto'" );
if ( mysql_num_rows ( $result ) <= 0 ) {
$error_msg = "Error: Recipient does not exist";
} else {
if ( empty ( $pm_subject ) || empty ( $pm_msg ) ) {
$error_msg = "Error: Subject and/or message field cannot be empty";
} else {
echo "$pm_array[$x]<br>";

$pm_stripped = strip_tags( $pm_msg );
$pm_post = str_replace( "\n", "<br>", $pm_stripped );
$insert_pm = mysql_query ( "INSERT INTO pm ( id, sent_by, sent_to, sent_on, subject, message ) VALUES ( '$nid', '$user_info[username]', '$pm_sendto', now(), '$pm_subject', '$pm_post' )" );
echo "<script>alert( 'PM has been successfully sent' )</script>";
echo "<script>document.location='$site_url/$main_filename?page=pm_inbox'</script>";

}
}

}
*/

}

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
document.pm_form.pm_msg.value += ':'+expression+' ';
}
</script>

<form name="pm_form" method="post">
<?php
if ( isset ( $error_msg ) ) {
echo "<table width='100%' cellpadding='5' cellspacing='0' class='main'><tr><td><b>$error_msg</b></td></tr></table>";
} else {
echo "";
} 
?>
<table width="100%" cellpadding="5" cellspacing="0" class="main">
<tr><td valign="top"><b>Recipient Username(s):</b><br><input type="text" name="pm_sendto" value="<?php 
if ( isset ( $_GET[to] ) ) {
echo $_GET[to];
} elseif ( isset ( $pm_sendto ) ) {
echo $pm_sendto;
}
?>" size="50" class="textbox"><br>You may send up to <b><?php echo $send_limit ?></b> message(s) at a time.<br>Seperate each username with a comma.</td></tr>
<tr><td valign="top"><b>Subject:</b><br><input type="text" name="pm_subject" value="
<?php 
if ( isset ( $reply ) && !empty ( $reply ) ) {
echo "RE: $reply";
} else {
echo $pm_subject; 
}
?>" size="50" class="textbox"></td></tr>
<tr><td valign="top"><b>Message:</b><br>
<?php
foreach ( $img_array as $var ) {
echo "<a href='#insertsmile' onclick='insert_smile( \"$var\" )'><img src='$site_url/news/images/smilies/$var.gif' border='0'></a>";
echo "   ";
}
?>
<br><textarea name="pm_msg" rows="10" cols="50" class="textbox"><?php echo $pm_msg ?></textarea>
</td></tr>
<tr><td valign="top"><b>Options:</b><br><input type="submit" name="pm_send" value="Send PM" class="submit_button">   <input type="button" value="Back To Inbox" class="submit_button" onclick="document.location='<?php echo "$PHP_SELF?page=pm_inbox" ?>'"></td></tr></form>
</table>






