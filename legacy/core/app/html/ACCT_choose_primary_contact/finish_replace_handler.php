<?php

require_once("CORE_app.php");

session_register("SESSION_person_id");
session_register("SESSION_contact_id");
session_register("SESSION_secret");
session_register("SESSION_step");

if(!empty($back)) {
    $SESSION_step -= 1;
    if(!empty($SESSION_contact_id)) { // either they picked an existing contact
        ForceReload("finish_page.php?person_id=$SESSION_person_id");
    } else {
        ForceReload("step9_page.php");
    }
} elseif(!empty($finish)) {
        ForceReload("add_contact_handler.php");
}

?>
