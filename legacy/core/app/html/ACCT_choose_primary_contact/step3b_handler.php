<?php

require_once("CORE_app.php");

session_register("SESSION_last_name");
session_register("SESSION_step");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step1_page.php?last_name=" . urlencode($SESSION_last_name));
} else {
    if(!empty($first_name) and !empty($last_name)) {
        session_register("SESSION_person_fname");
        session_register("SESSION_person_lname");
        session_unregister("SESSION_person_id");
        session_unregister("SESSION_contact_id");
        $SESSION_person_fname = $first_name;
        $SESSION_person_lname = $last_name;
        ForceReload("step6_page.php");
    } else {
        ForceReload($HTTP_REFERER);
    }
}

?>
