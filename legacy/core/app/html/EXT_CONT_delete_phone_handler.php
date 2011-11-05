<?php
require_once("CORE_app.php");
require_once("act/ActFactory.php");

if(!empty($delete) and
    $delete == 'yes' ) {
    
    $i_contact = ActFactory::getIContact();
    $external_contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);
    if(!empty($external_contact) and    
       !empty($phone_number) and
       !empty($phone_type_id)) {	    	                  
        $i_contact->deleteExternalContactPhone($individual_id,
                                               $phone_number,
                                               $phone_type_id);                                               	   
	}
}
?>
<script language="javascript">
window.opener.document.location = window.opener.document.location;
window.close();
</script>
