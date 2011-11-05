<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

checkDataOrExit( array( "account_number" => "Account Number" ) );

if( empty( $note_text ) ) {
        ForceReload($HTTP_REFERER);
        exit;
}

$user = new CONT_Contact();
$user->loadID(getRackSessionContactID());

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);

$GLOBAL_db->BeginTransaction();

$note = new ACFR_Note();
$note->noteText = nl2br($note_text);
$note->privateFlag = false;
$note->userComment = true;
$account->addNote($note);

$GLOBAL_db->CommitTransaction();
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

