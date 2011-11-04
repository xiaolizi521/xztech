<?php
function DisplayDate( $timestamp, $display_format, $display_format_option ) { 
global $user_info;

$days_passed = ( ( time() - $timestamp ) / 86400 );

if ( $display_format_option == 1 ) {
	if ( $days_passed <= 1 ) {
		$display_format = "\T\o\d\a\y\, h:i A";
	}
	elseif ( $days_passed <= 2 ) {
		$display_format = "\Y\e\s\\t\e\\r\d\a\y, h:i A";
	}
	else {
		$display_format = $display_format;
}
}  
if ( isset ( $user_info['user_id'] ) ) {
	if ( $user_info['dst'] == 1 ) {
		$timezone = ( $user_info['timezone'] + date( "I" ) );
	}
	elseif ( $user_info[dst] == 0 ) {
		$timezone = $user_info['timezone'];
}
}
else {
	$timezone = ( date( "O" )/100 + date( "I" ) ); //EST Zone setting
}
$zone = 3600 * $timezone;
$datetime = (int)$timestamp;
$date = gmdate ( $display_format, $datetime + $zone );
return $date;
} 
?>