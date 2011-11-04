<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

$i_contact = ActFactory::getIContact();

$primary_flag = "";

$i_contact->addExternalContactEmail(getRackSessionContactID(),
$SESSION_individual_id, $email_address, $email_type_value,
$primary_flag);

?>
<script language="javascript">
window.opener.document.location = window.opener.document.location;
window.close();
</script>
