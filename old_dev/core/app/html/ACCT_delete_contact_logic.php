<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

$contact = new CONT_Contact;
$role = new ACCT_AccountRole;
$i_account = ActFactory::getIAccount();

checkDataOrExit( array( 'account_number' =>
                  "An Account Number (Something's gone very wrong!)",
                  'role_id' =>
                  "A Role ID (Something's gone very wrong!)",
                  'contact_id' =>
                  "A Contact ID (Something's gone very wrong!)" ) );

$valid_role = $role->loadID( $role_id );
$valid_contact = $contact->loadID( $contact_id );
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$valid_account = ($account != null);

checkDataOrExit( array( 'valid_contact' => "Invalid Contact",
                  'valid_role' => "Invalid Role",
                  'valid_account' => "Invalid Account" ) );



makevar('account_name', $account->account_name);
$person = $contact->getPerson();
makevar( 'name', $person->getFirstName() . " " . $person->getLastName() );
makevar( 'role', $role->getName() );

$hidden_tags  = '<input type="hidden" name="account_number"' .
                ' value="'. $account_number . '">';
$hidden_tags  .= '<input type="hidden" name="contact_id"' .
                ' value="'. $contact_id . '">';
$hidden_tags  .= '<input type="hidden" name="role_id"' .
                ' value="'. $role_id . '">';

?>
