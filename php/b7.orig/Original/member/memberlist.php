<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

$file_title = 'Memberlist';

$result_membercount = mysql_query ( 'SELECT `username`, `registered_on` FROM `users` ORDER BY `registered_on` DESC' );
$newest_member = mysql_fetch_array ( $result_membercount );

if ( !isset ( $viewby ) && empty ( $viewby ) ) {
//	$result = mysql_query ( 'SELECT `username` FROM `users`' );
}
else if ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( '^[a-z0-9\-_\.]+$', $viewby ) ) && ( $viewby != 'other' ) ) {
	$viewby = mysql_real_escape_string ( $_GET['viewby'] );
	$result = mysql_query ( 'SELECT `username` FROM `users` WHERE left( `username`, 1 ) = \'' . $viewby . '\'' );
}
else if ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( '^[a-z0-9\-_\.]+$', $viewby ) ) && ( $viewby == 'other' ) ) {
	$viewby = mysql_real_escape_string ( $_GET['viewby'] );
	$result = mysql_query ( 'SELECT `username` FROM `users` WHERE left( `username`, 1 ) BETWEEN \'0\' AND \'9\' OR left( `username`, 1 ) = \'_\'' );
} 

$limit = 100;
$members_num = mysql_num_rows( $result );
$pages_num = ceil ( $members_num/$limit );

if ( isset ( $viewby ) && !empty ( $viewby ) ) {
	$viewbyident = '&amp;viewby=' . $viewby;
}

if ( !isset ( $pg ) || empty ( $pg ) || $pg < 1 ) {
	header ( 'Location: ' . $site_path . '/memberlist' . $viewbyident . '&pg=1' );
	ob_end_flush();
}
elseif ( $pg > $pages_num ) {
	header ( 'Location: ' . $site_path . '/memberlist' . $viewbyident . '&pg=' . $pages_num );
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
	else if ( $pg > $pages_num ) {
		$offset = ( ( $pages_num * $limit ) - $limit );
		$pg = $pages_num;
	}
}

if ( !isset ( $viewby ) && empty ( $viewby ) ) {
	$result_members = mysql_query ( 'SELECT * FROM `users` ORDER BY `username` LIMIT ' . $offset . ', ' . $limit );
}
else if ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( '^[a-z0-9\-_\.]+$', $viewby ) ) && ( $viewby != 'other' ) ) {
	$viewby = mysql_real_escape_string ( $_GET['viewby'] );
	$result_members = mysql_query ( 'SELECT * FROM `users` WHERE left( `username`, 1 ) = \'' . $viewby . '\' ORDER BY `username` LIMIT ' . $offset . ', ' . $limit );
}
else if ( isset ( $viewby ) && !empty ( $viewby ) && ( eregi ( '^[a-z0-9\-_\.]+$', $viewby ) ) && ( $viewby == 'other' ) ) {
	$viewby = mysql_real_escape_string ( $_GET['viewby'] );
	$result_members = mysql_query ( 'SELECT * FROM `users` WHERE left( `username`, 1 ) BETWEEN \'0\' AND \'9\' OR left( `username`, 1 ) = \'_\' ORDER BY `username` LIMIT ' . $offset . ', ' . $limit );
} 

$letters = array( 'other', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' );

echo '<p style="width: 100%; text-align: center;">
';

foreach ( $letters as $letter ) {
	if ( $viewby == $letter ) {
		echo '	<a href="', $site_path, '/memberlist&amp;viewby=', $letter, '&amp;pg=1"><b><span style="text-decoration: underline;">', strtoupper( $letter ), '</span></b></a> 
';
	}
	else {
		echo '	<a href="', $site_path, '/memberlist&amp;viewby=', $letter, '&amp;pg=1"><b>', strtoupper( $letter ), '</b></a> 
';
	}
}

echo '<a href="', $site_path, '/memberlist&amp;pg=1"><b>List All</b></a></p>
';

echo '<p style="width: 100%; text-align: center;">There are <b>', mysql_num_rows ( $result_membercount ), '</b> registered members with the latest being <b><a href="', $site_path, '/member&amp;id=', $newest_member['username'], '">', $newest_member['username'], '</a></b>, who registered on <b>', DisplayDate( $newest_member['registered_on'], 'F d Y \a\\t h:i A', '0' ), '</b></p><br />
'; 

if ( mysql_num_rows ( $result ) == 0 ) {
	echo '<p style="width: 100%; text-align: center;">There are no members starting with that letter</p>';
}
else {

	Paginate( 'pg', $pages_num, 'memberlist' . $viewbyident );
?>

<table cellpadding="5" cellspacing="0" style="width: 100%;" class="VerdanaSize1Main">
	<tr>
		<td><b>Username</b></td>
		<td><b>Registered</b></td>
		<td style="text-align: center;"><b>Posts</b></td>
	</tr>
<?php
while ( $member = mysql_fetch_array ( $result_members ) ) {
	$Member = new B7_User ( $member );
	$member_username = $Member->getDisplay_Username();
	$registered_on = DisplayDate( $member['registered_on'], 'M d Y, h:i A', '1' );
	echo '	<tr>
		<td><a href="', $site_path, '/member&amp;id=', $member['username'], '">', $member_username, '</a></td>
		<td>', $registered_on, '</td>
		<td style="text-align: center;">', $member['posts'], '</td>
	</tr>
';
}
?>
</table>
<?php
}
?>
