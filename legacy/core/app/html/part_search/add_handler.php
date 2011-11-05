<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("common.php");

checkDataOrExit( array( "not" => "A Logical Not (or not)",
                        "sku" => "One or More Products" ) );

if( $first_time ) {
    $SESSION_parts[] = array( "not" => $not, 
                              "sku" => $sku );
} else {
    checkDataOrExit( array( "logic" => "AND or OR" ) );
    $SESSION_parts[] = array( "logic" => $logic,
                              "not" => $not, 
                              "sku" => $sku );
}

// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
<HTML>
<HEAD>
<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function close_it() {
    window.close();
}
window.opener.location = window.opener.location;
//-->
</SCRIPT>
</HEAD>
<BODY onLoad="setTimeout(close_it,1)">
</BODY>
</HTML>
