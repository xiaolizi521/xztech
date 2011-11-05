<?php

	require_once ( './browser.php' );

$br = new Browser;

// Check if browser is Internet Explorer
if ( $br->Name == 'MSIE' ) {
	// IE 6 and below
	if ( $br->Version == '7.0' ) {
		echo "style_ie7.css";
	}
	else {
		echo "style_ie.css";
	}
}
// Firefox
elseif ( $br->Name == 'Firefox' ) {
	echo "style.css";
}
// Safari
elseif ( $br->Name == 'Safari' ) {
	echo "style_safari.css";
}
// Anything else
else {
	echo "style_other.css";
}
?>