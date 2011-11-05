<?php

require_once("CORE_app.php");
require_once("helpers.php");

checkDataOrExit( array( "account_number" => "Account Number",
                        "note_id" => "Note ID" ) );

if(!empty($delete)) {
    $i_account = ActFactory::getIAccount();
    $account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
    $account->deleteNote($note_id);        
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
if( !empty($delete) ):
?>
<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
<!--
window.opener.location = window.opener.location;
//-->
</SCRIPT>
<?php
endif;
?>
</HEAD>
<BODY onLoad="setTimeout(close_it,1)">
</BODY>
</HTML>

