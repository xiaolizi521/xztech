<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");
session_register("SESSION_country_id");
session_register("SESSION_contact_id");
$i_account = ActFactory::getIAccount();
$contact = ActFactory::getIContact();
$contact = $contact->getExternalContact( $GLOBAL_db, $SESSION_contact_id);
$address = $contact->individual->getPrimaryAddress();
$regionCode = '';
if ( $address && ($address->countryCode == $SESSION_country_id )) {
    $regionCode = $address->regionCode;
}

$country_options = "";
$selected_flag = "";
$state_options = "";
$stateLookups = $i_account->getLookupValues('region-'.$SESSION_country_id);
for($i=0; $i<count($stateLookups); $i++) {
    if( $stateLookups[$i]->parameter_id == $regionCode ) {
        $selected_flag = " selected ";
    } else {
        $selected_flag = ' ';
    }    
        $state_options .= '<OPTION value="'.$stateLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$stateLookups[$i]->desc.'</OPTION>';
}
if (count($stateLookups)==0)
{
    $state_options .= '<OPTION value="" selected>NONE</OPTION>"';
}

$addressTypeLookups = $i_account->getLookupValues('individual.addresstype');
$address_type_options = "";
for($i=0; $i<count($addressTypeLookups); $i++) {
        $address_type_options .= '<OPTION value="'.$addressTypeLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$addressTypeLookups[$i]->desc.'</OPTION>';
}

//session_start();

session_register("SESSION_address_id");
//$address =& new CONT_Address;
//$address->loadID( $SESSION_address_id );

session_register("SESSION_street1");
if( !empty( $SESSION_street1 ) ) {
        $street1 = $SESSION_street1;
} else {
        $street1 = ereg_replace( "\n", " ", $address->address1);
}
if( !empty( $SESSION_street2 ) ) {
        $street2 = $SESSION_street2;
} else {
        $street2 = ereg_replace( "\n", " ", $address->address2);
}
if( !empty( $SESSION_street3 ) ) {
        $street3 = $SESSION_street3;
} else {
        $street3 = ereg_replace( "\n", " ", $address->address3);
}
session_register("SESSION_city");
if( !empty( $SESSION_city ) ) {
        $city = $SESSION_city;
} else {
        $city = $address->city;
}

session_register("SESSION_zip");
if( !empty( $SESSION_zip ) ) {
      $zip = $SESSION_zip;
} else {
        $zip = $address->postCode;
}


/* Next Step! */
session_register("SESSION_step");
$step = $SESSION_step;

?>
