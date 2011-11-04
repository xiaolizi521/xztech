<?php

require_once("CORE_app.php");
require_once('helpers.php');

session_register("SESSION_job_title");
session_register("SESSION_street1");
session_register("SESSION_street2");
session_register("SESSION_street3");
session_register("SESSION_city");
session_register("SESSION_state");
session_register("SESSION_zip");
session_register("SESSION_country_id");
session_register("SESSION_step");

$step = $SESSION_step + 1;

if(empty($SESSION_job_title)) {
    $job_title = '';
} else {
    $job_title = $SESSION_job_title;
}

if (empty($SESSION_country_id ) ) {
    $country_id = 'US';
}

$street1 = $street2 = $street3 = $city = $state = $zip = '';
$iAccount = ActFactory::getIAccount();
if( !empty($SESSION_street1) ) {
        $street1 = $SESSION_street1;
}
if( !empty($SESSION_street2) ) {
        $street2 = $SESSION_street2;
}
if( !empty($SESSION_street3) ) {
        $street3 = $SESSION_street3;
}

if( !empty($SESSION_city) ) {
        $city = $SESSION_city;
}
if( !empty($SESSION_state) ) {
        $state = $SESSION_state;
}
if( !empty($SESSION_zip) ) {
        $zip = $SESSION_zip;
}
if( !empty($SESSION_country_id) ) {
        $country_id = $SESSION_country_id;
}

$countries = $iAccount->countryRetrieveList();
$regions = $iAccount->getLookupValues("region-$country_id");
$method = 'http://';
if (array_key_exists('HTTPS', $_SERVER ) ) {
    $method = 'https://';
}
$div_url = $method . $_SERVER['SERVER_NAME'] . "/ACCT_add_contact_onyx/get_regions_for_country.php";
?>
