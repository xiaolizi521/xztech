<?php

require_once('CORE_app.php');


session_register("SESSION_step");

session_register("SESSION_account_id");
session_register("SESSION_role_id");

if( !empty($SESSION_account_id) and !empty($SESSION_role_id) ) {
    // Ask what the user wants.
    ForceReload("step1_page.php");
} else {
    // Go straight to edit name.
    ForceReload("step3_page.php");
}

// Local Variables:
// mode: php 
// c-basic-offset: 4
// indent-tabs-mode: nil 
// End:
?>