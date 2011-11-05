<?php

require_once('CORE_app.php');
require_once('act/ActFactory.php');


session_register("SESSION_contact_id");
session_register("SESSION_individual_id");
session_register("SESSION_role_id");
session_register("SESSION_step");

if( !empty( $back ) ) {
        $SESSION_step -= 1;
        ForceReload("step2_page.php");
        exit;
}

$first_name = stripslashes(trim($first_name));
$last_name = stripslashes(trim($last_name));

    $i_account = ActFactory::getIAccount();
    $i_contact = ActFactory::getIContact();
    $i_contact->updateExternalContactName(getRackSessionContactID(), $SESSION_individual_id, $first_name, $last_name);

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
