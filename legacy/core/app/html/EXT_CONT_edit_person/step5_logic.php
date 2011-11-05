<?php

require_once('CORE_app.php');
require_once("act/ActFactory.php");

session_register("SESSION_account_id");
session_register("SESSION_contact_id");
session_register("SESSION_step");
$step = $SESSION_step;

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $SESSION_account_id);
$account_name = $account->account_name;

$contact = new CONT_Contact;
$contact->loadID( $SESSION_contact_id );

$person = $contact->getPerson();
$contact_name = $person->getFirstName() .  ' ' . $person->getLastName();

?>
