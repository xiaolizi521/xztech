<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !isset ( $viewby ) && empty ( $viewby ) ) {
$result = mysql_query ( "SELECT username FROM users" );
} elseif ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( "^[a-z0-9\-_\.]+$", $viewby ) ) && ( $viewby != "other" ) ) {
$viewby = mysql_real_escape_string ( $_GET['viewby'] );
$result = mysql_query ( "SELECT username FROM users WHERE left( username, 1 ) = '$viewby'" );
} elseif ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( "^[a-z0-9\-_\.]+$", $viewby ) ) && ( $viewby == "other" ) ) {
$viewby = mysql_real_escape_string ( $_GET['viewby'] );
$result = mysql_query ( "SELECT username FROM users WHERE left( username, 1 ) BETWEEN '0' AND '9' OR left( username, 1 ) = '_'" );
} 

$limit = 50;
$members_num = mysql_num_rows( $result );
$pgs_num = ceil ( $members_num/$limit );

if ( isset ( $pg ) && !empty ( $pg ) && ( $pg > 0 ) && ( $pg <= $pgs_num ) ) {
$offset = ( ( $pg * $limit ) - $limit );
} else {

if ( $pg <= 0 ) {
$offset = 0;
$pg = 1;
} elseif ( $pg > $pgs_num ) {
$offset = ( ( $pgs_num * $limit ) - $limit );
$pg = $pgs_num;
}

}

if ( !isset ( $viewby ) && empty ( $viewby ) ) {
$result_members = mysql_query ( "SELECT * FROM users ORDER BY username LIMIT $offset, $limit" );
} elseif ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( "^[a-z0-9\-_\.]+$", $viewby ) ) && ( $viewby != "other" ) ) {
$viewby = mysql_real_escape_string ( $_GET['viewby'] );
$result_members = mysql_query ( "SELECT * FROM users WHERE left( username, 1 ) = '$viewby' ORDER BY username LIMIT $offset, $limit" );
} elseif ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( "^[a-z0-9\-_\.]+$", $viewby ) ) && ( $viewby == "other" ) ) {
$viewby = mysql_real_escape_string ( $_GET['viewby'] );
$result_members = mysql_query ( "SELECT * FROM users WHERE left( username, 1 ) BETWEEN '0' AND '9' OR left( username, 1 ) = '_' ORDER BY username LIMIT $offset, $limit" );
} 

$result_count_total = mysql_query ( "SELECT * FROM users ORDER BY registered_on DESC" );
$member_latest = mysql_fetch_array ( $result_count_total );

$letters = array( "other", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z" );

$spacer = "  ";
echo "<center>";

foreach ( $letters as $letter ) {
if ( $viewby == $letter ) {
echo "<font size='3'><b>[<a href='$site_url/$main_filename?page=memberlist&viewby=$letter'>".strtoupper( $letter )."</a>]</b></font>$spacer";
} else {
echo "<b><a href='$site_url/$main_filename?page=memberlist&viewby=$letter'>".strtoupper( $letter )."</a></b>$spacer";
}
}

echo "<a href='$site_url/$main_filename?page=memberlist'><b>List All</b></a></center><p>";

echo "<center>There are <b>".mysql_num_rows ( $result_count_total )."</b> registered members with the newest being <b><a href='#viewmember' onclick='ViewMember( \"$member_latest[username]\" )'>$member_latest[username]</b></a>, who registered on <b>$member_latest[registered_on]</b><p></center>";

echo "<hr noshade color='#C3C3C3' size='1' width='100%'>";
echo "Pages: ";
for ( $x = 1; $x <= $pgs_num; $x++ ) {
if ( $pg == $x ) {
echo "<b>[$x] </b>";
} else {
if ( !isset ( $viewby ) && empty ( $viewby ) ) {
echo "<a href='$site_url/$main_filename?page=memberlist&pg=$x'>$x</a> ";
} else {
echo "<a href='$site_url/$main_filename?page=memberlist&viewby=$viewby&pg=$x'>$x</a> ";
}
}
}
echo "<hr noshade color='#C3C3C3' size='1' width='100%'>";
?>

<table width="100%" cellpadding="5" cellspacing="0" class="main">
<tr>
<td bgcolor="<?php echo $tableheadercolor ?>" class="secondary"><b><u>Username</u></b></td>
<td bgcolor="<?php echo $tableheadercolor ?>" class="secondary"><b><u>Joined</u></b></td>
<td bgcolor="<?php echo $tableheadercolor ?>" class="secondary"><b><u>Type</u></b></td>
<td bgcolor="<?php echo $tableheadercolor ?>" class="secondary"><b><u>Email Address</u></b></td>
</tr>

<?php
while ( $member = mysql_fetch_array ( $result_members ) ) {
$register_date = $member['registered_on']; 
$registered_on = date ( 'm/d/y', strtotime( $register_date ) ); 
echo "<tr>
<td><a href='#viewmember' onclick='ViewMember( \"$member[username]\" )'>$member[username]</a></td>
<td>$registered_on</td>
<td>";
if ( $member['type'] == "1" ) {
echo "Member";
} elseif ( $member['type'] == "2" ) {
echo "News Poster";
} elseif ( $member['type'] == "3" ) {
echo "Admin";
}
echo "</td>
<td>$member[email_address]</td>
</tr>";
}
?>
</table>














































