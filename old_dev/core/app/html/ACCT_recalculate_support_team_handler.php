<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

if( !isTeamLeader() ) {
        echo "You are not allowed, stop trying to hack CORE\n";
        exit;
}

/* This handler redoes the the support team and account executive
 */

checkDataOrExit( array( 'account_id' => "Account ID" ) );

#$print_sql = true;

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
$is_intensive = ($account->segment_id == INTENSIVE_SEGMENT);

?>
<HTML><HEAD>

<? if ( !$is_intensive ) { ?>

    <!-- Refresh calling view -->
    <SCRIPT LANGUAGE="JavaScript">
    <!--
    function close_it() { window.close(); }
    window.opener.location = window.opener.location;
    //-->
    </SCRIPT>
    </HEAD>
    <BODY onLoad="setTimeout(close_it,1)">
<? 
    $account->autoAssignSupportTeam("Manual Recalc", GetRackSessionContactID());    
   } 
   else {
?>
    </HEAD>
    <BODY>
    <TABLE class="blueman">
    <TR><TH class="blueman">Error!</TH></TR>
    <TR><TD class="blueman">
      <P>Intensive Accounts must have the team manually assigned!  Go to the Edit Account page to set it.</P>
    </TD></TR>
    </TABLE>
<? 
   } 
?>

<?php echo "Account#: $account_number\n" ?>
</BODY>
</HTML>
