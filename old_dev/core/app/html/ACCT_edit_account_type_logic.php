<?php

require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

if( ! ( in_dept("AR|SALES|ACCOUNT_EXECUTIVE|SUPPORT_SUPERVISOR") or isTeamLeader() ) ) {
    errorPage("You do not have permission to edit account types");
    exit;
}

$i_account = ActFactory::getIAccount();
$companySubTypes = $i_account->getLookupValues('company.subtype');
$companySubTypesOptions = "";
for($i=0; $i<count($companySubTypes); $i++) {
	$companySubTypesOptions .= '<OPTION value="'.$companySubTypes[$i]->parameter_id.'">'.$companySubTypes[$i]->desc.'</OPTION>';
}

$hidden_tags  = '<input type="hidden" name="account_number"' .
                ' value="'. $account_number . '">';

?>
