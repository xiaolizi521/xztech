<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

session_register("SESSION_contact_id");
session_register("SESSION_role_id");
session_register("SESSION_person_fname");
session_register("SESSION_person_lname");
session_register("SESSION_account_id");

$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();

checkDataOrExit(array('account_id' => "An account id (Data Missing)",
                      'external_contact_primary_id' => "A Contact ID (Something's gone very wrong!)",
                      'individual_id' => "An individual id (Something's gone very wrong!)"));

$contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);
$contact_name=$contact->individual->getFullName();
$role_name = $contact->getRoleName();
#$account_name_value = $contact->primaryCompanyName;
#$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);


$roles = array();
$roles[] = ONYX_ACCOUNT_ROLE_PRIMARY;

$SESSION_account_id = $account_id;
$SESSION_contact_id = $individual_id;
$SESSION_role_id = $roles;
$SESSION_person_fname = $contact->individual->firstName;
$SESSION_person_lname = $contact->individual->lastName;

?>

