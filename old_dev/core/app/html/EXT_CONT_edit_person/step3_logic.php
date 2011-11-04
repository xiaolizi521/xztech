<?php

require_once('CORE_app.php');



session_register("SESSION_first_name");
session_register("SESSION_last_name");
session_register("SESSION_contact_id");
session_register("SESSION_individual_id");
session_register("SESSION_step");
$step = $SESSION_step;

if( empty($SESSION_first_name) or
    empty($SESSION_last_name) ) {

        $contact = new CONT_Contact;
        $contact->loadID( $SESSION_contact_id );
        
        $person =& $contact->getPerson();
}

if( empty($SESSION_first_name) ) {
        $first_name = $person->getFirstName();
} else {
        $first_name = $SESSION_first_name;
}

if( empty($SESSION_last_name) ) {
        $last_name = $person->getLastName();
} else {
        $last_name = $SESSION_last_name;
}

$first_name = trim($first_name);
$last_name = trim($last_name);

?>
