<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

if( !isTeamLeader() ) {
        echo "You need to be a Team Lead to assign an Account Manager.\n";
        exit;
}

/* This handler redoes the the support team and account executive
 */

checkDataOrExit( array( 'account_id' => "Account ID" ) );

#$print_sql = true;
$GLOBAL_db->BeginTransaction();

#get the account number
$query = 'SELECT
            "AccountNumber"
          FROM
            "ACCT_Account"
          WHERE
            "ID" = '.$account_id;
$account_number = $GLOBAL_db->getVal( $query );

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$account->assignAccountRole(ACCOUNT_ROLE_ACCOUNT_EXECUTIVE, "Manual Recalc");

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
<?php echo "Account#: $account_number\n" ?>
</BODY>
</HTML>
