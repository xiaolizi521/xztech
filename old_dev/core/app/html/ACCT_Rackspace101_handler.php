<?php

require_once('CORE_app.php');
require_once("act/ActFactory.php");

if( !empty($close) ) {
        echo '<html><head><title>closing...</title></head><body onLoad="setTimeout(window.close,1)">';
        echo "\n";
        echo '</body></html>';
        echo "\n";
        exit;
}

require_once("helpers.php");

checkDataOrExit( array( "account_id" => "Account ID",
                        "state" => "State" ) );

if( $state == "by-passed" and empty($text) ) {
        ForceReload($HTTP_REFERER . "&old_state=$state");
        exit;
}

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $account_id);

$rs =& $account->getRackspace101();
if( $state == "by-passed" ) {
        $rs->setText( $text );
}
$rs->add();

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