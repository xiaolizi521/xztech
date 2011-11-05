<?php

require_once("CORE_app.php");

session_register("SESSION_step");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step2_page.php");
    exit;
}

session_register("SESSION_test");

foreach (array_keys($SESSION_contact_list) as $contact) {
   if(in_array($contact, $contact_list)) {
      $SESSION_contact_list[$contact] = True;
   } else {
      $SESSION_contact_list[$contact] = False;
   }
}

$SESSION_step += 1;
ForceReload( "finish_page.php" );

?>
