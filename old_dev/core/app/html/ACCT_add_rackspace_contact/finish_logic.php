<?php

require_once('CORE_app.php');
require_once("act/ActFactory.php");

session_register("SESSION_account_id");
session_register("SESSION_account_role");
session_register("SESSION_contact_id");

$account_id = $SESSION_account_id;
$account_role = $SESSION_account_role;
$contact_id = $SESSION_contact_id;

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $account_id);
$account_number = $account->account_number;
$account_name = $account->account_name;

$ar = new ACCT_AccountRole;
$ar->loadID( $account_role );
$account_role_name = $ar->getName();
if( eregi( "^[aeiou]", $account_role_name ) ) {
    $use_an = true;
} else {
    $use_an = false;
}

$cont = new CONT_Contact;
$cont->loadID( $contact_id );
$contact_name = $cont->getName();

?>