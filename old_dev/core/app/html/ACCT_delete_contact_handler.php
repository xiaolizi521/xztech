<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

$chkdict = array('account_number' =>
                  "An Account Number (Something's gone wrong!)",
                  'contact_id' =>
                  "A Contact ID (Something's gone wrong!)",
                  'role_id' =>
                  "A Role ID (Something's gone wrong!)");

checkDataOrExit($chkdict);

//error_reporting(0);

$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();

$acfr_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$acfr_contact = $i_contact->getInternalContact($GLOBAL_db, $contact_id, $acfr_account->account_id);
if($acfr_account) {
    $acfr_account->removeInternalContact($acfr_contact->contact_id, $role_id, $acfr_contact->getFullName());         
}

$tree_url = "$py_app_prefix/account/tree.pt?" .
"account_number=$account_number&";

?>
<HTML>
<HEAD>
<!-- Refresh main account view -->
<script language="JavaScript">
    function close_it() { window.close(); }
  	window.opener.parent.frames[0].location = '<?=$tree_url?>';
   	window.opener.parent.frames[1].location = '/py/account/view.pt?account_number=<?=$account_number ?>';
</script>
</HEAD>
<BODY onLoad="setTimeout(close_it, 1)">
</BODY>
</HTML>
