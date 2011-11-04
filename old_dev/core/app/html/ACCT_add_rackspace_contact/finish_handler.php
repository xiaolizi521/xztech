<?php
require_once("CORE_app.php");
require_once("act/ActFactory.php");

session_register("SESSION_account_id");
session_register("SESSION_account_role");
session_register("SESSION_contact_id");

if( !empty($back) ) {
    ForceReload("step2_page.php");
    exit();
}

/* We're finished! 
 * Do the add.
 */
session_register("SESSION_account_id");
session_register("SESSION_account_role");
session_register("SESSION_contact_id");

$account_id = $SESSION_account_id;
$account_role = $SESSION_account_role;
$contact_id = $SESSION_contact_id;

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $account_id);
$acct_name = $account->account_name;
$acct_number = $account->account_number;

// Add the contact
$account->addInternalContact($contact_id, $account_role);

?>
<HTML>
<HEAD>
<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
    opener.top.location.href= '/ACCT_main_workspace_page.php?account_number=<?=$acct_number?>';
    window.close();
</SCRIPT>
</HEAD>
<BODY>
</BODY>
</HTML>
