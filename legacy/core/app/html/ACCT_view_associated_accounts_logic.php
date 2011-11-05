<?php

require_once("CORE_app.php");

if( empty($contact_id) and empty($team_id) ) {
        trigger_error("You must include a <i>contact_id</i> or <i>team_id</i> to view this page", FATAL);
}


if( !empty($contact_id) ) {
        $contact = new CONT_Contact;
        $contact->loadID( $contact_id );
        $person = $contact->getPerson();
        $name = $person->getFirstName() . " " . $person->getLastName();
}

if( !empty($team_id) ) {
        $team = new ACCT_Team;
        $team->loadID( $team_id );
        $name = "Team ".$team->getName();
}


?>