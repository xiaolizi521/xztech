<?php

require_once('CORE_app.php');

session_register("SESSION_step");
$step = $SESSION_step;

session_register("SESSION_contact_id");
session_register("SESSION_individual_id");

// Number of contacts it'll impact.
//TODO: do we need to modify this to add the WHERE clase: WHERE "deprecated" = 'f' ?
$query = '
SELECT count(*)
FROM "CONT_Contact"
WHERE 
  "CONT_PersonID" in (SELECT "CONT_PersonID"
           FROM "CONT_Contact"
           WHERE "crm_individual_id" = '.$SESSION_individual_id.')
';
$num_contacts = $GLOBAL_db->GetVal($query);

if( $num_contacts <= 0 ) {
        $num_contacts = "Error";
}
if( $num_contacts > 1 ) {
        $contact_plural = 's';
} else {
        $contact_plural = '';
}

?>
