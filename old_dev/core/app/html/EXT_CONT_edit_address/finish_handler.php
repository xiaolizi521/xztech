<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");

//session_start();
session_register("SESSION_step");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step3_page.php");
    exit;
}

session_register("SESSION_contact_id");

include("finish_include.php");

$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();

foreach ($SESSION_contact_list as $key => $val) {
   if($val) {
      $i_contact->updateExternalContactAddress(
         getRackSessionContactID(), 
         $key, 
         "100136",
         $SESSION_street1, 
         $SESSION_street2,
         $SESSION_street3,
         "",
         "",
         $SESSION_city, 
         $SESSION_state, 
         $SESSION_zip, 
         $SESSION_country_id);
   }
}

session_destroy();
?>
<HTML>
<HEAD>
<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function close_it() { window.close(); }
window.opener.location = window.opener.location;
//-->
</SCRIPT>
</HEAD>
<BODY onLoad="setTimeout(close_it,1)">
</BODY>
</HTML>
