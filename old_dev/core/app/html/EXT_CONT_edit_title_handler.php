<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");

$i_account = ActFActory::getIAccount();
$i_contact = ActFActory::getIContact();

if(empty($cancel) and 
   !empty($save)) {        
    if(empty($title_name)) {
        ForceReload( $HTTP_REFERER );
        exit;
    }
            
    $i_contact->updateExternalContactTitle(getRackSessionContactID(), $SESSION_individual_id, $title_name);
}


?>
<HTML>
<HEAD>
<SCRIPT LANGUAGE="JavaScript">
<!-- 
function close_it() { window.close(); } 
//-->
</SCRIPT>
<?php
if( empty( $cancel ) ) {
?>
<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
<!--
window.opener.location = window.opener.location;
//-->
</SCRIPT>
<?php
}
?>
</HEAD> 
<BODY onLoad="setTimeout(close_it,1)">
</BODY>
</HTML>
