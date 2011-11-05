<?php

require_once("CORE_app.php");
require_once('helpers.php');
unset($SESSION_last_name);
unset($SESSION_first_name);
session_unregister("SESSION_last_name");
session_unregister("SESSION_first_name");
session_unregister("SESSION_job_title");
session_unregister("SESSION_street1");
session_unregister("SESSION_street2");
session_unregister("SESSION_street3");
session_unregister("SESSION_city");
session_unregister("SESSION_state");
session_unregister("SESSION_zip");
session_unregister("SESSION_phone_number");
session_unregister("SESSION_emer_phone_number");
session_unregister("SESSION_fax_phone_number");
session_unregister("SESSION_email");
session_unregister("SESSION_emer_email");
session_unregister("SESSION_question");
session_unregister("SESSION_answer");
session_unregister("SESSION_country_id");
session_unregister("SESSION_lock_account_role");
session_unregister("SESSION_old_contact_id");


session_register("SESSION_step");
session_register("SESSION_account_id");


if (!empty($account_id)) {
    $SESSION_account_id = $account_id;
}

if (empty($SESSION_account_id)) {
    print "Error: account variable missing";
    exit();
}

$step = $SESSION_step = 1;

if(empty($SESSION_last_name)) {
    $last_name = '';
} else {
    $last_name = $SESSION_last_name;
}

if(empty($SESSION_first_name)) {
    $first_name = '';
} else {
    $first_name = $SESSION_first_name;
}


?>
