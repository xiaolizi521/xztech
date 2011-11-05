<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");
$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();

checkDataOrExit(array('account_number' => "An account number (Data Missing)",
                      'external_contact_primary_id' => "A Contact ID (Something's gone very wrong!)",
                      'individual_id' => "An individual id (Something's gone very wrong!)"));

$contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);
$contact_name=$contact->individual->getFullName();
$role_name = $contact->getRoleName();
$account_name_value = $contact->primaryCompanyName;
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);

$hidden_tags  = '<input type="hidden" name="individual_id"' .
                ' value="'. $individual_id . '">';
$hidden_tags  .= '<input type="hidden" name="external_contact_primary_id"' .
                ' value="'. $external_contact_primary_id . '">';
$hidden_tags  .= '<input type="hidden" name="account_number"' .
                ' value="'. $account_number . '">';
?>
