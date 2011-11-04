<?php
require_once("CORE_app.php");

session_register("SESSION_account_role");
if( empty($account_role) ) {
    ForceReload("step1_page.php");
    exit();
}

// Remove step2's settings.
session_unregister("SESSION_show_all_toggle");
session_unregister("SESSION_contact_id");

$SESSION_account_role = $account_role;

ForceReload("step2_page.php");

?>