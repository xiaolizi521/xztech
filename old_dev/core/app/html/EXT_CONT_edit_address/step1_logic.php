<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");

$i_account = ActFactory::getIAccount();
//session_start();

session_register("SESSION_country_name");
session_register("SESSION_country_id");

$contact = ActFactory::getIContact();
$contact = $contact->getExternalContact( $GLOBAL_db, $SESSION_contact_id);
$address = $contact->individual->getPrimaryAddress();
$country_code = 'US'; # default country 
if ( $address ) {
    $country_code = $address->countryCode;
}
$country_options = "";
$countries = $i_account->countryRetrieveList();

foreach ($countries as $country) {
    if ( $country['countryCode'] == $country_code ) {
        $selected_flag = " selected ";
    } else {
        $selected_flag = " ";
    }
    $country_options .= '<OPTION value="'.$country['countryCode'].'"'.$selected_flag.'>'.$country['name'].'</OPTION>';
}

/* Next Step! */
session_register("SESSION_step");
$step = $SESSION_step;

?>
