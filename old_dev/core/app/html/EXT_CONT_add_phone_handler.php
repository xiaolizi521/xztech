<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

$concatenated_phone_number = "";
while(list($key,$val) = @each($phone_number)) {
    $concatenated_phone_number .= $val;
}

$i_contact = ActFactory::getIContact();

$primary_flag = "";
$i_contact->addExternalContactPhone(
    getRackSessionContactID(), 
    $SESSION_individual_id, 
    $concatenated_phone_number, 
    '' /* country is unused */, 
    $type_name,
    $primary_flag);

?>
<script language="javascript">
window.opener.document.location = window.opener.document.location;
window.close();
</script>
