<?php

require_once("CORE_app.php");

//session_start();
session_register("SESSION_step");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step1_page.php");
    exit;
}
if( empty( $street1 ) or
    empty( $city ) ) {
        ForceReload($HTTP_REFERER);
        exit;
}

session_register("SESSION_street1");
session_register("SESSION_street2");
session_register("SESSION_street3");
session_register("SESSION_city");
session_register("SESSION_state");
session_register("SESSION_zip");
session_register("SESSION_country_id");

$SESSION_street1 = stripslashes($street1);
$SESSION_street2 = stripslashes($street2);
$SESSION_street3 = stripslashes($street3);

$SESSION_city = stripslashes($city);
$SESSION_state = stripslashes($state);
$SESSION_zip = stripslashes($zip);
//$SESSION_country_id = stripslashes($country_id);

$SESSION_step += 1;
ForceReload( "step3_page.php" );

?>
