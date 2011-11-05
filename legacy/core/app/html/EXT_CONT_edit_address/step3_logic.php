<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");
$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();
$contact = $i_contact->getExternalContact( $GLOBAL_db, $SESSION_contact_id);
$externalContacts = $i_contact->getExternalContacts( $GLOBAL_db, $contact->companyId );

$unique_contact_list = array();

foreach ($externalContacts as $e) {
   if(!in_array($e->individual->primaryId, $unique_contact_list)) {
      $unique_contact_list[$e->individual->primaryId] = $e;
   }
}

session_register("SESSION_contact_list");

if(!isset($SESSION_contact_list)) {
   $SESSION_contact_list = array();
   $SESSION_contact_list[$SESSION_individual_id] = True;
}

$contact_list = '<select multiple name="contact_list[]" size="10">'."\n";

function contactOption($contact) {
   global $SESSION_contact_list;

   if(!array_key_exists($contact->individual->primaryId, $SESSION_contact_list)) {
      $SESSION_contact_list[$contact->individual->primaryId] = False;
   }

   $selected = "";

   if($SESSION_contact_list[$contact->individual->primaryId]) {
      $selected = " selected";
   }

   return "<option value=\"". $contact->individual->primaryId ."\"$selected>". $contact->individual->firstName ." ". $contact->individual->lastName ."\n";
}

if(array_key_exists($SESSION_individual_id, $unique_contact_list)) {
   $contact_list .= contactOption($unique_contact_list[$SESSION_individual_id]);
}

foreach ($unique_contact_list as $contact) {
   if($contact->individual->primaryId != $SESSION_individual_id) {
      $contact_list .= contactOption($contact);
   }
}

$contact_list .= "</select>\n";

/* Next Step! */
session_register("SESSION_step");
$step = $SESSION_step;

?>
