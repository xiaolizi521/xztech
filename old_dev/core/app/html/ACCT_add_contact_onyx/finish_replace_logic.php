<?php

require_once("CORE_app.php");


session_register("SESSION_step");
$step = $SESSION_step + 1;

session_register("SESSION_old_contact_id");
session_register("SESSION_person_id");
session_register("SESSION_person_fname");
session_register("SESSION_person_lname");
session_register("SESSION_contact_id");
session_register("SESSION_lock_account_role");

if( empty( $SESSION_person_fname ) or
    empty( $SESSION_person_lname ) ) {
    if( empty( $SESSION_person_id ) ) {
        $contact =& new CONT_Contact;
        $contact->loadID( $SESSION_contact_id );
        $person =& $contact->getPerson();
        $last_name = $person->getLastName();
        $first_name = $person->getFirstName();
        $is_new = false;
    } else {
        $person =& new CONT_Person;
        $person->loadID( $SESSION_person_id );
        $last_name = $person->getLastName();
        $first_name = $person->getFirstName();
        $is_new = true;
    }
} else {
    $last_name = $SESSION_person_lname;
    $first_name = $SESSION_person_fname;
    $is_new = true;
}
$new_name = "$first_name $last_name";

$contact =& new CONT_Contact;
$contact->loadID( $SESSION_old_contact_id );
$person =& $contact->getPerson();
$last_name = $person->getLastName();
$first_name = $person->getFirstName();
$old_name = "$first_name $last_name";

$role_obj =& new ACCT_AccountRole;
$role_obj->loadID($SESSION_lock_account_role);
$role = $role_obj->getName();

// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
