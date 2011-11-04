<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");

session_register("SESSION_step");
session_register("SESSION_account_id");
session_register("SESSION_contact_id");
session_register("SESSION_last_name");
session_register("SESSION_first_name");

if (!empty($contact_id)) {
    $SESSION_contact_id = $contact_id;
}

$step = $SESSION_step + 1;

if (empty($SESSION_account_id)) {
    trigger_error('Account ID missing in session', ERROR);
}

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $SESSION_account_id);
$accountName = $account->account_name;
$accountNumber = $account->account_number;

$contactName = $SESSION_first_name . " " . $SESSION_last_name;

?>
