<?php

require_once('CORE_app.php');

session_register("SESSION_account_role");
session_register("SESSION_account_id");
session_register("SESSION_no_step1");

$SESSION_account_id = $account_id;


if( empty($SESSION_account_role) ) {
    $account_role = 0;
} else {
    $account_role = $SESSION_account_role;
}

$user_cid = GetRackSessionContactID();

/*
 *  The rules:
 *     The user can pick among the roles that their team(s) have.
 *
 */

$result = $GLOBAL_db->SubmitQuery('
SELECT "ID", "Name"
FROM "ACCT_val_AccountRole"
WHERE "ACCT_val_AccountRoleTypeID" = '.ACCOUNT_ROLE_TYPE_EMPLOYEE.'

');

$special_account_roles = array(
	ACCOUNT_ROLE_ACCOUNT_EXECUTIVE,
	ACCOUNT_ROLE_ACCOUNT_COORDINATOR,
	ACCOUNT_ROLE_ACCOUNTS_RECEIVABLE,
	ACCOUNT_ROLE_BUSINESS_DEVELOPMENT,
	ACCOUNT_ROLE_VERTICAL_MARKETING
);

$roles = array();

for( $i=0; $i<$result->numRows(); $i++ ) {
    $row = $result->fetchArray($i);
    if ( !in_array($row['ID'], $special_account_roles)) {
        $roles[$row['ID']] = $row['Name'];
    }
}

$result->FreeResult();

?>
