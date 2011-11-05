<?php

require_once("CORE_app.php");
require_once( "reporter.php" );
require_once("act/ACFR_CoreContact.php");

if(empty($contact_id)) {
        trigger_error("You must include a <i>contact_id</i> to view this page", FATAL);
}

$contact = new ACFR_CoreContact();
$contact->load($GLOBAL_db, $contact_id);

$account_rows = "<TR class='reporter'>";
$account_rows .= "<TH class='reporter'>Acct #</TH>";
$account_rows .= "<TH class='reporter'>Name</TH>";
$account_rows .= "<TH class='reporter'>Contact</TH>";
$account_rows .= "<TH class='reporter'>State</TH>";
$account_rows .= "<TH class='reporter'>Country</TH>";
$account_rows .= "<TH class='reporter'>Business</TH>";
$account_rows .= "<TH class='reporter'>Role</TH>";
$account_rows .= "<TH class='reporter'>Status</TH>";
$account_rows .= "</TR>";

if (empty($offset)) {
    $offset = 0;
}

$page_size = 5;

$accounts = $contact->getAllAccounts($GLOBAL_db, $page_size, $offset);

$rowNum = 1;
foreach($accounts as $account) {
    if($rowNum % 2) {
        $class_string = "class='reporterodd'";
    }
    else {
        $class_string = "class='reporter'";
    }
    
    $account_rows .= "<TR " . $class_string . ">";
    
    //account number
    $account_rows .= "<TD " . $class_string . ">";
    $account_rows .= "<a href=\"ACCT_main_workspace_page.php?account_number=" . $account->account_number . "\" target=\"_top\">" . $account->account_number . "</a>";
    $account_rows .= "</TD>";
    
    //account name
    $account_rows .= "<TD " . $class_string . ">";
    $account_rows .= $account->account_name;
    $account_rows .= "</TD>";
    
    //contact name
    $account_rows .= "<TD " . $class_string . ">";
    $account_rows .= $contact->getFullName();
    $account_rows .= "</TD>";    
    
    //state
    $primaryContact = $account->getPrimaryContact();
    if(!empty($primaryContact)) {
        $primaryAddress = $primaryContact->individual->getPrimaryAddress();
        $account_rows .= "<TD " . $class_string . ">";
        $account_rows .= $primaryAddress->regionCode;
        $account_rows .= "</TD>";
        
        //country
        $account_rows .= "<TD " . $class_string . ">";
        $account_rows .= $primaryAddress->countryCode;
        $account_rows .= "</TD>";
    }
    else {
        $account_rows .= "<TD " . $class_string . ">";
        $account_rows .= "</TD>";
        
        //country
        $account_rows .= "<TD " . $class_string . ">";
        $account_rows .= "</TD>";
    }
        
    //business type
    $account_rows .= "<TD " . $class_string . ">";
    $account_rows .= $account->getBusinessTypeName();
    $account_rows .= "</TD>";
    
    //role contact plays for account
    $contactOnAccount = new ACFR_CoreContact();
    $contactOnAccount->load($GLOBAL_db, $contact_id, $account->account_id);
    $accountRoles = "";
    foreach($contactOnAccount->account_roles as $role) {        
        $accountRoles .= $role->account_role_name . "<br>";
    }
    $account_rows .= "<TD " . $class_string . ">";
    $account_rows .= $accountRoles;
    $account_rows .= "</TD>";
    
    //account status
    $account_rows .= "<TD " . $class_string . ">";
    $account_rows .= $account->status_name;
    $account_rows .= "</TD>";
        
    $account_rows .= "</TR>";
}

?>
