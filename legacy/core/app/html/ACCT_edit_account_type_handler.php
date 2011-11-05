<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

$i_account = ActFactory::getIAccount();
$onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$onyx_account->company_sub_type_id = $sub_type_parm_id;
$contact_id = getRackSessionContactID();
$result =  $i_account->updateCompany( 
        $contact_id,                         
        $onyx_account );    

?>
<HTML>
<HEAD>
<!-- Refresh main account view -->
<script language="JavaScript">
try {
    o2 = opener.top.frames.workspace;
    opener.top.frames.workspace.location.href=opener.top.frames.workspace.location.href;
    opener = o2;
} catch(e) {
    // Best bet, try this.
    opener.location.href=opener.location.href;
}
window.close();
</script>
</HEAD>
<BODY>
</BODY>
</HTML>
