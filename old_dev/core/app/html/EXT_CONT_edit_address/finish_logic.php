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

function contactName($contact) {
   return $contact->individual->firstName ." ". $contact->individual->lastName ."<br>\n";
}

$contact_list = "";

if(array_key_exists($SESSION_individual_id, $SESSION_contact_list) && $SESSION_contact_list[$SESSION_individual_id]) {
   if(array_key_exists($SESSION_individual_id, $unique_contact_list)) {
      $contact_list .= contactName($unique_contact_list[$SESSION_individual_id]);
   }
}

foreach ($SESSION_contact_list as $key => $val) {
   if($key != $SESSION_individual_id) {
      if($val && array_key_exists($key, $unique_contact_list)) {
         $contact_list .= contactName($unique_contact_list[$key]);
      }
   }
}

/* Next Step! */
session_register("SESSION_step");
$step = $SESSION_step;

include("finish_include.php");

?>
