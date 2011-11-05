<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

checkDataOrExit( array( 'external_contact_primary_id' => "External Contact Primary ID",
                        'individual_id' => "Individual ID",
                        'phone_number' => "Phone Number",
                        'phone_type_name' => "Phone Type Name",
                        'phone_type_id' => "Phone Type Id" ));

$i_contact = ActFactory::getIContact();
$external_contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);

makevar('first_name', $external_contact->individual->firstName);
makevar('last_name', $external_contact->individual->lastName);
        
//These are used on the page
makevar('phone', $phone_number);        
makevar('phone_type', $phone_type_name);             
             
$hidden_tags  = '<input type="hidden" name="external_contact_primary_id"' .
                ' value="'. $external_contact_primary_id . '">';
$hidden_tags .= '<input type="hidden" name="individual_id"' .
                ' value="'. $individual_id . '">';
$hidden_tags .= '<input type="hidden" name="phone_number"' .
                ' value="'. $phone_number . '">';
$hidden_tags .= '<input type="hidden" name="phone_type_id"' .
                ' value="'. $phone_type_id . '">';                                      

?>
