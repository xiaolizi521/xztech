<?php

require_once("CORE_app.php");

session_register("SESSION_contact_id");
session_register("SESSION_secret");
session_register("SESSION_step");

if(!empty($back)) {
    $SESSION_step -= 1;
    if(!empty($SESSION_contact_id)) { // either they picked an existing contact
        ForceReload("step2_page.php?last_name=$SESSION_last_name&first_name=$SESSION_first_name");
    } else {
        ForceReload("step9_page.php");
    }
} elseif(!empty($finish)) {
        session_register("SESSION_account_id");

    if( !empty($role_id) ) {
        ForceReload("add_contact_handler.php?$QUERY_STRING");
    } else {
        ForceReload($HTTP_REFERER);
    }
}

?>
