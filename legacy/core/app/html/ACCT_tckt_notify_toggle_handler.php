<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");



//error_reporting(0);

$i_contact = ActFactory::getIContact();
$i_contact->updateExternalContactTicketNotify(getRackSessionContactID(),
    $external_contact_primary_id);
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
