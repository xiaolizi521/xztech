<?php

require_once('CORE_app.php');

/* Check Data */
checkDataOrExit( array( 'external_contact_primary_id' => "Contact ID" ) );
        
// Assume this is a new instance of the wizard
session_unregister("SESSION_choice");
//session_unregister("SESSION_first_name");
//session_unregister("SESSION_last_name");

//session_register("SESSION_first_name");
//session_register("SESSION_last_name");
session_register("SESSION_contact_id");
session_register("SESSION_individual_id");
//$SESSION_last_name = $last_name;
//$SESSION_first_name = $first_name;
$SESSION_contact_id = $external_contact_primary_id;
$SESSION_individual_id = $individual_id;
$warning_replace_admin_who_is_primary = false;

if( !empty($role_id) and !empty($account_id) ) {
    if( $role_id == ONYX_ACCOUNT_ROLE_ADMINISTRATIVE ) {
        $i_account = ActFactory::getIAccount();
        $account = $i_account->getAccountByAccountId($GLOBAL_db, $account_id);
        $primaryContact = $account->getPrimaryContact();
        $isPrimary = ($contact_id == $primaryContact->contact_id);
                
        if($isPrimary) {
            $role_id = ONYX_ACCOUNT_ROLE_PRIMARY;
            $warning_replace_admin_who_is_primary = true;
        }
    }
    session_register("SESSION_role_id");
    $SESSION_role_id = $role_id;
} else {
    // If we don't have a role, then we 
    // can only rename a person.
    session_unregister("SESSION_role_id");
}

if( !empty($account_id) ) {
    session_register("SESSION_account_id");
    $SESSION_account_id = $account_id;
} else {
    // If we don't have an account, then we 
    // can only rename a person.
    session_unregister("SESSION_account_id");
}

/* Initialize Wizard Steppings */
session_register("SESSION_step");
$step = $SESSION_step = 1;

/*
Local Variables:
mode: php
c-basic-offset: 4
End:
*/
?>
