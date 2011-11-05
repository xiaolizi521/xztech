<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

?>

<?php
function Paginate( $identifier, $pages_num, $query_string ) {
global $site_path;
$pg = $_GET[$identifier];
$pg_limit = 5;
$pg_prev_pages = ($pg - floor ( $pg_limit/2 ) );
$pg_next_pages = ($pg + floor ( $pg_limit/2 ) );
if ( $pg_prev_pages >= 1 ) {
$start = $pg_prev_pages;
$first ="1";
} else {
$start = 1;
} 
if ( $pg_next_pages <= $pages_num ) {
$finish = $pg_next_pages;
$last = "1";
} else {
$finish = $pages_num;
}
if ( isset ( $pg ) && !empty ( $pg ) && ( $pg >= 1 ) && ( $pg <= $pages_num ) ) {
echo "<hr noshade color='#666666' size='1' width='96%' />
";
echo "<table width='96%' cellpadding='0' cellspacing='0'><tr><td align='left'>
";
echo "<table style='padding: 3px;' class='main'>
	<tr>
		";
echo "<td>(Page $pg of $pages_num)</td>
";
if ( $pg > 1 ) {
if ( $first == 1 ) {
echo "		<td><a href='$site_path/$query_string&amp;pg=1' title='First Page'><b>&laquo;</b></a></td>
";
}
echo "		<td><a href='$site_path/$query_string&amp;pg=".($pg-1)."' title='Previous Page'>&lt;</a></td>
";
}
for ( $x = $start; $x <= $finish; $x++ ) {
if ( $pg == $x ) {
echo "		<td><b>$x</b></td>
";
} else {
echo "		<td><a href='$site_path/$query_string&amp;pg=$x'>$x</a></td>
";
}
}
if ( $pg < $pages_num ) {
echo "		<td><a href='$site_path/$query_string&amp;pg=".($pg+1)."' title='Next Page'>&gt;</a></td>
";
if ( $last == 1 ) {
echo "		<td><a href='$site_path/$query_string&amp;pg=$pages_num' title='Last Page'><b>&raquo;</b></a></td>
";
}
}
echo "			</tr>
		</table></td>
		<td align='right'><select name='page' onchange='document.location=this.value' class='form'>
		";
for ( $x = 1; $x <= $pages_num; $x++ ) {
if ( $pg == $x ) {
echo "	<option value='$site_path/$query_string&amp;$identifier=$x' selected>Page $x</option>
";
} else {
echo "	<option value='$site_path/$query_string&amp;$identifier=$x'>Page $x</option>
";
}
}
echo "	</select></td>
";
echo "	</tr>
</table>
";
echo "<hr noshade color='#666666' size='1' width='96%' />";
}
}


function DisplayDate( $timestamp, $display_format, $display_format_option ) { 
global $user_info;

$days_passed = ( ( time() - $timestamp ) / 86400 );

if ( $display_format_option == 1 ) {
if ( $days_passed <= 1 ) {
$display_format = "\T\o\d\a\y\, h:i A";
} elseif ( $days_passed <= 2 ) {
$display_format = "\Y\e\s\\t\e\\r\d\a\y, h:i A";
} else {
$display_format = $display_format;
}
}  
if ( isset ( $user_info['user_id'] ) ) {
if ( $user_info['dst'] == 1 ) {
$timezone = ($user_info['timezone']+date("I"));
} elseif ( $user_info[dst] == 0 ) {
$timezone = $user_info['timezone'];
}
} else {
$timezone = ((date("O")/100)+date("I"));
}
$zone = 3600*$timezone;
$datetime = (int)$timestamp;
$date = gmdate ( $display_format, $datetime + $zone );
return $date;
} 


