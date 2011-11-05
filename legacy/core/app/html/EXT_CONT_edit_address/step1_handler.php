<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");
$i_account = ActFactory::getIAccount();

if( empty( $country_id ) ) {
        ForceReload($HTTP_REFERER);
        exit;
}

session_register("SESSION_step");
session_register("SESSION_country_id");
session_register("SESSION_country_name");

$SESSION_country_id = $country_id;
$countries = $i_account->countryRetrieveList();
foreach ($countries as $country) { 
    if ($SESSION_country_id == $country['countryCode']) {
        $SESSION_country_name = $country['name'];
    }
}    

$SESSION_step += 1;
ForceReload("step2_page.php");
?>

