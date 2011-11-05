<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");
require_once("act/IAccount.php");
require_once("act/IContact.php");

$chkdict = array('account_number' => "An Account Number (Something's gone wrong!)",
                 'external_contact_primary_id' => "A Contact ID (Something's gone wrong!)",
                 'individual_id' => "Individual Id (Something's gone very wrong!)");

checkDataOrExit( $chkdict );

$i_account = ActFactory::getIAccount();
$i_contact = ActFactory::getIContact();
$onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);
$contact_name = $contact->individual->getFullName();
$role_name = $contact->getRoleName();

if(isset($DELETE_x) and ($onyx_account != null) and ($onyx_account->account_number != "")) {       
    $i_contact->deleteExternalContact($external_contact_primary_id);
    $note = new ACFR_Note();
    $note->noteText = "Deleted $contact_name as role $role_name.";   
    $onyx_account->addNote($note);
}

$tree_url = "$py_app_prefix/account/tree.pt?account_number=$account_number&";

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
