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

include ( "$_SERVER[DOCUMENT_ROOT]/functions.php" );

$x = 1;

if ( $user_info[type] == 1 ) {
$limit = 50;
} elseif ( $user_info[type] == 2 ) {
$limit = 100;
} elseif ( $user_info[type] == 3 ) {
$limit = 150;
}

echo "<form name='pm_form' method='post'>";
if ( !isset ( $id ) && empty ( $id ) ) {
$result = mysql_query( "SELECT * FROM pm WHERE sent_to='$user_info[username]' ORDER BY sent_on DESC" );
$result_countunread = mysql_query( "SELECT status FROM pm WHERE sent_to='$user_info[username]' AND status='0' ORDER BY sent_on DESC" );
if ( mysql_num_rows ( $result ) <= 0 ) {
echo "<center><b>You have no messages in your inbox</b></center>";
} else {
if ( mysql_num_rows ( $result ) > $limit ) {
$delete_pm_select = mysql_query( "SELECT * FROM pm WHERE sent_to='$user_info[username]' ORDER BY sent_on ASC" );
$delete = mysql_fetch_array( $delete_pm_select );
$delete_pm = mysql_query ( "DELETE FROM pm WHERE id='$delete[id]' AND sent_to='$user_info[username]'" );
echo "<script>alert( 'You have exceeded the limit of $limit messages. The oldest 
message will be deleted' )</script>";
echo "<script>document.location.href='$PHP_SELF?page=pm_inbox'</script>";
}
?>
<script type="text/javascript">
function CheckAll(checkWhat) {
  // Find all the checkboxes...
  var inputs = document.getElementsByTagName("input");

  // Loop through all form elements (input tags)
  for(index = 0; index < inputs.length; index++)
  {
    // ...if it's the type of checkbox we're looking for, toggle its checked status
    if(inputs[index].id == checkWhat)
      if(inputs[index].checked == 0)
      {
        inputs[index].checked = 1;
      }
      else if(inputs[index].checked == 1)
      {
        inputs[index].checked = 0;
      }
  }
}
</script>
<table width="100%" cellpadding="3" cellspacing="0" border="0" class="main" style="border-bottom: 1px solid #C3C3C3"><tr>
<td width="20"><a href="<?php echo "$PHP_SELF?page=pm_compose" ?>"><img src="members/images/msg_new.gif" alt="Compose A PM" border="0"></a></td>
<td width="1"><input type="checkbox" onClick="CheckAll('pm_delete')"></td>
<td width="45%"><b><u>Subject</u></b></td>
<td width="20%"><b><u>From</u></b></td>
<td><b><u>Date</u></b></td>
</tr></table>
<?php
while ( $pm = mysql_fetch_array ( $result ) ) {
echo "<table width='100%' cellpadding='3' cellspacing='0' class='main' style='border-bottom: 1px solid #C3C3C3'>";
$sent_date = $pm['sent_on']; 
$sent_on = date ( 'm/d/y \a\\t h:i A', strtotime( $sent_date ) ); 
echo "<tr>
<td width='20'>";
if ( $pm[status] == 0 ) {
echo "<img src='members/images/msg_new.gif' alt='Unread Message'>";
} else {
echo "<img src='members/images/msg_old.gif' alt='Read Message'>";
} 
echo "</td>
<td width='1'><input type='checkbox' id='pm_delete' name='pm_delete[]' value='$pm[id]'></td>
<td width='45%'>";
if ( $pm[status] == 0 ) {
echo "<b><a href='$PHP_SELF?page=pm_inbox&id=$pm[id]'>$pm[subject]</a></b>";
} else {
echo "<a href='$PHP_SELF?page=pm_inbox&id=$pm[id]'>$pm[subject]</a>";
}
echo "</td>
<td width='20%'><a href='#viewmember' onclick='ViewMember( \"$pm[sent_by]\" )'>$pm[sent_by]</a></b></td>
<td align='right'>$sent_on</td>
</tr>";
$x++;
echo "</table>";
}


}

if ( isset ( $_POST[submit] ) ) {
for ( $i = 0; $i <= ( count ( $pm_delete ) - 1 ); $i++ ) {
$delete_pm = mysql_query ( "DELETE FROM pm WHERE id='$pm_delete[$i]' AND sent_to='$user_info[username]'" );
}
echo "<script>alert( 'PM(s) successfully deleted' )</script>";
echo "<script>document.location='$PHP_SELF?page=pm_inbox'</script>";
}


?>
<table width="100%" cellpadding="3" cellspacing="0" class="main">
<tr><td height="5"></td></tr>
<tr>
<td align="left"><b><?php echo "".mysql_num_rows ( $result )." Message(s), ".mysql_num_rows ( $result_countunread )." Unread out of a maximum $limit" ?></b></td>
<td align="right"><input type="button" value="Compose PM" class="submit_button" onclick="document.location='<?php echo "$site_url/$main_filename?page=pm_compose" ?>'">   <input type="submit" name="submit" value="Delete PM" class="submit_button"></td>
</tr></table>
<?php
} else {
$result = mysql_query( "SELECT * FROM pm WHERE id='$id' AND sent_to='$user_info[username]'" );
$pm = mysql_fetch_array ( $result );

if ( isset ( $id ) && !empty ( $id ) && ( $action == "delete" ) ) {
echo "<table width='100%' class='main'><tr><td align='center'><b>Are you sure you want to delete this PM?<br><a href='$PHP_SELF?page=pm_inbox&id=$id&action=delete&option=yes'>Yes</a> | <a href='$PHP_SELF?page=pm_inbox&id=$id'>No</a></b></td></tr></table><p>";
}

if ( isset ( $id ) && !empty ( $id ) && ( $action == "delete" ) && ( $option == "yes" ) ) {
$delete_pm = mysql_query ( "DELETE FROM pm WHERE id='$id' AND sent_to='$user_info[username]'" );
echo "<script>alert( 'PM Deleted' )</script>";
echo "<script>document.location='$PHP_SELF?page=pm_inbox'</script>";
}

if ( mysql_num_rows ( $result ) <= 0 ) {
echo "<center><b>Invalid PM ID</b></center>";
} else {

if ( ereg ( "id=$id", $GLOBALS[QUERY_STRING] ) && ( $pm[status] == 0 ) ) {
$result_changestatus = mysql_query ( "UPDATE pm SET status='1' WHERE id='$id' AND sent_to='$user_info[username]'" );
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

$result_getuserinfo = mysql_query ( "SELECT avatar FROM users WHERE username='$pm[sent_by]'" );
$viewpm = mysql_fetch_array ( $result_getuserinfo );
$datetime = $pm['sent_on']; 
$date = date ( 'm/d/y - h:i A', strtotime( $datetime ) ); 
$pm_message = $pm['message'];
$pm_message = parse_message ( "$pm_message" );

echo "<table bgcolor='$tableheadercolor' width='100%' cellpadding='2' cellspacing='0' class='secondary'><tr><td align='left'><b>$pm[subject] | <a href='#viewmember' onclick='ViewMember( \"$pm[sent_by]\" )'>$pm[sent_by]</a> | $date</b></td><td align='right'><b><a href='$PHP_SELF?page=pm_compose&to=$pm[sent_by]&reply=$pm[subject]'>Reply</a> | <a href='$PHP_SELF?page=pm_inbox&id=$id&action=delete'>Delete</a></b></tr></table>";

echo "<table height='1'><tr><td></td></tr></table>";

echo "<table width='100%' cellpadding='2' cellspacing='0' class='main'><tr>";

echo "<td width='1' align='center' valign='top'>";

if ( !empty ( $viewpm['avatar'] ) ) {
echo "<img src='$viewpm[avatar]' width='50' height='50'>";
} else {
echo "<div style='width: 50px; height: 50px; border: 1px solid #4A4D4F'></div>";
}

echo "</td><td valign='top'>";

echo "<table width='100%' cellpadding='1' cellspacing='0' class='main'><tr><td style='text-align: justify'>$pm_message</td></tr></table>";

echo "</td></tr></table>";

} 

echo "<p><input type='button' value='Back To Inbox' class='submit_button' onclick='document.location=\"$PHP_SELF?page=pm_inbox\"'>   <input type='button' value='Compose PM' class='submit_button' onclick='document.location=\"$site_url/$main_filename?page=pm_compose\"'>";

}
echo "</form>";
?>