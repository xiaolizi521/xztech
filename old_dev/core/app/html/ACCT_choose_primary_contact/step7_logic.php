<?php

require_once("CORE_app.php");
require_once('helpers.php');

session_register("SESSION_primary_phone_number");
session_register("SESSION_primary_phone_type_id");
session_register("SESSION_country_id");
session_register("SESSION_step");

$step = $SESSION_step + 1;


$primary_phone_number = '';
if(!empty($SESSION_primary_phone_number)) {
    $primary_phone_number = $SESSION_primary_phone_number;
}
$primary_phone_type_id = '';
if(!empty($SESSION_primary_phone_type_id)) {
    $primary_phone_type_id = $SESSION_primary_phone_type_id;
}

$iAccount 	= ActFactory::getIAccount();
$phoneTypes = $iAccount->getLookupValues('individual.phonetype');
$primary_phone_type_id_options = '';
foreach($phoneTypes as $pt) {
    if($pt->parameter_id == $primary_phone_type_id) {
        $primary_phone_type_id_options .= '<option value="'.$pt->parameter_id.'" selected>'.$pt->desc."</option>";
    }
    else {
        $primary_phone_type_id_options .= '<option value="'.$pt->parameter_id.'">'.$pt->desc."</option>";
    }
}
?>
