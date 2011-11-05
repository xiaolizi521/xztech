<?php

if ( isset ( $user_info['user_id'] ) ) {
	if ( $user_info['type'] >= 3 ) {
	

echo '<p style="text-align: left;"><span class="VerdanaSize2Main"><b> :: Logs :: </b></span><br />
<br />
Note that these are log messages - only <b>some</b> are errors<br />
Messages occuring due to a user that is not logged in show as "By: - ", otherwise shows Mods/Admin name. </p>
';

$query = 'SELECT * FROM `error_logs` ORDER BY `id` DESC';
$r = mysql_query ($query);
echo '<ul>
';
while ($e = mysql_fetch_array ($r)) {
	echo '	<li><div class="mBox">';
	if( $e['type']=='error' ) {
		echo '<span style="color: #ff0000;">';
	}
	echo 'Log id:<b>', $e['id'], '</b> Occured at: ', $e['date'], '<br />
		', $e['type'], ': ', $e['message'], '<br />
		From: ', $e['source'], ' By: ', $e['user'];
	if( $e['type'] == 'error' ){
		echo '</span>';
	}
	echo '</div></li>
';
}
echo '</ul>';

	}
	else { //if user is not logged on
		echo 'Only moderators and above may access this facility.';
	}

}
else { //if user is not logged on
	echo 'Please log in to use this facility';
}

?>
