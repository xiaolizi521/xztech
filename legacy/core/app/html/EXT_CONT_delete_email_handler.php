<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

$i_contact = ActFactory::getIContact();

if( !empty( $delete ) and
    $delete == 'yes' ) {
    $i_contact->deleteExternalContactEmail($external_contact_primary_id, 
                    $individual_id, $email_address, $email_type_id, '0' );    
}
?>
<script language="javascript">
window.opener.document.location = window.opener.document.location;
window.close();
</script>
