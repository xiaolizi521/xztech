<?php

require_once("CORE_app.php");

session_start();

session_register("SESSION_external_contact_primary_id");
session_register("SESSION_street");
session_register("SESSION_city");
session_register("SESSION_state");
session_register("SESSION_zip");
session_register("SESSION_country_id");

$new_street = $SESSION_street;
$new_city = $SESSION_city;
$new_state = $SESSION_state;
$new_zip = $SESSION_zip;

/* Get new address */
$new_country_obj =& new CONT_Country;
$new_country_obj->loadID($SESSION_country_id);
$new_country = $new_country_obj->getAbbrev();

$new_address = "$new_street\n$new_city, $new_state $new_zip" .
"  $new_country";

/* Get old address */
$contact =& new CONT_Contact;
$contact->loadID( $SESSION_contact_id );
$address =& $contact->getSecret();

$old_street = $address->getStreet();
$old_city = $address->getCity();
$old_state = $address->getState();
$old_zip = $address->getPostalCode();

$old_country_obj =& $address->getCountry();
$old_country = $old_country_obj->getAbbrev();

$old_address = "$old_street\n$old_city, $old_state $old_zip  $old_country";


?>
