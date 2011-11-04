<?php

require_once("CORE_app.php");

session_register("SESSION_step");
session_register("SESSION_contact_id");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step1_page.php");
} elseif(!empty($new)) {
    $SESSION_step += 1;
    ForceReload("step3b_page.php");
} elseif(!empty($next)) {
    if(!empty($contact_id)) {
        $SESSION_step += 1;
        $SESSION_contact_id = $contact_id;
        if( !empty( $SESSION_lock_account_role ) ) {
            ForceReload("finish_replace_page.php");
        }
        else {
            ForceReload("finish_page.php?contact_id=$contact_id");
        }
    } else {
        ForceReload($HTTP_REFERER);
    }
}

?>
