<?php
require_once("CORE_app.php");

session_register("SESSION_contact_id");

if( !empty($back) ) {
    ForceReload("step1_page.php");
    exit();
}

if( !empty($show_all) or !empty($hide_all) ) {
    if( !empty($contact_id) ) {
        $SESSION_contact_id = $contact_id;
    }
    ForceReload("step2_page.php?$QUERY_STRING");
    exit();
}

if( empty($contact_id) ) {
    ForceReload("step2_page.php");
    exit();
}

$SESSION_contact_id = $contact_id;

ForceReload("finish_page.php");

?>