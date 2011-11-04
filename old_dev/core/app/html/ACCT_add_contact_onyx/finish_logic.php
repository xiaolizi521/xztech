<?php

require_once("CORE_app.php");

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
global $GLOBAL_db;
$iAccount = ActFactory::getIAccount();
$account = $iAccount->getAccountByAccountId( $GLOBAL_db, $SESSION_account_id );
if ( $account->getPrimaryContact() ) {
    $has_primary_contact = true;
} else {
    $has_primary_contact = false;
}
$result = $iAccount->getLookupValues("externalcontact.types");

$role_list_options = "";
foreach ($result as $item) {
    $id = $item->parameter_id;
    $desc = $item->desc;
    $role_list_options .= "<option value=\"$id\">$desc</option>";
}

?>
