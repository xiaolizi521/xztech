<?php

require_once('CORE_app.php');



session_register("SESSION_choice");
if( empty($SESSION_choice) ) {
    $choice = '';
} else {
    $choice = $SESSION_choice;
}

/* Next Step! */
session_register("SESSION_step");
$step = $SESSION_step;

?>
