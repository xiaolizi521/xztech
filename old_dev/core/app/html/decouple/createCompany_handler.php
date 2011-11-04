<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("decouple/TestActFactory.php");
require_once("menus.php");

$i_account = TestActFactory::getITestAccount();

/* ============================================================== */
/* This series of assignments are strange.  The only thing I could*/
/* think of is that the assignments are made because this routine */
/* was to be used more like a function.                           */
/* ============================================================== */

$contact_id = getRackSessionContactID();
$crm_company_id = $crm_company_id; 
$slaType = $sla_type_value;
$marketSector = $marketSector;
$supportTeam = $support_team;
$companyType =  $companyType;
$status = $companyStatus;
$currentTeamId = $current_team;

checkDataOrExit( array( 'account_name' => "Account Name",
                  'account_number' => array( "Account Number",
                                             true,
                                             array('data_required',
                                                   'data_number' )
                          ) ) );
$name = stripslashes( $account_name );
#$i_account = ActFactory::getIAccount();
$onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);

/* ============================================================== */
/* Detect changes in Team from the original value.                */
/* ============================================================== */

if($currentTeamId != $supportTeam) {
/* -------------------------------------------------------------- */
/* Update the account team in Oynx                                */
/* -------------------------------------------------------------- */
    $query = '
        select
            "ID"
        from
            "ACCT_Team"
        where
            "crm_team_id" = ' . $supportTeam;    
    
    $result = $GLOBAL_db->SubmitQuery($query);    
    $team_id = $result->getResult(0, 0);
               
    $onyx_account->setSupportTeam($team_id, "Manually modified", $contact_id);    
/* -------------------------------------------------------------- */
/* IFF we have no account executive, we assign one manually       */
/* -------------------------------------------------------------- */
    $account_executive = $onyx_account-> getAccountExecutive();
    if ( empty( $account_executive )) {
        $onyx_account->assignAccountRole(ACCOUNT_ROLE_ACCOUNT_EXECUTIVE, "Manual Recalc");
    }

/* -------------------------------------------------------------- */
/* IFF we have no BDC, we assign one manually                     */
/* -------------------------------------------------------------- */
    $BDC = $onyx_account->getBusinessDevelopmentConsultant();
    if ( empty( $BDC )) {
        $onyx_account->assignAccountRole(ACCOUNT_ROLE_BUSINESS_DEVELOPMENT, "Manual Recalc");
    }

}

/* ============================================================== */
/* Update the rest of the onyx account values                     */
/* ============================================================== */
$onyx_account->account_name = $name;
$onyx_account->market_sector_id = $marketSector;
$onyx_account->company_type_id = $companyType;
$onyx_account->status_id = $status;
$onyx_account->setSLAType( $slaType );
$onyx_account->segment_id = $segment;
$result =  $i_account->updateCompany(
        $contact_id,
        $onyx_account );


/* ============================================================== */
/* Check for any onyx errors                                      */
/* ============================================================== */
$error = '';
if ( isset( $result['updateCompanyFieldsReturn']['errorCode'] ) && !empty($result['updateCompanyFieldsReturn']['errorCode'] ) ) {
    $error = $result['updateCompanyFieldsReturn']['errorText'];
}

?>
<HTML>
<HEAD>
<script language="JavaScript">
    function loadAccountWindow() {
        window.location = '/py/account/view.pt?account_number=<?=$account_number ?>';
    }
</script>
    <LINK HREF="/css/core_ui.css"
          REL="stylesheet">
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
</HEAD>
<BODY>
    <? if ( $error ) { ?>
        <p> An error occured while updating values.  The error message is: <BR><BR><b>
            <?= $error ?>
            </b><BR><BR>.  Please report this error to the CORE Team.  Your assistance is appreciated.  </p>
            <BR> Click the Continue button to load the the account page.
    <? } else { ?>
        <p> You successfully edited the account fields.  Click the Continue button to load the account page. </p><BR>
    <? } ?>
    <form action="">
    <p align="left"><input type="button" value="Continue" class="form_button" onClick="loadAccountWindow();"></p>
    </form>
</BODY>
</HTML>
