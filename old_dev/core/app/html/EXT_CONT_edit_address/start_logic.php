<?php

require_once("CORE_app.php");

/* Check Data */
require_once('helpers.php');
#checkDataOrExit( array( 'external_contact_primary_id' => "External Contact Primary ID" ) );
#checkDataOrExit( array( 'individual_id' => "Individual ID" ) );

// session_start();

// Assume this is a new instance of the wizard

session_unregister("SESSION_contact_list");
session_unregister("SESSION_external_contact_primary_id");
session_unregister("SESSION_contact_id");
session_unregister("SESSION_individual_id");
session_unregister("SESSION_street");
session_unregister("SESSION_city");
session_unregister("SESSION_state");
session_unregister("SESSION_zip");
session_unregister("SESSION_country_id");
session_unregister("SESSION_step");
session_unregister("SESSION_street1");
session_unregister("SESSION_street2");
session_unregister("SESSION_street3");
session_unregister("SESSION_step");
session_unregister("SESSION_country_name");
session_unregister("SESSION_address_id");

session_register("SESSION_external_contact_primary_id");
session_register("SESSION_contact_id");
session_register("SESSION_individual_id");
session_register("SESSION_street");
session_register("SESSION_city");
session_register("SESSION_state");
session_register("SESSION_zip");
session_register("SESSION_country_id");

if ( isset( $external_contact_primary_id ) ) {
    $SESSION_contact_id = $external_contact_primary_id;
}
if ( isset( $individual_id ) ) {
    $SESSION_individual_id = $individual_id;
}
/* Initialize Wizard Steppings */
session_register("SESSION_step");
$step = $SESSION_step = 1;

?>
