<?php

require_once('CORE_app.php');


session_register("SESSION_contact_id");
session_register("SESSION_account_id");
session_register("SESSION_role_id");
session_register("SESSION_step");


if( !empty($back) ) {
        $SESSION_step -= 1;
        ForceReload("step4_page.php");
} elseif( !empty($next) ) {
        # Jump to "Add Contact"
        JSForceReload("/ACCT_add_contact_onyx/step1_page.php" . 
                      "?old_contact_id=$SESSION_contact_id" .
                      "&lock_account_role=$SESSION_role_id" .
                      "&account_id=$SESSION_account_id");
} else {
        ForceReload($HTTP_REFERER);
}


?>