function ParseMessage( $string ) { 
global $site_url, $script_folder, $smilies_array;

foreach ( $smilies_array as $var ) {
$string = str_replace( ":$var", "<img src='$site_url/$script_folder/images/smilies/$var.gif'>", $string );
}

$patterns = array ( 
'`\[b\](.+?)\[/b\]`is', 
'`\[i\](.+?)\[/i\]`is', 
'`\[u\](.+?)\[/u\]`is', 
'`\[email\](.+?)\[/email\]`is', 
'`\[email=(.+?)\](.+?)\[/email\]`is', 
'`\[url=([a-z0-9]+?://){1}(.+?)\](.+?)\[/url\]`is', 
'`\[url=(.+?)\](.+?)\[/url\]`is', 
'`\[url\]([a-z0-9]+?://){1}(.+?)\[/url\]`is', 
'`\[url\](.+?)\[/url\]`is', 
'`\[spoiler\](.+?)\[/spoiler\]`is',
'`\[quote\](.+?)\[/quote\]`is',
'`\[quote=(.+?)\](.+?)\[/quote\]`is',
'`\[color=(.+?)\](.+?)\[/color\]`is',
'`\[hl=(.+?)\](.+?)\[/hl\]`is',
'`\[img=(.+?)\]`is'
); 

$replacements =  array ( 
'<b>\\1</b>', 
'<i>\\1</i>', 
'<u>\\1</u>', 
'<a href="mailto:\1" target="_blank">\1</a>', 
'<a href="mailto:\1" target="_blank">\2</a>', 
'<a href="\1\2" target="_blank">\\3</a>', 
'<a href="http://\\1" target="_blank">\\2</a>', 
'<a href="\1\2" target="_blank">\1\2</a>', 
'<a href="http://\\1" target="_blank">\\1</a>', 
'<div style="margin:5px 20px 20px 20px"><br>
	<div class="smallfont" style="margin-bottom:2px"><b>Spoiler:</b> <input type="button" value="Show" style="width:45px;font-size:10px;margin:0px;padding:0px;"
			onclick="if
(this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display
!= \'\') {
this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display
= \'\';this.innerText = \'\'; this.value = \'Hide\'; } else {
this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display
= \'none\'; this.innerText = \'\'; this.value = \'Show\'; }" ID="Button1" NAME="Button1"></div>
	<div class="alt2" style="margin: 0px; padding: 6px; border: 1px inset;">
		<div style="display: none;">
			\1
		</div>
	</div>
</div>',
'<table width="95%" cellpadding="0" cellspacing="0" align="center" class="main"><tr><td><b>Quote:</b><table width="100%" cellpadding="2" cellspacing="0" class="main" style="border: 1px solid #C3C3C3"><tr><td valign="top"><i>\1</i></td></tr></table></td></tr></table>',
'<table width="95%" cellpadding="0" cellspacing="0" align="center" class="main"><tr><td><b>Quote:</b><table width="100%" cellpadding="2" cellspacing="0" class="main" style="border: 1px solid #C3C3C3"><tr><td valign="top">Originally Posted By <b>\1</b><br><i>\2</i></td></tr></table></td></tr></table>',
'<span style="color: \1;">\2</span>',
'<span style="background-color: \1;">\2</span>',
'<a href="\1"><img height="120" width="160" src="\1"></a>'
); 

$previous_string = "";
while ( $previous_string != $string ) {
$previous_string = $string;
$string = preg_replace( $patterns, $replacements , $string ); 
}

return $string; 

}


function getuserdetail($username) {
	$username= stripslashes(htmlentities($username));
	$query="SELECT * FROM users WHERE username='$username'";
	$result= mysql_query($query);
	$row= mysql_fetch_array($result);
	return $row;
}

function isbanned($userid) {
	$userid = stripslashes(htmlentities($userid));
	
	// Get a list of users with that user's ip address
	$query2 = "SELECT c.* FROM comments_banned c, users u WHERE u.user_id = $userid AND c.ip = u.ip_address";
	$result2 = mysql_query($query2);
	$result2_count = mysql_num_rows($result2);
	$count = 0;
	
	// if the list found users with the same ip address
	if ( $result2_count > 0 ) {
		// go through the list
		while ( $row_main2 = mysql_fetch_array($result2) ) {
			// if the userid the same as one in the list
			// add one to count
			if ( $userid == $row_main2['id'] ) {
				$count++;
			}
		}
		// if the ip address  is found, but the user name is not part of the list
		// the the user is using an alternate account, and an ip ban needs to be placed
		if ( $count == 0 ) {
			return true;
		}
	}

	$query="SELECT *, UNIX_TIMESTAMP(banlength) As banends FROM comments_banned WHERE user_id=$userid";

	$result= mysql_query($query);
	$row= mysql_fetch_array($result);

	if (mysql_num_rows($result) > 0) {
		//Still banned
		if($row['banends'] >= time() ) { 
			return true;
		}
		else {
			//Updates that users ban status
			mysql_query("DELETE FROM comments_banned where id=".$row['id']."");
			return false;
		}
	}
	else {
		return false ;
	}
}

###################
##  log_entry
###################

/*
Creates a new log entry for an event.

log_entry (<type>,<message>);
 :: type :: This is used to identify different type of log entries.
 		- 'message' :: This indicates the entry is just to log an event
 		- 'error' :: This indicates the entry is to log an error
 :: message :: This is the string to enter as the message for this event log.

*/
function log_entry ($type2,$text2,$user) {
	$type=$type2;
	$text=$text2;
	$ip=$_SERVER['REMOTE_ADDR'];
	if ( isset ( $user ) ){
		$user=$user ;
	}
	else{
		$user="Uknown";
	}
	$sql = mysql_query( "INSERT INTO error_logs (type,message,date,source,user)
		VALUES ('$type','$text','" . date('H:i:s d/m/Y') . "','$ip','$user')" ) or die( mysql_error() );
}

function can_ban ($user_id, $bannee_id) {
	$sql = mysql_query( "SELECT * FROM rank_ban WHERE ID = '$user_id' AND ban_id = '$bannee_id'" );
	$can_ban_info = mysql_fetch_array ( $sql );
	if ( $can_ban_info['can_ban'] == 1 ) {
		return true;
	}
	return false;
}
?>
