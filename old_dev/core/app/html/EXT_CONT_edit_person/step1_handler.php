<?php

require_once('CORE_app.php');

if( empty( $choice ) ) {
        ForceReload($HTTP_REFERER);
        exit;
}


session_register("SESSION_step");
session_register("SESSION_choice");

$SESSION_step += 1;
if( $choice == "edit" ) {
        $SESSION_choice = "edit";
        ForceReload("step2_page.php");
} else {
        $SESSION_choice = "add";
        ForceReload("step4_page.php");
}

?>

