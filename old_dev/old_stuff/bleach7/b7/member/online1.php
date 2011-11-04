<?php 
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

//echo "<META HTTP-EQUIV='Refresh' CONTENT='30;URL=$current_location'>";

$timeout = 300;
$file_title = "Users Online";

$result_online_users = mysql_query ( "SELECT * FROM users WHERE UNIX_TIMESTAMP(now()) - last_activity_time <= $timeout ORDER BY username ASC" );
$result_online_guests = mysql_query ( "SELECT * FROM guests WHERE UNIX_TIMESTAMP(now()) - last_activity_time <= $timeout" );
$total_online_members = mysql_num_rows ( $result_online_users );
$total_online_guests = mysql_num_rows ( $result_online_guests );
$total_online = ( $total_online_members + $total_online_guests );

if ( ereg ( "/online", $_GET[$ident] ) ) {

if ( $total_online_members != 1 ) {
$members_online = "$total_online_members members";
} else {
$members_online = "$total_online_members member";
}
if ( $total_online_guests != 1 ) {
$guests_online = "$total_online_guests guests";
} else {
$guests_online = "$total_online_guests guest";
}
echo "<center>";
if ( $total_online != 1 ) {
echo "<b>There are currently $members_online and $guests_online online</b><p />";
} else {
echo "<b>There is currently $members_online and $guests_online online</b><p />";
}
echo "</center>";

echo "<table width=\"100%\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\" class=\"main\">";
echo "	<tr>
		<td><b>Username</b></td>
		<td><b>Location</b></td>
		<td align=\"center\"><b>Last Activity</b></td>
		<td align=\"center\"><b>Contact</b></td>
	</tr>";

while ( $online_user = mysql_fetch_array ( $result_online_users ) ) {
if ( $online_user[type] == 1 || $online_user[type] == 2 ) {
$last_activity_username = $online_user[username];
} elseif ( $online_user[type] == 3 ) {
$last_activity_username = "<b>$online_user[username]</b>";
} elseif ( $online_user[type] == 4 ) {
$last_activity_username = "<b><u>$online_user[username]</u></b>";
} elseif ( $online_user[type] == 5 ) {
$last_activity_username = "<b><i>$online_user[username]</i></b>";
}
$last_activity_time = DisplayDate( "$online_user[last_activity_time]", "M d Y, h:i:s A", "1" );
$last_activity_url = $online_user[last_activity_url];
if ( !empty ( $online_user[aim] ) ) {
$online_user_aim = str_replace ( " ", "+", $online_user[aim] );
$aim_site_name = str_replace ( " ", "+", $sitetitle );
$user_contact_aim = "<a href=\"aim:goim?screenname=$online_user_aim&amp;message=Hi!+I+saw+you+from+$aim_site_name\">AIM</a> | ";
} else {
$user_contact_aim = "";
}
if ( isset ( $user_info[user_id] ) ) {
$last_activity_contact = "$user_contact_aim<a href=\"$site_path/pm_compose&amp;to=$online_user[username]\">PM</a>";
} else {
$last_activity_contact = str_replace ( "|", "", $user_contact_aim );
}
echo "<td><a href=\"$site_path/member&ampid=$online_user[username]\">$last_activity_username</a></td>";
if ( !ereg ( ":split:", $online_user[last_activity_title] ) ) {
$last_activity_location = "Page";
if ( empty ( $online_user[last_activity_title]  ) ) {
$last_activity_title = str_replace ( "$site_url/", "", $last_activity_url );
if ( strlen ( $last_activity_title ) > 40 ) {
$last_activity_title = "".substr ( $last_activity_title, 0, 39 )."...";
}
} else {
$last_activity_title = $online_user[last_activity_title];
}
} else {
list ( $last_activity_location, $last_activity_title ) = explode ( ":split:", $online_user[last_activity_title] );
} 
echo "<td>Viewing $last_activity_location:<br />&raquo; <a href=\"$last_activity_url\">$last_activity_title</a></td>";
echo "<td width=\"110\" align=\"center\">$last_activity_time</td>";
echo "<td align=\"center\">$last_activity_contact</td></tr>";
}

$x = 1;
while ( $online_guests = mysql_fetch_array ( $result_online_guests ) ) {
$last_activity_time = DisplayDate( "$online_guests[last_activity_time]", "M d Y, h:i:s A", "1" );
$last_activity_url = $online_guests[last_activity_url];
echo "<td>Guest</td>";
if ( !ereg ( ":split:", $online_guests[last_activity_title] ) ) {
$last_activity_location = "Page";
if ( empty ( $online_guests[last_activity_title]  ) ) {
$last_activity_title = str_replace ( "$site_url/", "", $last_activity_url );
if ( strlen ( $last_activity_title ) > 40 ) {
$last_activity_title = "".substr ( $last_activity_title, 0, 39 )."...";
}
} else {
$last_activity_title = $online_guests[last_activity_title];
}
} else {
list ( $last_activity_location, $last_activity_title ) = explode ( ":split:", $online_guests[last_activity_title] );
} 
echo "<td>Viewing $last_activity_location:<br />&raquo; <a href=\"$last_activity_url\">$last_activity_title</a></td>";
echo "<td width=\"110\" align=\"center\">$last_activity_time</td>";
echo "<td align=\"center\"></td></tr>";
$x++;
}

echo "</table>";

} else {

$x = 1;

if ( $total_online_members != 1 ) {
$members_online = "$total_online_members members";
} else {
$members_online = "$total_online_members member";
}
if ( $total_online_guests != 1 ) {
$guests_online = "$total_online_guests guests";
} else {
$guests_online = "$total_online_guests guest";
}
if ( $total_online != 1 ) {
echo "There are currently <b>$total_online</b> users online ($members_online, $guests_online)<br />";
} else {
echo "There is currently <b>$total_online</b> user online ($members_online, $guests_online)<br />";
}

while ( $online_user = mysql_fetch_array ( $result_online_users ) ) {
if ( $online_user[type] == 1 || $online_user[type] == 2 ) {
$last_activity_username = $online_user[username];
} elseif ( $online_user[type] == 3 ) {
$last_activity_username = "<b>$online_user[username]</b>";
} elseif ( $online_user[type] == 4 ) {
$last_activity_username = "<b><u>$online_user[username]</u></b>";
} elseif ( $online_user[type] == 5 ) {
$last_activity_username = "<b><i>$online_user[username]</i></b>";
}
echo "<a href=\"$site_path/member&amp;id=$online_user[username]\">";
if ( mysql_num_rows ( $result_online_users ) != $x ) { 
echo "$last_activity_username, ";
} else {
echo "$last_activity_username";
}
echo "</a>";
$x++;
}

}
?>
