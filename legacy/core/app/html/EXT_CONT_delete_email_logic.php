<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");
checkDataOrExit( array( 'external_contact_primary_id' => "Contact ID" ) );
checkDataOrExit( array( 'individual_id' => "Individual ID" ) );
checkDataOrExit( array( 'email_address' => "Email Address" ) );
checkDataOrExit( array( 'email_type' => "Email Type" ) );
checkDataOrExit( array( 'email_type_id' => "Email Type ID" ) );

$contact = ActFactory::getIContact();
$contact = $contact->getExternalContact( $GLOBAL_db, $external_contact_primary_id);

if ( $contact->individual->getPrimaryEmailAddress() == $email_address ) {
        print "You cannot delete a Primary email address!";
        exit;
}

$hidden_tags  = '<input type="hidden" name="external_contact_primary_id"' .
                    ' value="'. $external_contact_primary_id . '">';
$hidden_tags  .= '<input type="hidden" name="individual_id"' .
                ' value="'. $individual_id . '">';
$hidden_tags  .= '<input type="hidden" name="email_type_id"' .
                ' value="'. $email_type_id. '">';
$hidden_tags  .= '<input type="hidden" name="email_address"' .
                ' value="'. $email_address. '">';
$hidden_tags  .= '<input type="hidden" name="email_type"' .
                ' value="'. $email_type. '">';
$hidden_tags .= '<input type="hidden" name="submitted" value="yes">';

?>
