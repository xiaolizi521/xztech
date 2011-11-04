<?php

require_once('CORE_app.php');
require_once("act/ActFactory.php");

session_register("SESSION_account_id");
session_register("SESSION_account_role");
session_register("SESSION_contact_id");
session_register("SESSION_show_all_toggle");
session_register("SESSION_no_step1");

if( empty($show_all) and
    !empty($SESSION_show_all_toggle) ) {
    $show_all = $SESSION_show_all_toggle;
} else {
    $SESSION_show_all_toggle = !empty($show_all);
}

$account_id = $SESSION_account_id;

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountId($GLOBAL_db, $account_id);

if( empty($SESSION_contact_id) ) {
    $contact_id = 0;
} else {
    $contact_id = $SESSION_contact_id;
}

$account_role = $SESSION_account_role;
$ar = new ACCT_AccountRole;
$ar->loadID( $account_role );
$account_role_name = $ar->getName();

$team_name = $account->getSupportTeamName();

$teams = array();
if(empty($show_all)) {   
    $result = $GLOBAL_db->SubmitQuery('
SELECT "ID"
FROM "ACCT_Team"
WHERE "crm_team_id" = ' . $account->support_team_id);

    //there should only be 1, but just in case we do something weird with the mapping in the future
    for($i=0; $i<$result->numRows(); $i++) {
        $teams[] = $result->getCell($i,0);
    }
    $result->FreeResult();   
} else {
    $result = $GLOBAL_db->SubmitQuery('
SELECT "ID"
FROM "ACCT_Team"
');

    for( $i=0; $i<$result->numRows(); $i++ ) {
        $teams[] = $result->getCell($i,0);
    }
    $result->FreeResult();
}

$result = $GLOBAL_db->SubmitQuery('
SELECT "CONT_Contact"."ID", "FirstName" || \' \' || "LastName" as name, "employee_authorization".userid
FROM "ACCT_xref_Team_Contact_TeamMemberRole"
JOIN "CONT_Contact" on ("CONT_ContactID" = "CONT_Contact"."ID") 
JOIN "CONT_Person" on ("CONT_PersonID" = "CONT_Person"."ID")
JOIN "xref_employee_number_Contact" ON "xref_employee_number_Contact"."CONT_ContactID" = "CONT_Contact"."ID"
JOIN "employee_authorization" ON "xref_employee_number_Contact"."employee_number" = "employee_authorization"."employee_number"
WHERE "ACCT_xref_Team_Contact_TeamMemberRole"."ACCT_TeamID" in ('.join(",",$teams).')
EXCEPT
SELECT "CONT_Contact"."ID", "FirstName" || \' \' || "LastName" as name, "employee_authorization".userid
FROM "ACCT_xref_Account_Contact_AccountRole"
JOIN "CONT_Contact" on ("CONT_ContactID" = "CONT_Contact"."ID") 
JOIN "CONT_Person" on ("CONT_PersonID" = "CONT_Person"."ID")
JOIN "xref_employee_number_Contact" ON "xref_employee_number_Contact"."CONT_ContactID" = "CONT_Contact"."ID"
JOIN "employee_authorization" ON "xref_employee_number_Contact"."employee_number" = "employee_authorization"."employee_number"
WHERE "ACCT_val_AccountRoleID" = ' . $account_role . '
AND "ACCT_xref_Account_Contact_AccountRole"."ACCT_AccountID" = ' . $account_id . ' 
ORDER BY name
');

// get a list of userids from the contact query
$userid_list = array();
for($i = 0; $i < $result->numRows(); $i++) {
	$userid_list[] = $result->getCell($i, 2);
}



$i_contact = ActFactory::getIContact();

$people = array();
for($i = 0; $i < $result->numRows(); $i++) {
	// only append this if the employee's userid is in the userid_list
	if(in_array($result->getCell($i, 2), $userid_list)) {
    	$people[$result->getCell($i,0)] = $result->getCell($i,1);
    	$last_cid = $result->getCell($i,0);
	}
}

// If there is one choice, we should make it simple
// Even if this is pointless.
if(count($people) == 1) {
    $contact_id = $last_cid;
}

$result->FreeResult();
?>
