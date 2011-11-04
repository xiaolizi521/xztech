<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("decouple/TestActFactory.php");

if( empty( $account_number ) ) {
        @$account_number += 0;
        print '<html><head><title>Enter Contact Number</title></head><body>';
        print "\n";
        print '<form action="' . $GLOBALS['REQUEST_URI'] . '">';
        print 'Please enter an account number<input name="account_number"';
        print 'value="' . $account_number . '">';
        print "<br>\n";
        print "<input type='submit' name='Submit'>\n";
        print "</form>\n";
        print '</body></html>';
        print "\n";
        exit;
}

$i_account = TestActFactory::getITestAccount();
$onyx_account = $i_account->getTESTAccountByAccountNumber($GLOBAL_db, $account_number,$id);
$contactid = getRackSessionContactID();
$i_account->updateCompany($contactid, $onyx_account);
$onyx_account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$companyTypeLookups = $i_account->getLookupValues('company.type');
$companyStatusLookups = $i_account->getLookupValues('company.status');
$companyMarketSectorLookups = $i_account->getLookupValues('company.marketsector');
$companySupportTeamsLookups = $i_account->getLookupValues('company.user12');
$segmentLookups = $i_account->getLookupValues('company.user9');

$segmentOptions = "";
for($i=0; $i<count($segmentLookups); $i++) {
        if ($onyx_account->segment_id == $segmentLookups[$i]->parameter_id) {
            $selected_flag = " selected ";
        } else {
            $selected_flag = "";
        }
        $segmentOptions .= '<OPTION value="'.$segmentLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$segmentLookups[$i]->desc.'</OPTION>';
}

$currentTeamId = "";
$companyTeamsOptions = "";
$selected_flag = "";
for($i=0; $i<count($companySupportTeamsLookups); $i++) {
        if ($onyx_account->support_team_id == $companySupportTeamsLookups[$i]->parameter_id) {
            $currentTeamId = $companySupportTeamsLookups[$i]->parameter_id;
            $selected_flag = " selected ";
        } else {
            $selected_flag = "";
        }
        $companyTeamsOptions .= '<OPTION value="'.$companySupportTeamsLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$companySupportTeamsLookups[$i]->desc.'</OPTION>';
}

$companyTypeOptions = "";
for($i=0; $i<count($companyTypeLookups); $i++) {    
    if ($onyx_account->company_type_id == $companyTypeLookups[$i]->parameter_id) {
        $selected_flag = " selected ";
    } else {
        $selected_flag = "";
    }
    
    //if this is not a former customer, then display all choices. if it is a former customer, only show the former customer choice, because onyx won't let
    //you change away from a former customer.    
    if($onyx_account->company_type_id != COMPANY_TYPE_FORMER_CUSTOMER or
      (($onyx_account->company_type_id == COMPANY_TYPE_FORMER_CUSTOMER and 
        $companyTypeLookups[$i]->parameter_id == COMPANY_TYPE_FORMER_CUSTOMER))) {        
        $companyTypeOptions .= '<OPTION value="'.$companyTypeLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$companyTypeLookups[$i]->desc.'</OPTION>';
    }    
}
$companyStatusOptions = "";
for($i=0; $i<count($companyStatusLookups); $i++) {
        if ($onyx_account->status_id == $companyStatusLookups[$i]->parameter_id) {
            $selected_flag = " selected ";
        } else {
            $selected_flag = "";
        }
        $companyStatusOptions .= '<OPTION value="'.$companyStatusLookups[$i]->parameter_id.'"'.$selected_flag.'>'.$companyStatusLookups[$i]->desc.'</OPTION>';
}
$marketSectorOptions = $onyx_account->getBusinessTypeSelectOptions();

makevar('account_name', $onyx_account->account_name);

makevar( 'account_status', $onyx_account->status_id);

if( in_dept("AR") ) {
    $editable = true;
} else {
    $editable = false;
}

makevar( 'contact_lookup_id', '' );

$sla_type = $onyx_account->getSLAType();
$sla_name = $sla_type->getName();
$sla_options = $sla_type->getSelectOptions();

makevar( 'business_type', $onyx_account->market_sector_id);

$hidden_tags  = '<input type="hidden" name="account_number"' .
                ' value="'. $account_number . '">';
$hidden_tags .= '<input type="hidden" name="current_team"' .
                ' value="'. $currentTeamId . '">';
$hidden_tags .= '<input type="hidden" name="crm_company_id"' .
                ' value="'. $onyx_account->crm_company_id . '">';                
?>
